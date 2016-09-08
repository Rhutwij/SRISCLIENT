<?php
session_start();
ob_start();
include_once 'function_lib.inc';
expireSession();
if(isset($_SESSION) && !isset($_SESSION['username']) && !isset($_SESSION['registered']))
{
    @header("Location: index.php?m=timeout");
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
    
    if($_SESSION['roleId']==1 || !isset($_SESSION['roleId']))
    {
        @header('Location: index.php?m=noaccess');
    }
    
    $ban=0;
    if(isset($_POST) && !empty($_POST) && isset($_POST['reportselected']))
    {
        if($_POST['optionselected']=="bannedUsers")
        {
            $users=getBannedUsers();
            $ban=1;
        }
        else
        {
            $users=getUnBannedUsers();
        }
        unset($_POST);
    }
    else
    {
        $users=getBannedUsers();
        $ban=1;
    }
    //get Documents here;
    //print_r($subjects);exit;    
    @header('Access-Control-Allow-Origin: *');
    //print_r($_SESSION);
    
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
            <li><a href="admin.php">Admin</a></li>
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
    <?php
    
    ?>
    <br/>
      <h1> User Management</h1>
      <ol class="breadcrumb">
         <li><a href="dash.php">Home</a></li>
         <li><a href='admin.php'>Admin</a></li>
      </ol>
      <div class="starter-template">
       <p>Users Change roles/Ban/Unban</p>
      </div>
       <div>
        <form method="POST" action="users.php">
        <select name="optionselected">
            
            <?php
            if($ban==1)
            echo '<option value="bannedUsers" selected>bannedUsers</option>';
            else
            echo '<option value="bannedUsers">bannedUsers</option>';
            if($ban==0)
            echo '<option value="unbannedUsers" selected>unbannedUsers</option>';
            else
            echo '<option value="unbannedUsers">unbannedUsers</option>';
            ?>
        </select>
        <input type="submit" name='reportselected' value="Go">
        </form>
           <table width="100%" cellspacing="8" cellpadding="6" style="border-spacing:1em;border-collapse:separate">
            <thead>
              <tr>
                <?php
                if(isset($users)  && !empty($users) )
                {
                    echo '
                    <th>Username</th>
                    <th>UserId</th>
                    <th>RoleId</th>
                    <th>Ban</th>';
                }
                ?>
              </tr>
            </thead>
            <tbody>
           <?php
             if(isset($users) && !empty($users))
             {
                echo printUsersAdminDashBoard($users);
             }
          
            ?>
            </tbody>
           </table>
            
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
            <p id="logoutmessage">Sure you want to logout?</p>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" onclick="window.location.href = 'index.php?m=logout'">logout</button>
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
    
    

    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <script src="bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
    <script type="text/javascript">
        var userId=<?php echo $_SESSION['userId'];?>;
        var banId=0;
        
        function banUser(a)
        {
            banId=$(a).attr("name").replace ( /[^\d.]/g, '' );
            $(a).prop("disabled",true);
            $('#pleaseWaitDialog').modal('show');
            $.post( "http://localhost:8088/simple-service-webapp/api/users/banUser",
            JSON.stringify({ userid: userId, banid: banId}),"json")
            .done(function( data ) {
             if(data[0].msg!="you are banned")
             {
                $('#pleaseWaitDialog').modal('hide');
                $('#validationtitle').text("Attention User banned");
                $('#validationmsg').text("User banned");
                $('#validationmodal').modal("show");
                window.setTimeout(function(){location.reload()},3000)
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
        
        function unBanUser(a)
        {
            banId=$(a).attr("name").replace ( /[^\d.]/g, '' );
            $(a).prop("disabled",true);
            $('#pleaseWaitDialog').modal('show');
            $.post( "http://localhost:8088/simple-service-webapp/api/users/UnBanUser",
            JSON.stringify({ userid: userId, banid: banId}),"json")
            .done(function( data ) {
             if(data[0].msg!="you are banned")
             {
                $('#pleaseWaitDialog').modal('hide');
                $('#validationtitle').text("Attention User Unbanned");
                $('#validationmsg').text("User Unbanned");
                $('#validationmodal').modal("show");
                window.setTimeout(function(){location.reload()},3000)
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
