<?php
session_start();
ob_start();
include_once 'function_lib.inc';
expireSession();
if(isset($_SESSION) && !isset($_SESSION['username']) && !isset($_SESSION['registered']))
{
    header("Location: index.php?m=timeout");
}
else
{
    if(isset($_SESSION['username']))
    {
        $userInfo=getuserInfo($_SESSION['username']);
        $display_name=$_SESSION['username'];
        if($userInfo[0]['Ban']==1)
        {
            @header('Location: index.php?m=banned');//redirect to test
        }
    }
    if(isset($_GET['bucketId']) && !empty($_GET['bucketId']))
    {
        $bucketInfo=getBucketInfo($_GET['bucketId']);
        $professorId=$bucketInfo[0]['Owner'];
        $userInfoBucket=getuserInfoById($professorId);
    }
    $lastcommentId=isset($_GET['last']) && trim($_GET['last'])!=""? $_GET['last']:1;
    //get list of colleges
    if(1==$_SESSION['registered'])
    {
        $documentId=isset($_GET) && isset($_GET['documentId'])?$_GET['documentId']:0;
        $userId=isset($_SESSION) && isset($_SESSION['userId'])?$_SESSION['userId']:0;
        //
        $collegeId=isset($_GET) && isset($_GET['collegeId'])?$_GET['collegeId']:0;
        $professorId=isset($_GET) && isset($_GET['professorId'])?$_GET['professorId']:0;
        $bucketId=isset($_GET) && isset($_GET['bucketId'])?$_GET['bucketId']:0;
        $subjectId=isset($_GET) && isset($_GET['subjectId'])?$_GET['subjectId']:0;
        $roleId=isset($_SESSION) && isset($_SESSION['roleId'])?$_SESSION['roleId']:0;
        $q=isset($_GET) && isset($_GET['q'])?$_GET['q']:"";
        $sort=isset($_GET) && isset($_GET['sort'])?$_GET['sort']:0;
        if($collegeId==0)
        {
            $collegeId=$userInfoBucket[0]['CollegeId'];   
        }
        if($professorId==0)
        {
            $professorId=$userInfoBucket[0]['UserId'];
        }
        //
        $comments=getCommentsByDocumentId($documentId,$lastcommentId);
        $count_reponse=getCommentsCountByDocumentId($documentId);
        $totalMax=$count_reponse[0]['max'];
        $totalMin=$count_reponse[0]['min'];
        $total=$count_reponse[0]['count'];
    }
    $currentPage=$lastcommentId;
    $totalPages=$total;
    print_r($_SESSION);
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">

    <title>Comment listing</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap-3.3.6-dist/css/bootstrap.min.css" rel="stylesheet">
  </head>

  <body>
 
    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="dash.php">SRIS</a>
        </div>
        <div id="navbar" class="collapse navbar-collapse">
          <ul class="nav navbar-nav">
            <li><a href="dash.php">Home</a></li>
            <?php
            if($roleId!=1)
            echo '<li><a href="admin.php">Admin</a></li>';
            ?>
          </ul>
          <div class="navbar-collapse collapse">
            <ul class="nav navbar-nav navbar-right">
              <li><a class="glyphicon glyphicon-user" style="color: white;"> <?php echo $display_name;?></a></li>
              <li><a class="glyphicon glyphicon-off" style="color: white;" href="#" onclick="logout();" id="logout"> Logout</a></li>
            </ul>
          </div>
        </div><!--/.nav-collapse -->
      </div>
    </nav>

    <div class="container">
    <br/>
    <br/>
    <br/>
      <h3 class="glyphicon glyphicon-comment"> Comments <span class="badge"><?php echo $total?></span></h3>
      <ol class="breadcrumb">
        <li><a href="dash.php">Home</a></li>
        <li><a href='<?php echo "professors.php?collegeId=$collegeId";?>'>Professors</a></li>
        <li><a href='<?php echo "buckets.php?professorId=$professorId&collegeId=$collegeId";?>' class="active">Buckets</a></li>
        <li><a href='<?php echo "documents.php?professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId&q=$q&sort=$sort";?>'>Documents</a></li>
        <li><a href='<?php echo "comments.php?professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId&q=$q&sort=$sort&documentId=$documentId";?>' class="active">comments</a></li>
      </ol>
      <div class="starter-template">
       <button type="button" class="btn btn-success" style="float: right;margin-right: 3em;width: 12em;" id="postComment" onclick="opencommentdialog(this);">Post Comment</button>
      </div>
       <br/>
       <br/>
       <div>
            <ol>
                <?php
                $actuallastcommentid=0;
                $minlastcommentid=$comments[0]['commentId'];
                if(!empty($comments))
                {
                    echo "<div id='commentsDb' class='bs-docs-section'>";
                    foreach($comments as $comment)
                    {
                        //[id] => 13 [bucketid] => 1 [collegeid] => 1 [userid] => 7 [title] => parallel programming basic [description] => java program to make game using tcp and server
                        //[url] => sris71parallelsystemsmaterial/1451165988376HelloServer(1).java [rating] => 0 [hide] => 0
                        echo "<div id='comment".$comment['commentId']."' style='border-left-color: #d9534f;padding: 20px;margin: 20px 0;border: 1px solid #eee;
                        border-left-width: 5px;border-radius: 3px;'>";
                        
                        if($comment['Username']==$_SESSION['username'] || (isset($roleId) && $roleId!=1))
                        echo '<button type="button" class="btn btn-default btn-lg" onclick="removeComment(this)" name="'.$comment['commentId'].'" style="float:right;margin-right:4px;">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Remove
                        </button>';
                        
                        echo '<button type="button" class="btn btn-default btn-lg" onclick="report(this)" name="'.$comment['commentId'].'" style="float:right;margin-right:4px;">
                        <span class="glyphicon glyphicon-flag" aria-hidden="true"></span> Report
                        </button>';
                        echo "<h4 style='color: #d9534f'>".$comment["Comment"]."</h4>";
                        echo "<p>Posted By : <span class='label label-warning'>".$comment["Username"]."</span></p>";
                        echo "<p>Date Posted: <span class='label label-default'>".$comment["Date"]."</span></p>";
                        $actuallastcommentid=$comment['commentId'];
                        echo "</div>";
                    }
                    echo "</div>";
                }
                ?>
           </ol>
       </div>
       <?php
            if(empty($comments))
            {
                echo "<div class=\"panel panel-warning\"><div class=\"panel-heading\"><h2 class=\"panel-title\">No results</h2></div> No comments go back</div>";
            }
            ?>
       <?php paginationCommentPage($actuallastcommentid,$documentId,$totalMax,$totalMin,$minlastcommentid,$professorId,$bucketId,$subjectId,$collegeId,$q,$sort);?>
    </div><!-- /.container -->
    
    
    
    <!-- Modal  post comment required-->
    <div id="postcommentmodal" class="modal fade" role="dialog" tabindex="-1" aria-labelledby="myModalLabel" aria-hidden="true">
      <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Post Comment</h4>
          </div>
          <div class="modal-body">
            <textarea class="form-control" rows="5" id="postCommentDb" name="comment" placeholder="enter comment" required></textarea>
          </div>
          <div class="modal-footer">
             <button id="postCommentButton" type="button" class="btn btn-primary">post document</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
    
      </div>
    </div>
    
    <!-- Modal  validation modal required-->
    <div id="validationmodal" class="modal fade" role="dialog">
      <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="validationtitle"></h4>
          </div>
          <div class="modal-body">
            <p id="validationmsg"></p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
    
      </div>
    </div>
    
    <!---  progress bar modal-->
    <div class="modal fade" id="pleaseWaitDialog" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h1>Processing...</h1>
                </div>
                <div class="modal-body">
                    <div class="progress progress-striped active">
                        <div class="progress-bar" role="progressbar" style="width: 100%;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
   <!-- Modal  logout required-->
    <div id="logoutmodal" class="modal fade" role="dialog">
      <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title">Logout Confirmation</h4>
          </div>
          <div class="modal-body">
            <p id="validationmsg">Sure you want to logout?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" onclick="window.location.href = 'index.php?m=logout'">logout</button>
          </div>
        </div>
    
      </div>
    </div>

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
    <script type="text/javascript">
     var documentId=<?php echo $documentId?>;
     var commentId=0;
     var userId=<?php echo $userId?>;
     function logout()
      {
        $('#logoutmodal').modal('show');
      }
    function opencommentdialog(a){
                $('#postcommentmodal').modal('show');
            }
    function report(a)
        {
            commentId=$(a).attr("name");
            $(a).prop("disabled",true);
            $('#pleaseWaitDialog').modal('show');
            $.post( "http://localhost:8088/simple-service-webapp/api/users/comment/reportComment",
            JSON.stringify({ reporter: userId, commentid: commentId}),"json")
            .done(function( data ) {
             if(data[0].msg!="you are banned")
             {
                $('#validationtitle').text("Attention comment is reported");
                $('#validationmsg').text("Comment reported");
                $('#validationmodal').modal("show");
                $('#pleaseWaitDialog').modal('hide');
             }
             else
             {
                $('#validationtitle').text("Attention cant perform action");
                $('#validationmsg').text(" You are banned or error occured");
                $('#validationmodal').modal("show");
                $('#pleaseWaitDialog').modal('hide');
             }
         });
            
        }
        
        function removeComment(a)
        {
            commentId=$(a).attr("name");
            $(a).prop("disabled",true);
            $('#pleaseWaitDialog').modal('show');
            $.post( "http://localhost:8088/simple-service-webapp/api/users/comment/hideComment",
            JSON.stringify({ userid: userId, commentid: commentId}),"json")
            .done(function( data ) {
             if(data[0].msg!="you are banned")
             {
                $('#validationtitle').text("Attention document is removed");
                $('#validationmsg').text("Document removed reload page");
                $('#validationmodal').modal("show");
                $('#comment'+commentId).remove()
                $('#pleaseWaitDialog').modal('hide');
             }
             else
             {
                $('#validationtitle').text("Attention cant perform action");
                $('#validationmsg').text(" You are banned or error occured");
                $('#validationmodal').modal("show");
                $('#pleaseWaitDialog').modal('hide');
             }
         });
            
        }
    $(document).ready(function () {
        //post comment code
        
        $('#postCommentButton').click(function(){
                    var validationcheck2=true;
                    if(0==$('#postCommentDb').val().trim().length||""==$('#postCommentDb').val())
                    {
                        $('#validationtitle').text("Attention comment is required");
                        $('#validationmsg').text(" You need to fill in comment section");
                        $('#validationmodal').modal("show");
                        validationcheck2=false;
                    }
                    if(validationcheck2)
                    {
                        var comment=$('#postCommentDb').val();
                        if(comment.trim().length>0 && comment!="" )
                        {
                            $('#pleaseWaitDialog').modal('show');
                            $.post( "http://localhost:8088/simple-service-webapp/api/users/document/postComment",
                               JSON.stringify({ userid: userId, documentid: documentId,comment:comment }),"json")
                            .done(function( data ) {
                                if(data[0].msg!="you are banned")
                                {
                                   $('#validationtitle').text("Attention comment is posted");
                                   $('#validationmsg').text(" Comment has been posted");
                                   $('#postcommentmodal').modal('hide');
                                   $('#validationmodal').modal("show");
                                   $('#postCommentDb').val("");
                                   $('#pleaseWaitDialog').modal('hide');
                                   location.reload();
                                }
                                else
                                {
                                   $('#validationtitle').text("Attention comment not posted");
                                   $('#validationmsg').text(" You are banned or error occured");
                                   $('#postcommentmodal').modal('hide');
                                   $('#validationmodal').modal("show");
                                   $('#pleaseWaitDialog').modal('hide');
                                   $('#postCommentDb').val("");
                                }
                            });
                        }
                        else
                        {
                            $('#validationtitle').text("Attention you need to have comment");
                            $('#validationmsg').text("please enter comment");
                            $('#validationmodal').modal("show");
                        }
                    }
                });
        //end of post comment code
        $('#postCommentDb').val("");
        
        //hide comment document code
    });
    </script>
  </body>
</html>
