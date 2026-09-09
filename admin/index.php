<?php
session_start();
error_reporting(0);
include("dbconnection.php");
if(isset($_POST['login']))
{
  $adminusername=$_POST['username'];
  $pass=$_POST['password'];
  $ret=mysqli_query($con,"SELECT * FROM users WHERE (email='$adminusername' AND pass='$pass') AND type='admin' ");
  $num=mysqli_fetch_array($ret);
  if($num>0)
  {
    $extra="dashboard.php";
    $_SESSION['login']=$_POST['username'];
    $_SESSION['id']=$num['id'];
    echo "<script>window.location.href='".$extra."'</script>";
    exit();
  }
  else
  {
    $_SESSION['action1']="*Invalid username or password";
    $extra="index.php";
    echo "<script>window.location.href='".$extra."'</script>";
    exit();
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>Admin | Login</title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link href="../assets/css/auth.css" rel="stylesheet" />
</head>

<body class="auth-page">
  <header class="site-header">
    <nav class="auth-nav" aria-label="Authentication navigation">
      <a class="auth-brand" href="../index.php"><span class="auth-brand-mark"><i></i><i></i><i></i></span><span>car<strong>share</strong></span></a>
      <ul class="auth-nav-links">
        <li><span style="color:var(--auth-lime); font-weight:600;"><i class="material-icons" style="font-size:16px;vertical-align:middle;">admin_panel_settings</i> Admin Portal</span></li>
      </ul>
    </nav>
  </header>
  
  <div class="page-header header-filter auth-header">
    <div class="container auth-layout" style="grid-template-columns: 1fr; justify-content:center; max-width:440px; margin:auto;">
      <div class="auth-card-wrap">
          <div class="card card-login auth-card" style="margin:auto;">
            <div class="auth-card-header">
                <h2 style="font-size:24px;">Admin Console</h2>
                <p>Secure login for platform administrators.</p>
            </div>
            
            <form class="form auth-form" method="post" action="">
              <div class="card-body">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="material-icons">person</i></span>
                  </div> 
                  <input type="text" name="username" class="form-control" placeholder="Admin ID (Email)" autofocus required>
                </div>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text"><i class="material-icons">lock_outline</i></span>
                  </div>
                  <input type="password" name="password" class="form-control" placeholder="Password" required>
                </div>
              </div>
              <div class="footer text-center">
                <?php if ($_SESSION['action1'] !== '') { echo '<span class="auth-error">'.$_SESSION['action1'].'</span>'; $_SESSION['action1']=""; } ?>
                <button type="submit" name="login" class="btn auth-submit" style="background:#173f36; color:white;">Authenticate <span aria-hidden="true">&rarr;</span></button>
                <span class="auth-switch"><a href="../index.php">&larr; Back to main site</a></span>
              </div>
            </form>
          </div>
      </div>
    </div>
  </div>
</body>
</html>
