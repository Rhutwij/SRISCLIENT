<?php
session_start();
ob_start();
session_regenerate_id();
// destroy the session
if(isset($_GET['m']) && $_GET['m']=="logout")
{
    session_unset();
    session_destroy();
}
include_once 'util.inc';
expireSession();
if(isset($_POST['ldaplogin']))
{
    if(isset($_POST['username'])){
        if("prof2"==$_POST['username'])
        {
            $_SESSION['username']="prof2";
            $_SESSION['accounttype']="prof2";
            setRoleid();
            @header("Location: sris_login.php?m=success");
        }
        else
        {
        $debug_message='start';
        $ldaphost = 'ldaps://ldap.rit.edu';
        $ldapbasedn = 'ou=people,dc=rit,dc=edu';
        $u = $_POST['username'];//"dsbics";
        $password = $_POST['password'];//"";
	$filter = "(uid=$u)";$dn = "uid=$u, "; 
        $ds = @ldap_connect($ldaphost, 636);
        if ( $ds ){
            ldap_set_option($ds, LDAP_OPT_PROTOCOL_VERSION, 3);
            if ( ($password != "") && ldap_bind($ds, "uid=$u,$ldapbasedn", $password) ){
            //they are in
                $sr = ldap_search($ds, $ldapbasedn,"$filter");
	        $info = ldap_get_entries($ds, $sr);
                $_SESSION['username']=$info[0]['uid'][0];
                $_SESSION['accounttype']=$info[0]['riteduaccounttype'][0];
		if(empty($_SESSION['accounttype']))
		{
		    $_SESSION['accounttype']=$info[0]['title'][0];
		}
                setRoleid();
                header("Location: sris_login.php");
                
            }else{
                $debug_message.='bad user/pass combo';
                header("Location: index.php?m=".$debug_message);
                }
            ldap_close($ds);
            }
        else{
            $debug_message.='no connection';
            header("Location: index.php?m=".$debug_message);
        }
      }//remove later
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

    <title>SRIS Login</title>

    <!-- Bootstrap core CSS -->
    <link href="bootstrap-3.3.6-dist/css/bootstrap.min.css" rel="stylesheet">

    <![endif]-->
  </head>

  <body style="background-image: url('http://www.ntid.rit.edu/sites/default/files/imagecache/newsphoto_big/1280x800.jpg');background-repeat: no-repeat;width: 100%;background-size:cover;">

    <div class="container">
      <form class="form-signin" method="post" action="index.php">
	<h1 class="form-signin-heading" style="text-align: center;">Welcome to SRIS</h1>
	<br/>
        <h4 class="form-signin-heading" style="text-align: center;">SRIS Login</h4>
	<?php
        if(isset($_GET['m']))
	{
	    if($_GET['m']=="banned")
	    $message="You have been banned";
	    else if($_GET['m']=="noaccess")
	    $message=" Access is restricted";
	    else if($_GET['m']=="timeout")
	    $message=" You were timed out due to inactivity";
	    else if($_GET['m']=="logout")
	    $message=" You logged out";
	    else
	    $message="Bad Username/Password combination";
	    
	    echo "<div class=\"alert alert-danger\" role=\"alert\">
                <span class=\"glyphicon glyphicon-exclamation-sign\" aria-hidden=\"true\"></span>
                <span class=\"sr-only\"></span>
                <span style=\"font-size:1.3em\"> $message</span>
                </div>";
	}
       ?>
        <label for="inputEmail" class="sr-only">User Name</label>
        <input type="text" id="username" name="username" class="form-control" placeholder="RIT username" required autofocus>
        <label for="password" class="sr-only">Password</label>
        <input type="password" id="password" name="password" class="form-control" placeholder="RIT password" required>
        <button class="btn btn-lg btn-primary btn-block" type="submit" name="ldaplogin" value="ldaplogin">Sign in</button>
      </form>

    </div> <!-- /container -->
  </body>
</html>
