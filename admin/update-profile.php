<?php
session_start();
include'dbconnection.php';
//Checking session is valid or not
if (strlen($_SESSION['id']==0)) {
  header('location:logout.php');
  } else{

$name = mysqli_query($con,"SELECT name FROM users WHERE id='$_SESSION[id]'");
$user_name = mysqli_fetch_assoc($name);

// for updating user info    
if(isset($_POST['Submit']))
{
	$name=$_POST['name'];
	$email=$_POST['email'];
	$contact=$_POST['mo_num'];
  $gender=$_POST['gender'];
  $type=$_POST['type'];

  $uid=intval($_GET['uid']);
$query=mysqli_query($con,"update users set name='$name' ,email='$email' , mo_num='$contact',gender='$gender',type='$type' where id='$uid'");
$_SESSION['msg']="Profile Updated successfully";

if ($query) {
  echo "<script>window.location.href='manage-users.php'</script>";
}

}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Dashboard">
    <meta name="keyword" content="Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">

    <title>Admin | Update Profile</title>
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <link href="assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/style-responsive.css" rel="stylesheet">
  </head>

  <body>

  <section id="container" >
      <header class="header black-bg">
              <div class="sidebar-toggle-box">
                  <div class="fa fa-bars tooltips" data-placement="right" data-original-title="Toggle Navigation"></div>
              </div>
            <a href="#" class="logo"><b>Admin Dashboard</b></a>
            <div class="nav notify-row" id="top_menu">
               
                         
                   
                </ul>
            </div>
            <div class="top-menu">
            	<ul class="nav pull-right top-menu">
                    <li><a class="logout" href="logout.php">Logout</a></li>
            	</ul>
            </div>
        </header>
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <ul class="sidebar-menu" id="nav-accordion">
              
              	    <p class="centered"><a href="#"><img src="../assets/img/profile.png" class="img-circle" width="60"></a></p>
                  <h5 class="centered"><?php echo $user_name['name'];?></h5>
                    
                  <li class="mt">
                      <a href="change-password.php">
                          <i class="fa fa-file"></i>
                          <span>Change Password</span>
                      </a>
                  </li>

                  <li class="sub-menu">
                      <a href="manage-users.php" >
                          <i class="fa fa-user"></i>
                          <span>Manage Users</span>
                      </a>
                   
                  </li>

                  <li class="sub-menu">
                      <a href="car_ride.php" >
                          <i class="fa fa-car"></i>
                          <span>Added ride</span>
                      </a>
                   
                  </li>

                   <li class="sub-menu">
                      <a href="booked_ride.php" >
                          <i class="fa fa-taxi"></i>
                          <span>Booked ride</span>
                      </a>
                   
                  </li>
              
                 
              </ul>
          </div>
      </aside>
      <?php $ret=mysqli_query($con,"select * from users where id='".$_GET['uid']."'");
	  while($row=mysqli_fetch_array($ret))
	  
	  {?>
      <section id="main-content">
          <section class="wrapper">
          	<h3><i class="fa fa-angle-right"></i> <?php echo $row['name'];?>'s Information</h3>
             	
				<div class="row">
				
                  
	                  
                  <div class="col-md-12">
                      <div class="content-panel">
                      <p align="center" style="color:#F00;"><?php echo $_SESSION['msg'];?><?php echo $_SESSION['msg']=""; ?></p>
                           <form class="form-horizontal style-form" name="form1" method="post" action="" onSubmit="return valid();">
                           <p style="color:#F00"><?php echo $_SESSION['msg'];?><?php echo $_SESSION['msg']="";?></p>
                          <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label" style="padding-left:40px;">Name </label>
                              <div class="col-sm-10">
                                  <input type="text" class="form-control" name="name" value="<?php echo $row['name'];?>" >
                              </div>
                          </div>
                          
                          
                          <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label" style="padding-left:40px;">Email </label>
                              <div class="col-sm-10">
                                  <input type="text" class="form-control" name="email" value="<?php echo $row['email'];?>" readonly >
                              </div>
                          </div>
                           <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label" style="padding-left:40px;">Contact no </label>
                              <div class="col-sm-10">
                                  <input type="text" class="form-control" name="mo_num" value="<?php echo $row['mo_num'];?>" readonly >
                              </div>
                          </div>
                          <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label" style="padding-left:40px;">Gender </label>
                              <div class="col-sm-10">
                                  <select class="form-control" name="gender">
                                    <option <?php if($row['gender'] == 'Male') {echo "selected='selected'" ;} ?>>Male</option>
                                    <option <?php if($row['gender'] == 'Female') {echo "selected='selected'";} ?>>Female</option>
                                  </select>
                              </div>
                          </div>
                           <div class="form-group">
                              <label class="col-sm-2 col-sm-2 control-label" style="padding-left:40px;">Type </label>
                              <div class="col-sm-10">
                                  <select class="form-control" name="type">
                                    <option <?php if($row['type'] == 'user') {echo "selected='selected'" ;} ?>>user</option>
                                    <option <?php if($row['type'] == 'admin') {echo "selected='selected'";} ?>>admin</option>
                                  </select>
                              </div>
                          </div>
                          
                         
                          <div style="margin-left:100px;">
                          <input type="submit" name="Submit" value="Update" class="btn btn-theme"></div>
                          </form>
                      </div>
                  </div>
              </div>
		</section>
        <?php } ?>
      </section></section>
    <script src="assets/js/jquery.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script class="include" type="text/javascript" src="assets/js/jquery.dcjqaccordion.2.7.js"></script>
    <script src="assets/js/jquery.scrollTo.min.js"></script>
    <script src="assets/js/jquery.nicescroll.js" type="text/javascript"></script>
    <script src="assets/js/common-scripts.js"></script>
  <script>
      $(function(){
          $('select.styled').customSelect();
      });

  </script>

  </body>
</html>
<?php } ?>