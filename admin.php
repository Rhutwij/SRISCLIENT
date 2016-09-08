<?php
session_start();
ob_start();
include_once 'function_lib.inc';
expireSession();

if($_SESSION['roleId']==1 || !isset($_SESSION['roleId'])|| !isset($_SESSION['username']))
{
    @header('Location: index.php?m=noaccess');
}

if(isset($_SESSION) && !isset($_SESSION['username']) && !isset($_SESSION['registered']))
{
    header("Location: index.php?m=timeout");
}
else
{
    //get list of colleges
    header('Access-Control-Allow-Origin: *');
    print_r($_SESSION);
    if(isset($_SESSION['username']))
    {
        
        $userInfo=getuserInfo($_SESSION['username']);
        $display_name=$_SESSION['username'];
        if($userInfo[0]['Ban']==1 )
        {
            @header('Location: index.php?m=banned');//redirect to test
        }
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
            <li class="active"><a href="#">Admin</a></li>
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
      <h1> Administartion and Monitoring</h1>
      <ol class="breadcrumb">
         <li><a href="dash.php">Home</a></li>
         <li><a href='#'>Admin</a></li>
      </ol>
      <div class="starter-template">
       <p> Welcome admin, This is monitoring and administration console.</p>
      </div>
       <div>
            <ul>
               <li><a href="colleges.php"> Add/Remove/Edit Colleges</a></li>
               <li><a href="subjects.php"> Add/Remove/Edit Subjects</a></li>
               <li><a href="monitor.php"> Monitor reported Documents/Comments Management</a></li>
               <li><a href="users.php">User Management Console</a></li>
           </ul>
       </div>

    </div><!-- /.container -->

    
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
        function logout()
      {
        $('#logoutmodal').modal('show');
      }
    </script>
  </body>
</html>
