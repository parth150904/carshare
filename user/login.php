<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
   Login
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <link href="../assets/css/auth.css" rel="stylesheet" />
  <?php
  require '../config.php';
  $error = "";

  if(isset($_POST['login']))
  {
    $email=$_POST['email'];
    $pass = $_POST['pass'];

    $ver = mysqli_query($con,"SELECT email FROM users WHERE email='$email' AND pass='$pass'");
    $ver_num = mysqli_num_rows($ver);

    if ($ver_num == 1)
    {
      session_start();
      $_SESSION['user'] = $email;
      header("location:profile-page.php");
    }
    else
    {
      $error = "Email/Password does't match";
    }
  } 
  ?>
</head>

<body class="sidebar-collapse auth-page">
  <header class="site-header">
    <nav class="auth-nav" aria-label="Authentication navigation">
      <a class="auth-brand" href="../index.php"><span class="auth-brand-mark"><i></i><i></i><i></i></span><span>car<strong>share</strong></span></a>
      <ul class="auth-nav-links">
        <li><a class="auth-nav-active" href="login.php">Log in</a></li>
        <li><a href="user-reg.php">Create account</a></li>
      </ul>
    </nav>
  </header>
  <div class="page-header header-filter auth-header">
    <div class="container auth-layout">
      <div class="auth-story">
        <p class="auth-eyebrow"><span></span> Welcome back</p>
        <h1>Good journeys<br><em>start here.</em></h1>
        <p>Pick up where you left off. Your next shared ride is only a few clicks away.</p>
        <div class="auth-story-points"><span class="auth-story-point"><span>&#10003;</span> Trusted community</span><span class="auth-story-point"><span>&#10003;</span> Simple booking</span></div>
      </div>
      <div class="auth-card-wrap">
          <div class="card card-login auth-card">
            <div class="auth-card-header"><h2>Welcome back</h2><p>Log in to manage your shared journeys.</p></div>
            <form class="form auth-form" method="post" action="">
              <div class="card-body">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="material-icons">mail</i>
                    </span>
                  </div> 
                  <input type="email" name="email" class="form-control" placeholder="Email...">
                </div>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="material-icons">lock_outline</i>
                    </span>
                  </div>
                  <input type="password" name="pass" class="form-control" placeholder="Password...">
                </div>
              </div>
              <div class="footer text-center">
                <?php if ($error !== '') { echo '<span class="auth-error">'.$error.'</span>'; } ?>
                <button type="submit" name="login" class="btn auth-submit">Log in <span aria-hidden="true">&#8594;</span></button>
                <span class="auth-switch">New to CarShare? <a href="user-reg.php">Create an account</a></span>
              </div>
            </form>
          </div>
      </div>
    </div>
    
</body>
</html>
