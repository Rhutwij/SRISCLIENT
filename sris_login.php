<?php
session_start();
ob_start();
include_once 'function_lib.inc';
expireSession();
if(isset($_POST) && isset($_POST['register']))
{
    $registration_url="http://localhost:8088/simple-service-webapp/api/users/register";
    $postArray=json_encode(array(
        'username' => $_POST['username'],
        'srispassword' => $_POST['srispassword'],
        'rating' => $_POST['rating'],
        'college' => $_POST['college'],
        'role' => $_SESSION['roleId'],
    ),true);
    $response=optsPost($registration_url,$postArray);
    //print_r($response);exit;
    if(isset($response) && isset($response[0]['HTTP_CODE']) && isset($response[0]['msg']))
    {
        $_SESSION['registered']=1;
        @header('Location: dash.php');
    }
    else
    {
        if(isset($response[0]['error']))
        {
            $has_error=1;
        }
    }
}
//print_r($_SESSION);exit;
if(!isset($_SESSION['username']))
{
    @header('Location: index.php?m=timeout');//redirect to test
}
else
{
    $url="http://localhost:8088/simple-service-webapp/api/colleges/list";
    $colleges=opts($url);
    $userInfo=getuserInfo($_SESSION['username']);
    if($userInfo[0]['Ban']==1)
    {
        @header('Location: index.php?m=banned');//redirect to test
    }
}
/**
 *on form submission
 */

?>
<!DOCTYPE html>
<html lang="en" class="remove-padding" >
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="">
    <meta name="author" content="">
    <link rel="shortcut icon" href="../../assets/ico/favicon.ico">

    <title>Profile Page</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap-3.3.6-dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
      <script src="https://oss.maxcdn.com/libs/respond.js/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>

  <body class="remove-padding" style="filter: blur(20px);background-image: url('https://www.rit.edu/studentaffairs/survivalguide/sites/rit.edu.studentaffairs.survivalguide/files/styles/slideshow_image/public/slideshow_images/Imagine-RIT.png?itok=FDYV83VH'); background-size: cover;background-position: left top;">
    <!-- nav bar and header --->
    <div class="container " style="opacity: 1;">
      <div class="navbar navbar-inverse navbar-fixed-top" role="navigation">
      <div class="container-fluid">
        <div class="navbar-header">
          <a class="navbar-brand" href="#">SRIS Profile Page</a>
        </div>
      </div>
    </div>
      <!--- form and image-->
      <div class="row">
        <div class="col-md-8"></div>
        <div class="col-md-4"></div>
      </div>
      <br/>
      <br/>
      <div class="row">
        <h3 style="color: #FAFAFA">Welcome to SRIS profile page</h3>
        <div class="col-md-12" style="color: #FAFAFA">
          <h5>Create your SRIS profile by entering information below
          </h5>
        </div>
      </div>
      <br/>
      <div class="row">
        <?php
        if($has_error)
	{
	    echo "<div class=\"alert alert-danger\" role=\"alert\">
                <span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
                <span class=\"sr-only\"></span>
                <span style=\"font-size:1.3em\"> Wrong password</span>
                </div>";
	}
       ?>
        <form class="form" role="form" method="post" action="sris_login.php">
          <div class="col-md-8">
            <div>
                <label for="#username" style="color: #FAFAFA">Username: </label>
                <input type="text" name="username" class="form-control" value="<?php echo $_SESSION['username'];?>" id="username" readonly>
                <br/>
                <?php
                  if($has_error)
                  echo "<div class='form-group has-error'>";
                ?>
                <label for="#password" style="color: #FAFAFA">Password: </label>
                <input type="password" name="srispassword" class="form-control" id="password" placeholder="Password" required>
                <?php
                  if($has_error)
                  echo "</div>";
                ?>
                <br/>
                <label for="#college" style="color: #FAFAFA">College: </label>
                <select name="college" id="college">
                <?php
                $disabled="";
                if(isset($userInfo[0]['CollegeId']) && !empty($userInfo[0]['CollegeId']))
                {
                    $disabled="disabled";
                }
                foreach($colleges as $college)
                {
                    $selected="";
                    if($college['CollegeId']==$userInfo[0]['CollegeId'])
                    {
                        $selected="selected";
                    }
                    echo "<option value='".$college['CollegeId']."' $selected $disabled>".$college['Name']."</option>";
                }
                ?>
                </select>
                <br/>
                <?php
                if($disabled=="")
                {
                    $button_text="Register";
                }
                else
                {
                    $button_text="Login";
                }
                ?>
                <button class="btn btn-lg btn-primary btn-block" name="register" type="submit" value="register"><?php echo $button_text;?></button>
              </div>
          </div>
        </form>
      </div>
    </div> <!-- /container -->


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
  </body>
</html>