<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="../assets/img/apple-icon.png">
  <link rel="icon" type="image/png" href="../assets/img/favicon.png">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
        Registration
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Space+Grotesk:wght@500;600;700&display=swap" />
  <link rel="stylesheet" href="https://fonts.googleapis.com/icon?family=Material+Icons">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css">
  <link href="../assets/css/auth.css" rel="stylesheet" />
  <?php
  require '../config.php';
  $error = '';
  if(isset($_POST['save']))
  {
    $user = $_POST['user_name'];
    $num =  $_POST['num'];
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $sex = $_POST['sex'];

    $ev = mysqli_query($con,"SELECT email from users WHERE email='$email'");
    $ver_num = mysqli_num_rows($ev);
    if ($ver_num==1)
    {
      $error = 'This email id aldredy exist';
    }
    else
    {
      $ins = mysqli_query($con,"INSERT INTO users (name,email,mo_num,gender,pass) VALUES ('$user','$email','$num','$sex','$pass')");
    }
    
  }
   ?>
</head>

<body class="sidebar-collapse auth-page">
  <header class="site-header">
    <nav class="auth-nav" aria-label="Authentication navigation">
      <a class="auth-brand" href="../index.php"><span class="auth-brand-mark"><i></i><i></i><i></i></span><span>car<strong>share</strong></span></a>
      <ul class="auth-nav-links">
        <li><a href="login.php">Log in</a></li>
        <li><a class="auth-nav-active" href="user-reg.php">Create account</a></li>
      </ul>
    </nav>
  </header>
  <div class="page-header header-filter auth-header">
    <div class="container auth-layout">
      <div class="auth-story">
        <p class="auth-eyebrow"><span></span> Join the journey</p>
        <h1>Make room for<br><em>more good.</em></h1>
        <p>Turn empty seats into new connections. Create your free account and make your next ride count.</p>
        <div class="auth-story-points"><span class="auth-story-point"><span>&#10003;</span> Free to join</span><span class="auth-story-point"><span>&#10003;</span> Easy to share</span></div>
      </div>
      <div class="auth-card-wrap">
          <div class="card card-login auth-card">
            <div class="auth-card-header"><h2>Create your account</h2><p>Join a community built around better journeys.</p></div>
            <form class="form auth-form" method="post">
              <div class="card-body">
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="material-icons">face</i>
                    </span>
                  </div>
                  <input type="text" name="user_name" required="required" class="form-control" placeholder="Name (First-Name Last-Name)">
                </div>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="material-icons">phone</i>
                    </span>
                  </div>
                  <input type="text" name="num" required="required" class="form-control" placeholder="Contact number">
                </div>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="material-icons">mail</i>
                    </span>
                  </div>
                  <input type="email" name="email" required="required" class="form-control" placeholder="Email...">
                </div>
                <div class="input-group">
                  <div class="input-group-prepend">
                    <span class="input-group-text">
                      <i class="material-icons">lock_outline</i>
                    </span>
                  </div>
                  <input type="password" required="required" name="pass" class="form-control" placeholder="Password...">
                </div>
                <div class="form-check form-check-radio auth-gender">
                  <label class="form-check-label">How should we refer to you?</label>
                  <label class="form-check-label">
                      <input class="form-check-input" type="radio" checked="true" name="sex" id="exampleRadios1" value="Male" >
                      Male
                      <span class="circle">
                          <span class="check"></span>
                      </span>
                  </label>
                  <label class="form-check-label">
                      <input class="form-check-input" type="radio" name="sex" id="exampleRadios1" value="Female" >
                      Female
                      <span class="circle">
                          <span class="check"></span>
                      </span>
                  </label>
                </div>
              </div>
              <div class="footer text-center">
                <button type="submit" class="btn auth-submit" name="save">Create account <span aria-hidden="true">&#8599;</span></button>
                <span class="auth-switch">Already a member? <a href="login.php">Log in</a></span>
              </div>
            </form>
            <?php echo '<span style="color:red">'.$error.'</span>' ?>
          </div>
      </div>
    </div>
  
  </div>

</body>
</html>
