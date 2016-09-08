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
    //get list of colleges
    header('Access-Control-Allow-Origin: *');
    if(1==$_SESSION['registered'])
    {
        $collegeId=isset($_GET) && isset($_GET['collegeId'])?$_GET['collegeId']:0;
        $collegeInfo=getCollegeInfo($_GET['collegeId']);
        $professors=getProfessorsByCollegeId($collegeId);
        $subjects=getSubjectsByCollegeId($collegeId);
        $roleId=isset($_SESSION) && isset($_SESSION['roleId'])?$_SESSION['roleId']:0;
    }
    if(isset($_SESSION['username']))
    {
        $userInfo=getuserInfo($_SESSION['username']);
        $display_name=$_SESSION['username'];
        if($userInfo[0]['Ban']==1)
        {
            @header('Location: index.php?m=banned');//redirect to test
        }
    }
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
            if($roleId!=1)
            {
                echo '<li><a href="admin.php">Admin</a></li>';
                echo '<li><a data-toggle="modal" href="#createBucketModal" data-target="#createBucketModal">Create Buckets</a></li>';
            }
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
    <br/>
      <h1><span class="glyphicon glyphicon-book"> <?php echo $collegeInfo[0]['Name']." - ".$collegeInfo[0]['Type'];?></span></h1>
      <ol class="breadcrumb">
         <li><a href="dash.php">Home</a></li>
         <li class="active"><a href='<?php echo "professors.php?collegeId=$collegeId";?>'>Professors</a></li>
      </ol>
      <div class="starter-template">
       <p>Select the professors to show respective buckets of each professor and start sharing</p>
      </div>
       <div>
            <ul>
                <?php
                if(!empty($professors))
                {
                    foreach($professors as $professor)
                    {
                        echo "<li><a href='buckets.php?professorId=".$professor['UserId']."&collegeId=".$collegeId."'>".$professor['Username']."</a></li>";//$college['CollegeId']
                    }
                }
                ?>
           </ul>
       </div>

    </div><!-- /.container -->

    <!-- Modal -->
    <div class="modal fade" id="createBucketModal" tabindex="-1" role="dialog" 
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
                        Create Bucket
                    </h4>
                </div>
                
                <!-- Modal Body createbucket -->
                <div class="modal-body">
                    
                    <form class="form-horizontal" role="form">
                      <div class="form-group">
                        <label class="col-sm-2 control-label"
                              for="bucketname" >Bucket Name:</label>
                        <div class="col-sm-10">
                            <input type="text" class="form-control"
                                id="bucketname" placeholder="bucket name" required/>
                        </div>
                      </div>
                      <div class="form-group">
                           <label for="subject" class="col-sm-2 control-label">Select Subject:</label>
                           <div class="col-sm-10">
                                <select class="form-control" id="subject" style="max-width: 14em;">
                                 <?php
                                 foreach($subjects as $subject)
                                 {
                                     echo "<option value='".$subject['SubjectId']."'>".$subject['Name']."</option>";
                                 }
                                 ?>
                                                          
                                </select>
                           </div>
                       </div>
                    </form>          
                </div>        
                <!-- Modal Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">
                                Close
                    </button>
                    <button id="createbucket" type="button" class="btn btn-primary">
                        Create bucket
                    </button>
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
    
    <!-- Modal  bucket created-->
    <div id="bucketcreatedmsg" class="modal fade" role="dialog">
      <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title" id="createbuckettitle">Created bucket</h4>
          </div>
          <div class="modal-body" id="createbucketmsg">
            <p> BucketCreated</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
          </div>
        </div>
    
      </div>
    </div>
    
    <!-- Modal  bucket name required-->
    <div id="bucketnamerequired" class="modal fade" role="dialog">
      <div class="modal-dialog">
    
        <!-- Modal content-->
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal">&times;</button>
            <h4 class="modal-title"> Alert need a name for bucket</h4>
          </div>
          <div class="modal-body">
            <p> Bucket Name required!!!</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
        initialize();
        function logout()
       {
        $('#logoutmodal').modal('show');
       }
        function initialize()
        {
            $('#createbucket').click(function(){
                if(0==$('#bucketname').val().trim().length||""==$('#bucketname').val())
                {
                    $('#bucketnamerequired').modal("show");    
                }
                else
                {
                    var bucketname=$('#bucketname').val().trim();
                    var subjectId=$('#subject').val();
                    var userId="<?php echo isset($_SESSION['userId'])?$_SESSION['userId']:0;?>";
                    var registered="<?php echo isset($_SESSION['registered'])?$_SESSION['registered']:0;?>";
                    var roleId="<?php echo isset($_SESSION['roleId'])?$_SESSION['roleId']:0;?>";
                    $("#createBucketModal").modal('hide');
                    $('#pleaseWaitDialog').modal('show');
                    $.post( "http://localhost:8088/simple-service-webapp/api/users/createBucket",
                           JSON.stringify({ name: bucketname, subjectid: subjectId,userid:userId }),"json")
                    .done(function( data ) {
                      $('#pleaseWaitDialog').modal('hide');
                      if(data[0].msg)
                      {
                        $('#bucketcreatedmsg').modal('show');
                        location.reload();
                      }
                      else
                      {
                          $("#createbuckettitle").text(" Bucket already exists for this subject");
                          $("#createbucketmsg").text("Only one bucket per subject");
                          $('#bucketcreatedmsg').modal('show');
                      }
                    });
                }
            });
        }
    </script>
  </body>
</html>
