<?php
session_start();
ob_start();
include_once 'function_lib.inc';
expireSession();
ini_set("display_errors",1);
if(isset($_SESSION) && !isset($_SESSION['username']) && !isset($_SESSION['registered']))
{
    header("Location: index.php?m=timeout");
}
else
{
    //get list of colleges
    if(1==$_SESSION['registered'])
    {
        $url="http://localhost:8088/simple-service-webapp/api/colleges/list";
        $colleges=opts($url);
    }
    if(isset($_SESSION['username']))
    {
        $url2="http://localhost:8088/simple-service-webapp/api/users/".$_SESSION['username'];
        $userid=opts($url2);
        $_SESSION['userId']=$userid[0]['UserId'];
        $userId=$userid[0]['UserId'];
    }
    if(isset($_GET) || isset($_POST))
    {
      //curl request to get documents;
      $q=isset($_GET['q'])?$_GET['q']:"";
      $roleId=isset($_SESSION) && isset($_SESSION['roleId'])?$_SESSION['roleId']:0;
      
      $start=(isset($_GET['start']) && !empty($_GET['start']) && $_GET['start']>=1) ?$_GET['start']:1;
      $sort=(isset($_GET['sort']) && ($_GET['sort']>=0))?$_GET['sort']:1;
      $userId=(isset($userId) && is_numeric($userId))?$userId:0;
      //users/search?q=$q&bucketname=$bucketname&rating=$rating&username=$username&start=$start&sort=$sort
      if($q=="" && isset($_POST['q']))
      {
        $q=$_POST['q'];
      }
      if($start==1 && isset($_POST['start']) && !empty($_POST['start']))
      {
        $start=$_POST['start'];
      }
      if($sort==1 && isset($_POST['sort']) && !empty($_POST['sort']))
      {
        $sort=$_POST['sort'];
      }
      if(isset($_POST)&& !empty($_POST))
      {
        
        if(!empty($_POST['bucketname']))
        {
          $bucketname=implode(" ",array_values($_POST['bucketname']));
          $bucketname=str_replace(".","",$bucketname);
        }
        if(!empty($_POST['rating']))
        {
          $rating=implode(" ",array_values($_POST['rating']));
          $rating=str_replace(".","",$rating);
        }
        if(!empty($_POST['username']))
        {
          $username=implode(" ",array_values($_POST['username']));
          $username=str_replace(".","",$username);
        }
      }
      if(isset($_POST) && empty($_POST['username']))
      {
        $username=(isset($_GET['username']) && (!empty($_GET['username'])))?$_GET['username']:"";
        $isChecked=1;
      }
      if(isset($_POST) && empty($_POST['bucketname']))
      {
        $bucketname=(isset($_GET['bucketname']) && (!empty($_GET['bucketname'])))?$_GET['bucketname']:"";
        $isChecked=1;
      }
      if(isset($_POST) && empty($_POST['rating']))
      {
        $rating=(isset($_GET['rating']) && ($_GET['rating']>=0))?$_GET['rating']:-1;
        $isChecked=1;
      }
      if(isset($_POST['searchbutton']))
      $isChecked=1;
      $checked="";
      //getting documents
      unset($_POST);
      //print_r($bucketname);exit;
      $documents_response=getDocumentsGeneral(urlencode($q),urlencode($username),urlencode($bucketname),urlencode($rating),$start,$sort);
      $response_facet=json_decode($documents_response['facet'],true);
      $documents=json_decode($documents_response['response'],true);
      $bucketfacet=$response_facet['bucketname'];
      $ratingfacet=$response_facet['rating'];
      $usernamefacet=$response_facet['username'];
      $bucketidfacet=array_keys($response_facet['bucketid']);
      $useridfacet=array_keys($response_facet['userid']);
      $total_documents=$documents_response['numFound'];
      $total_pages = ceil($total_documents / 10);
    }
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
    <link rel="icon" href="../../favicon.ico">

    <title>Dashboard Template for Bootstrap</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap-3.3.6-dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="dashboard.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <nav class="navbar navbar-inverse navbar-fixed-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a class="navbar-brand" href="#">SRIS</a>
        </div>
          <ul class="nav navbar-nav">
            <li class="active"><a href="#">Home</a></li>
            <li><a href="#about">Admin</a></li>
          </ul>
      </div>
    </nav>

      <div class="container">
        <br/>
        <br/>
        <br/>
        <br/>
        <div class="row">
          <div class="col-sm-8">
            <?php paginationDashBoard($q,$bucketname,$username,$rating,$sort,$start,$total_pages);?>
            <div class="btn-group">
           <button type="button" class="btn btn-danger">Sort</button>
           <button type="button" class="btn btn-danger dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
             <span class="caret"></span>
             <span class="sr-only">Toggle Dropdown</span>
           </button>
           <ul class="dropdown-menu">
             <li><a href='<?php echo "newdash.php?q=$q&bucketname=$bucketname&username=$username&rating=$rating&sort=2&start=$start";?>'>Rating</a></li>
             <li><a href='<?php echo "newdash.php?q=$q&bucketname=$bucketname&username=$username&rating=$rating&sort=1&start=$start";?>'>Recent</a></li>
             <li><a href='<?php echo "newdash.php?q=$q&bucketname=$bucketname&username=$username&rating=$rating&sort=0&start=$start";?>'>Old</a></li>
           </ul>
         </div>
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
                        echo '<button type="button" class="btn btn-default btn-lg" onclick="removeDoc(this)" name="'.$document['id'].'" style="float:right;margin-right:4px;">
                        <span class="glyphicon glyphicon-trash" aria-hidden="true"></span> Hide
                        </button>';
                        echo '<button type="button" class="btn btn-default btn-lg" onclick="report(this)" name="'.$document['id'].'" style="float:right;margin-right:4px;">
                        <span class="glyphicon glyphicon-flag" aria-hidden="true"></span> Report
                        </button>';
                        echo "<h4 style='color: #d9534f'>".$document["title"]."</h4>";
                        echo "<p>Posted By: <span class='label label-warning'>".$document["username"]."</span></p>";
                        echo "<p>Date: <span class='label label-default'>".$document["lastmodified"]."</span></p>";
                        echo "<p>".$document["description"]."</p>";
                        echo '<div><a href="'.$document['url'].'" class="btn btn-danger" role="button" ><span class="glyphicon glyphicon-save" aria-hidden="true"></span>Download</a></div>';
                        echo "</div>";
                    }
                    echo "</div>";
                }
                ?>
           </ol>
            <?php paginationDashBoard($q,$bucketname,$username,$rating,$sort,$start,$total_pages);?>
          </div>
          <div class="col-sm-3 col-sm-offset-1 blog-sidebar">
          <div class="sidebar-module">
            <h4>Search for documents</h4>
            <form method="POST" action="newdash.php" name='searchform' id="searchform">
              <input type="text" class="form-control" placeholder="Search..." name="q" id="qvalue">
              <input type="hidden" class="form-control" name="userid"  value="<?php echo $userId;?>">
              <input type="hidden" class="form-control" name="sort"  value="<?php echo $sort;?>">
              <input type="hidden" class="form-control" name="start"  value="1">
               <br/>
              <button style="width: 50%;" type="submit" class="btn btn-success" aria-label="Center Align" id="searchbutton" name="searchbutton">Search</button>
               <p>Filters:</p> 
              <div class="dropdown">
                <button style="width: 80%;" class="btn btn-default dropdown-toggle" type="button" id="bucketnamefilter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                  Bucketname
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                  <?php
                  $i=0;
                  foreach($bucketfacet as $value=>$count)
                  {
                      if(isset($isChecked) && !empty($bucketname) && preg_match("/$bucketidfacet[$i]/",$bucketname)>0)
                      {
                        $checked="checked";
                      }
                      echo "<li style='margin-bottom:3px;'><input type='checkbox' name='bucketname[]' value='.$bucketidfacet[$i].' ".$checked."> $value  ($count)</li>";
                      $i++;
                      $checked="";
                  }
                  ?>
                </ul>
              </div>
              <br/>
              <div class="dropdown">
                <button style="width: 80%;" class="btn btn-default dropdown-toggle" type="button" id="usernamefilter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                   Username
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                  <?php
                  $i=0;
                  foreach($usernamefacet as $value=>$count)
                  {
                      if(isset($isChecked) && !empty($username)  && preg_match("/$useridfacet[$i]/",$username)>0)
                      $checked="checked";
                      echo "<li style='margin-bottom:3px;'><input type='checkbox' name='username[]' value='.$useridfacet[$i].' ".$checked."> $value  ($count)</li>";
                      $checked="";
                  }
                  ?>
                </ul>
              </div>
              <br/>
              <div class="dropdown">
                <button style="width: 80%;" class="btn btn-default dropdown-toggle" type="button" id="ratingfilter" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                   Rating
                  <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                  <?php
                  foreach($ratingfacet as $value=>$count)
                  {
                     if(isset($isChecked) && $rating!=-1 && preg_match("/$value/",$rating)>0)
                      $checked="checked"; 
                      echo "<li style='margin-bottom:3px;'><input type='checkbox' name='rating[]' value='.$value.' ".$checked."> $value  ($count)</li>";
                      $checked="";
                  }
                  ?>
                </ul>
              </div>
            </form>
          </div>
          <div class="sidebar-module">
            <h4>Colleges</h4>
            <ol class='list-group'>
              <?php
                foreach($colleges as $college)
                {
                    echo "<li class='list-group-item'><a href='professors.php?collegeId=".$college['CollegeId']."'>".$college['Name']."</a></li>";//$college['CollegeId']
                }
                ?>
            </ol>
          </div>
        </div><!-- /.blog-sidebar -->
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


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
    <script type="text/javascript">
      var documentId=0;
      var userId=<?php echo $userId;?>;
      function report(a)
        {
            documentId=$(a).attr("name");
            $(a).prop("disabled",true);
            $('#pleaseWaitDialog').modal('show');
            $.post( "http://localhost:8088/simple-service-webapp/api/users/document/reportDocument",
            JSON.stringify({ reporter: userId, documentid: documentId}),"json")
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
    </script>
  </body>
</html>
