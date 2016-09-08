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
    
    //getting user id from bucket
    if(isset($_GET['bucketId']) && !empty($_GET['bucketId']))
    {
        $bucketInfo=getBucketInfo($_GET['bucketId']);
        $professorId=$bucketInfo[0]['Owner'];
        $userInfoBucket=getuserInfoById($professorId);
    }
    
    if(isset($_POST) && !empty($_POST) && isset($_POST['bucketid']) && isset($_POST['uniqid']) AND $_POST['uniqid'] != $_SESSION['uniqid'])
    {
        $filename = $_FILES['file']['name'];
        $filedata = $_FILES['file']['tmp_name'];
        $filesize = $_FILES['file']['size'];
        $bucketid=$_POST['bucketid'];
        $subjectid=$_POST['subjectid'];
        $userid=$_POST['userid'];
        $professorid=$_POST['professorid'];
        $collegeid=$_POST['collegeid'];
        $title=$_POST['title'];
        $desc=$_POST['desc'];
        $allowed =  array('pptx','ppt' ,'txt','docx','java','cpp','py','php','pdf','csv','xls');
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $error=0;
        
        if(!in_array($ext,$allowed)  || 50000000<$filesize) {
            $error=1;
        }
        if ($filedata != '')
        {
            $_SESSION['uniqid'] = $_POST['uniqid'];
            $headers = array("Content-Type:multipart/form-data"); // cURL headers for file uploading
            $postfields = array("filedata" => "@$filedata", "filename" => $filename ,"bucketid"=>$bucketid,"subjectid"=>$subjectid,
                                "userid"=>$userid,"collegeid"=>$collegeid,"professorid"=>$professorid,"title"=>$title,"desc"=>$desc);
            $ch = curl_init();
            $options = array(
                CURLOPT_URL => "http://localhost:8088/simple-service-webapp/api/users/postDocument",
                CURLOPT_HEADER => false,
                CURLOPT_POST => 1,
                CURLOPT_HTTPHEADER => $headers,
                CURLOPT_POSTFIELDS => $postfields,
                CURLOPT_INFILESIZE => $filesize,
                CURLOPT_RETURNTRANSFER => true
            ); // cURL options
            curl_setopt_array($ch, $options);
            $response=curl_exec($ch);
            $response_json=json_decode($response,true);
            curl_close($ch);
            unset($_POST);
            if($response_json[0]['msg']=="posted")
            {
                
            }
            else
            {
                $error=1;
                //go to error page
            }
        }
    }
   
    // pagination variables
    $start=(isset($_GET['start']) && !empty($_GET['start']) && $_GET['start']>=1) ?$_GET['start']:1;
    $sort=(isset($_GET['sort']) && ($_GET['sort']>=0))?$_GET['sort']:1;
    $q="";
    if(isset($q) && empty($q))
    $q=isset($_POST['q'])?$_POST['q']:"";
    if(isset($q) && empty($q))
    $q=isset($_GET['q'])?$_GET['q']:"";
    
    //get list of colleges
    if(1==$_SESSION['registered'])
    {
        $collegeId=isset($_GET) && isset($_GET['collegeId'])?$_GET['collegeId']:0;
        
        $professorId=isset($_GET) && isset($_GET['professorId'])?$_GET['professorId']:0;
        if($collegeId==0)
        {
            $collegeId=$userInfoBucket[0]['CollegeId'];   
        }
        if($professorId==0)
        {
            $professorId=$userInfoBucket[0]['UserId'];
        }
        $bucketId=isset($_GET) && isset($_GET['bucketId'])?$_GET['bucketId']:0;
        $subjectId=isset($_GET) && isset($_GET['subjectId'])?$_GET['subjectId']:0;
        $roleId=isset($_SESSION) && isset($_SESSION['roleId'])?$_SESSION['roleId']:0;
        $userId=isset($_SESSION) && isset($_SESSION['userId'])?$_SESSION['userId']:0;
    }
    $documents_response=getDocumentsByBucketId($bucketId,urlencode($q),$start,$sort);
    $documents=json_decode($documents_response['response'],true);
    $bucketname=$documents[0]['bucketname'];
    $total_documents=$documents_response['numFound'];
    $total_pages = ceil($total_documents / 10);
    /*
     *if($start!=0)
    {
        $start=$start/10;
        $start=floor($start);
        if($start>$total_pages)
        {
            $start=$total_pages-1;
        }
        $start=$start."0";
    }
    */
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

    <title>Professor listing</title>

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
          <form class="navbar-form navbar-right " method="GET" id="searchform" action='documents.php'>
            <input type="text" class="form-control" placeholder="Search..." id="qvalue" name="q" value="<?php echo $q;?>">
            <input type="hidden" name="start" value="<?php echo $start;?>">
            <input type="hidden" name="professorId" value="<?php echo $professorId;?>">
            <input type="hidden" name="bucketId" value="<?php echo $bucketId;?>">
            <input type="hidden" name="subjectId" value="<?php echo $subjectId;?>">
            <input type="hidden" name="collegeId" value="<?php echo $collegeId;?>">
            <input type="hidden" name="sort" value="<?php echo $sort;?>">
            <button type="submit" class="btn btn-default" aria-label="Left Align" id="searchbutton"><span class="glyphicon glyphicon-search" aria-hidden="true"></span></button>
          </form>
          
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
    <?php
    if(isset($error) && $error)
    {
        echo "<div class=\"alert alert-danger\" role=\"alert\">
                <span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
                <span class=\"sr-only\">Error File not uploaded:</span>
                File size should be less that 50MB and Formats allowed
                'pptx','ppt' ,'txt','docx','java','cpp','py','php','pdf','csv','xls'
                </div>";
    }
    else
    {
        if(isset($error) && $error==0)
        {
            echo "<div class=\"alert alert-success\" role=\"success\">
                    <span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
                    <span style=\"font-size:1.3em\">File successfully uploaded</span>
                    </div>";
        }
    }
    ?>
    <br/>
      <h1 class="glyphicon glyphicon-list-alt"> <?php echo $bucketname;?> Documents <span class="badge" id="totaldocs"><?php echo $total_documents?></span></h1>
      <ol class="breadcrumb">
        <li><a href="dash.php">Home</a></li>
        <li><a href='<?php echo "professors.php?collegeId=$collegeId";?>'>Professors</a></li>
        <li><a href='<?php echo "buckets.php?professorId=$professorId&collegeId=$collegeId";?>' class="active">Buckets</a></li>
        <li><a href='<?php echo "documents.php?professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId&q=$q&sort=$sort";?>' class="active">Documents</a></li>
      </ol>
      <div class="starter-template">
        <?php paginationDocumentPage($start,$total_pages,$bucketId,$professorId,$subjectId,$collegeId,$sort,$q);?>
       <button type="button" class="btn btn-success" style="float: right;margin-right: 3em;width: 12em;" id="postDoc">Post Document</button>
      </div>
       <br/>
       <br/>
                <!-- Split button -->
         <div class="btn-group">
           <button type="button" class="btn btn-danger">Sort</button>
           <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <span class="caret"></span>
             <span class="sr-only">Toggle Dropdown</span>
           </button>
           <ul class="dropdown-menu">
             <li><a href='<?php echo "documents.php?professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId&q=$q&sort=2";?>'>Rating</a></li>
             <li><a href='<?php echo "documents.php?professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId&q=$q&sort=1";?>'>Recent</a></li>
             <li><a href='<?php echo "documents.php?professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId&q=$q&sort=0";?>'>Old</a></li>
             <li><a href='<?php echo "documents.php?professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId&q=$q&sort=3";?>'>title</a></li>
             <li><a href='<?php echo "documents.php?professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId&q=$q&sort=4";?>'>username</a></li>
             
           </ul>
         </div>
         
       <div>
            <ol>
                <?php
                if(!empty($documents))
                {
                    echo "<div id='solrdocuments' class='bs-docs-section'>";
                    foreach($documents as $document)
                    {
                        //[id] => 13 [bucketid] => 1 [collegeid] => 1 [userid] => 7 [title] => parallel programming basic [description] => java program to make game using tcp and server
                        //[url] => sris71parallelsystemsmaterial/1451165988376HelloServer(1).java [rating] => 0 [hide] => 0
                        echo "<div id='solrdocument".$document['id']."' style='border-left-color: #d9534f;padding: 20px;margin: 20px 0;border: 1px solid #eee;
                        border-left-width: 5px;border-radius: 3px;'>";
                        echo '<button type="button" class="btn btn-default btn-lg" onclick="ratingDoc(this)" name="'.$document['id'].'" style="float:right;margin-right:4px;">
                        <span class="glyphicon glyphicon-hand-right" aria-hidden="true"></span> Like <span class="badge" id="likes'.$document['id'].'">'.$document['rating'].'</span>
                        </button>';
                        if($document['username']==$_SESSION['username'] ||isset($roleId) && $roleId!=1)
                        echo '<button type="button" class="btn btn-default btn-lg" onclick="removeDoc(this)" name="'.$document['id'].'" style="float:right;margin-right:4px;">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Remove
                        </button>';
                        echo '<button type="button" class="btn btn-default btn-lg" onclick="report(this)" name="'.$document['id'].'" style="float:right;margin-right:4px;">
                        <span class="glyphicon glyphicon-flag" aria-hidden="true"></span> Report
                        </button>';
                        echo "<h4 style='color: #d9534f'>".$document["title"]."</h4>";
                        echo "<p>Posted By: <span class='label label-warning'>".$document["username"]."</span></p>";
                        echo "<p>Date: <span class='label label-default'>".$document["lastmodified"]."</span></p>";
                        echo "<p>".$document["description"]."</p>";
                        echo '<div><a href="#" class="btn btn-primary" role="button" onclick="opencommentdialog(this);" value="'.$document['id'].'">Post Comment</a><a href="comments.php?q='.$q.'&start='.$start.'&professorId='.$professorId.'&bucketId='.$bucketId.'&subjectId='.$subjectId.'&collegeId='.$collegeId.'&sort='.$sort.'&documentId='.$document['id'].'" style="margin-left:12px;">View Comments <span class="badge">!</span></a>
 <a href="'.$document['url'].'" class="btn btn-danger" role="button" ><span class="glyphicon glyphicon-save" aria-hidden="true"></span>Download</a></div>';
                        echo "</div>";
                    }
                    echo "</div>";
                }
                ?>
           </ol>
            <?php
            if(empty($documents))
            {
                echo "<div class=\"panel panel-warning\"><div class=\"panel-heading\"><h2 class=\"panel-title\">No results</h2></div> Try new search no results found. </div>";
            }
            ?>
       </div>
       <?php paginationDocumentPage($start,$total_pages,$bucketId,$professorId,$subjectId,$collegeId,$sort,$q);?>
    </div><!-- /.container -->
    
    <!-- Modal -->
    <div class="modal fade" id="postDocumentModal" tabindex="-1" role="dialog" 
         aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <!-- Modal Header -->
                <div class="modal-header">
                    <button type="button" class="close" 
                       data-dismiss="modal">
                           <span aria-hidden="true">&times;</span>
                           <span class="sr-only">Close</span>
                    </button>
                    <h4 class="modal-title" id="myModalLabel">
                        Post Document
                    </h4>
                </div>
                
                <!-- Modal Body createbucket -->
                <div class="modal-body">
                    
                    <form class="form-horizontal" role="form" id="postDocumentForm" enctype="multipart/form-data" method="post" action="<?php echo "documents.php?professorId=$professorId&bucketId=$bucketId&subjectId=$subjectId&collegeId=$collegeId";?>">
                      <div class="form-group">
                        <label class="col-sm-2 control-label"
                              for="doctitle" >title:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control"
                                id="doctitle" placeholder="document title" name="title" required/>
                        </div>
                      </div>
                      <div class="form-group">
                        <label class="col-sm-2 control-label"
                              for="docdesc" >description:</label>
                        <div class="col-sm-10">
                             <textarea class="form-control" rows="3" id="docdesc" placeholder="description" name="desc" required></textarea>
                        </div>
                      </div>
                       <p>Select a file : <input type="file" name="file" size="85"  id="docfile" accept="application/msword, application/vnd.ms-excel, application/vnd.ms-powerpoint,
                         text/plain, application/pdf" /></p>
                       <input type="hidden" name="subjectid" value="<?php echo $subjectId?>">
                       <input type="hidden" name="bucketid" value="<?php echo $bucketId?>">
                       <input type="hidden" name="collegeid" value="<?php echo $collegeId?>">
                       <input type="hidden" name="professorid" value="<?php echo $professorId?>">
                       <input type="hidden" name="userid" value="<?php echo $userId?>">
                       <input type="hidden" name="uniqid" value="<?php echo uniqid();?>" />
                    </form>          
                </div>        
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">
                                Close
                    </button>
                    <button id="postDocumentButton" type="button" class="btn btn-primary">
                        post document
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    
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
    
    <!-- Modal  reason report modal required-->
    <div id="reasonmodal" class="modal fade" role="dialog">
      <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="reasontitle"> Reason for reporting</h4>
          </div>
          <div class="modal-body">
             <input type="radio" name="reason" value="abusive" checked> Abusive<br>
             <input type="radio" name="reason" value="copyright violation"> Copyright violation<br>
             <input type="radio" name="reason" value="other"> Other
          </div>
          <div class="modal-footer">
             <button type="button" class="btn btn-default" id="reportreason"> Report</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
        var documentId=0;
        var userId=<?php echo $userId?>;
        function logout()
      {
        $('#logoutmodal').modal('show');
      }
        function opencommentdialog(a){
                    documentId=$(a).attr("value");
                    $('#postcommentmodal').modal('show');
                }
                
        function report(a)
        {
            documentId=$(a).attr("name");
            $(a).prop("disabled",true);
            $('#reasonmodal').modal('show');
            $('#reportreason').click(function(){
                $('#reasonmodal').modal('hide');
                $('#pleaseWaitDialog').modal('show');
                var reasonActual=$("input[name=reason]:checked").val();
                $.post( "http://localhost:8088/simple-service-webapp/api/users/document/reportDocument",
                JSON.stringify({ reporter: userId, documentid: documentId, reason:reasonActual}),"json")
                .done(function( data ) {
                 if(data[0].msg!="you are banned")
                 {
                    $('#pleaseWaitDialog').modal('hide');
                    $('#validationtitle').text("Attention document is reported");
                    $('#validationmsg').text("Document reported");
                    $('#validationmodal').modal("show");
                 }
                 else
                 {
                    $('#pleaseWaitDialog').modal('hide');
                    $('#validationtitle').text("Attention cant perform action");
                    $('#validationmsg').text(" You are banned or error occured");
                    $('#validationmodal').modal("show");
                 }
                });
            });
            
        }
        
        function removeDoc(a)
        {
            documentId=$(a).attr("name");
            $(a).prop("disabled",true);
            $('#pleaseWaitDialog').modal('show');
            $.post( "http://localhost:8088/simple-service-webapp/api/users/document/hideDocument",
            JSON.stringify({ userid: userId, documentid: documentId}),"json")
            .done(function( data ) {
             if(data[0].msg!="you are banned")
             {
                $('#pleaseWaitDialog').modal('hide');
                $('#validationtitle').text("Attention document is removed");
                $('#validationmsg').text("Document removed reload page");
                $('#validationmodal').modal("show");
                $('#solrdocument'+documentId).remove()
                var totldocs=$('#totaldocs').text();
                if(parseInt(totldocs)>0)
                totaldocs=totldocs-1;
                $('#totaldocs').text(totaldocs);
             }
             else
             {
                $('#pleaseWaitDialog').modal('hide');
                $('#validationtitle').text("Attention cant perform action");
                $('#validationmsg').text(" You are banned or error occured");
                $('#validationmodal').modal("show");
             }
         });
            
        }
        
        function ratingDoc(a)
        {
            documentId=$(a).attr("name");
            $(a).prop("disabled",true);
            $('#pleaseWaitDialog').modal('show');
            $.post( "http://localhost:8088/simple-service-webapp/api/users/document/rateDocument",
            JSON.stringify({ userid: userId, documentid: documentId}),"json")
            .done(function( data ) {
             if(data[0].msg!="you are banned")
             {
                var rating=parseInt($('#likes'+documentId).text());
                $('#likes'+documentId).text(rating+1);
                $('#pleaseWaitDialog').modal('hide');
                $('#validationtitle').text("Document rated!!");
                $('#validationmsg').text("Document rated reload page");
                $('#validationmodal').modal("show");
             }
             else
             {
                $('#pleaseWaitDialog').modal('hide');
                $('#validationtitle').text("Attention cant perform action");
                $('#validationmsg').text(" You are banned or error occured");
                $('#validationmodal').modal("show");
             }
         });
        }
        $(document).ready(function () {
            initialize();
            function initialize()
            {
                $('#postDoc').click(function(){
                   $('#postDocumentModal').modal('show');
                });
                
                $('#postDocumentButton').click(function(){
                    var validationcheck=true;
                   if(0==$('#docdesc').val().trim().length||""==$('#docdesc').val())
                    {
                        $('#validationtitle').text("Attention Description is required");
                        $('#validationmsg').text(" You need to fill in Description of the document");
                        $('#validationmodal').modal("show");
                        validationcheck=false;
                    }
                    if(0==$('#doctitle').val().trim().length||""==$('#doctitle').val())
                    {
                        $('#validationtitle').text("Attention title is required");
                        $('#validationmsg').text(" You need to fill in Title of the document");
                        $('#validationmodal').modal("show");
                        validationcheck=false;
                    }
                    if(0==$('#docfile').val().trim().length||""==$('#docfile').val())
                    {
                        $('#validationtitle').text("Attention document is required");
                        $('#validationmsg').text("You need to chose document to upload ");
                        $('#validationmodal').modal("show");
                        validationcheck=false;
                    }
                    if(validationcheck)
                    {
                        $("#postDocumentForm").submit(); 
                    }
                });
                
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
                                    $('#pleaseWaitDialog').modal('hide');
                                   $('#validationtitle').text("Attention comment is posted");
                                   $('#validationmsg').text(" Comment has been posted");
                                   $('#postcommentmodal').modal('hide');
                                   $('#validationmodal').modal("show");
                                   $('#postCommentDb').val("");
                                }
                                else
                                {
                                    $('#pleaseWaitDialog').modal('hide');
                                   $('#validationtitle').text("Attention comment not posted");
                                   $('#validationmsg').text(" You are banned or error occured");
                                   $('#postcommentmodal').modal('hide');
                                   $('#validationmodal').modal("show");
                                   $('#postCommentDb').val("");
                                }
                            });
                        }
                        else
                        {
                            $('#pleaseWaitDialog').modal('hide');
                            $('#validationtitle').text("Attention you need to have comment");
                            $('#validationmsg').text("please enter comment");
                            $('#validationmodal').modal("show");
                        }
                    }
                });
            }$('#postCommentDb').val("");
        });
    </script>
  </body>
</html>
