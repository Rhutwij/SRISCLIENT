<?php
session_start();
ob_start();
include_once 'function_lib.inc';
expireSession();

if($_SESSION['roleId']==1 || !isset($_SESSION['roleId']))
{
    @header('Location: index.php?m=noaccess');
}

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
    
    if(isset($_POST) && !empty($_POST) && isset($_GET['form']) && $_GET['form']=='add')
    {
        $error=0;
        //add college curl request;
        //print_r($_POST);exit;
        if(trim($_POST['nameadd'])=="")
        {
            $error=1;
        }
        if(trim($_POST['typeadd'])=="")
        {
            if($error==1)
            $error=2;
            else
            $error=3;
        }
        $userId=(isset($_SESSION['userId']) && !empty($_SESSION['userId']))?$_SESSION['userId']:0;
        if($error==0)
        {
            $add_response=addCollege($_POST['nameadd'],$_POST['typeadd'],$userId);
            if($add_response[0]["msg"]=="college added")
            {
                $error=0;
            }
            else
            {
                $error=3;
            }
        }
    }
    if(isset($_POST) && !empty($_POST) && isset($_GET['form']) && is_numeric($_GET['form']))
    {
        $error=0;
        if(isset($_POST['edit'.$_GET['form']]))
        {
            //update request
            $edit_reponse=editCollege($_POST['name'.$_GET['form']],$_POST['type'.$_GET['form']],$userId,$_GET['form']);
            if($edit_reponse[0]['msg']=="you are banned")
            {
                $error=4;
            }
        }
        
        if(isset($_POST['delete'.$_GET['form']]))
        {
            //delete request
            $delete_reponse=deleteCollege($userId,$_GET['form']);
            if($delete_reponse[0]['msg']=="you are banned")
            {
                $error=5;
            }
        }
    }
    $colleges=getColleges();
    //print_r($colleges);exit;    
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
    if(isset($error) && $error)
    {
        if($error==1)
        {
            $error_msg=" Name is empty";
        }
        else if($error==2)
        {
            $error_msg=" Name  & Type are empty";
        }
        else if($error==3)
        {
            $error_msg="Type is empty";
        }
        else if($error==4)
        {
            $error_msg="Operation Failed";
        }
        else if($error==5)
        {
            $error_msg="Operation Failed";
        }
        else
        {
            $error_msg="You dont have priviliges or service not available";
        }
        
        echo "<div class=\"alert alert-danger\" role=\"alert\">
                <span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
                <span class=\"sr-only\"></span>
                <span style=\"font-size:1.3em\"> $error_msg</span>
                </div>";
    }
    else
    {
        if(isset($error) && $error==0)
        {
            echo "<div class=\"alert alert-success\" role=\"success\">
                    <span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
                    <span style=\"font-size:1.3em\"> Operation Succeeded</span>
                    </div>";
        }
    }
    ?>
    <br/>
      <h1> Colleges Management</h1>
      <ol class="breadcrumb">
         <li><a href="dash.php">Home</a></li>
         <li><a href='admin.php'>Admin</a></li>
      </ol>
      <div class="starter-template">
       <p> Add /Remove /Edit Colleges table</p>
      </div>
       <div>
           <table width="100%">
            <thead>
              <tr>
                <td>
                    <table class="table">
                        <tr>
                            <thead>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Actions</th>
                            </thead>
                        </tr>
                    </table>
                </td>
              </tr>
            </thead>
            <tbody>
<?php
          foreach($colleges as $college)
          {
            echo '<tr id='."tr".$college['CollegeId'].'><td><form method="POST" action="colleges.php?form='.$college['CollegeId'].'"><table class="table"><tr><td><input type="text" class="form-control"  name='."name".$college['CollegeId'].' id='."name".$college['CollegeId'].' value="'.$college['Name'].'"></td>
            <td><input type="text" class="form-control" name='."type".$college['CollegeId'].' id='."type".$college['CollegeId'].' value="'.$college['Type'].'"></td>
            <td><button type="submit" name='."edit".$college['CollegeId'].' class="btn btn-success">Edit</button> <button type="submit" name='."delete".$college['CollegeId'].' class="btn btn-danger">Delete</button></td></td></tr></table></form></tr>';
          }
           echo '<tr id="tradd"><td><form method="POST" action="colleges.php?form=add"><table class="table"><tr><td><input type="text" class="form-control"  name="nameadd" value=""></td>
            <td><input type="text" class="form-control" name="typeadd" value=""></td>
            <td><button type="submit" name="add" class="btn btn-success">Add</button></td></td></tr></table></form></tr>';
          
          
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
