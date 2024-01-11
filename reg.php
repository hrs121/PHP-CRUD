<?php
require('connection.php');

$error = '';

$fname = isset($_POST['fname']) ? $_POST['fname'] : '';
$lname = isset($_POST['lname']) ? $_POST['lname'] : '';
$Address = isset($_POST['address']) ? $_POST['address'] : '';
$Dob = isset($_POST['dob']) ? $_POST['dob'] : '';

$name = $fname . ' ' . $lname;

if (isset($_POST['email']) && isset($_POST['password'])) {
    
  $email = $_POST['email'];
  $pattern = '/^[a-zA-Z._-]+[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/';
  $isValid = preg_match($pattern, $email);

if(!$isValid){
  $error="Incorrect Email Syntax";
}else{
  $password = md5($_POST['password']); // Using MD5 for password hashing

  if (!empty($email) && !empty($password)) {
      $sql1 = "SELECT email FROM userinfo WHERE email= '$email' AND password='$password'";
      $sql_query1 = mysqli_query($con, $sql1);

      if ($sql_query1 && mysqli_num_rows($sql_query1) > 0) {
          $error = "This email is already registered.";
      } else {
          $sql2 = "INSERT INTO `userinfo`(`Name`,`email`, `password`,`Address`,`DOB`) VALUES ('$name','$email','$password','$Address','$Dob')";
          $sql_query2 = mysqli_query($con, $sql2);

          if ($sql_query2) {
              header('location: RegWelcome.php');
          } else {
              $error = "Please fill out the fields in the correct syntax.";
          }
      }
  } else {
      $error = "Please Fill out all the fields";
  }}
      
    }
?>






<html>
    <head>
        <link rel="stylesheet" href="./login.css">
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    </head>
    <body>
    <section class="vh-100 gradient-custom">
  <div class="container py-5 h-100">
    <div class="row d-flex justify-content-center align-items-center h-100">
      <div class="col-12 col-md-8 col-lg-6 col-xl-5">
        <div class="card bg-dark text-white" style="border-radius: 1rem;">
          <div class="card-body p-5 text-center">

          <div class="alert alert-warning" role="alert">
  <?php echo $error;?>
</div>

            <div class="mb-md-5 mt-md-4 pb-5">

              <h2 class="fw-bold mb-2 text-uppercase">Registration</h2>
              <p class="text-white-50 mb-5">Please enter all the fields.</p>

              <!-- form starts-->
                <form action="reg.php" method="post"> 


              <div class="form-outline form-white mb-4">
              <label class="form-label" for="typefname">First name</label>
                <input type="text" name="fname" id="typefname" class="form-control form-control-lg" placeholder="First Name"/>  
              </div>

              <div class="form-outline form-white mb-4">
              <label class="form-label" for="typelname">Last name</label>
                <input type="text" name="lname" id="typelname" class="form-control form-control-lg" placeholder="Last Name"/>  
              </div>


              <div class="form-outline form-white mb-4">
              <label class="form-label" for="typeEmailX">Email</label>
                <input type="email" name="email" id="typeEmailX" class="form-control form-control-lg" placeholder="Email"/>  
              </div>

              <div class="form-outline form-white mb-4">
              <label class="form-label" for="typeAddress">Address</label>
              <input type="text" name="address" id="typeAddress" class="form-control form-control-lg" placeholder="Address"/>  
              </div>

              <div class="form-outline form-white mb-4">
              <label class="form-label" for="typeDOB">Date of Birth</label>
              <input type="date" name="dob" id="typeDOB" class="form-control form-control-lg" placeholder="Date of birth"/>  
              </div>


              <div class="form-outline form-white mb-4">
              <label class="form-label" for="typePasswordX">Password</label>
              <input type="password" name='password' id="typePasswordX" class="form-control form-control-lg" placeholder="Password"/>
              </div>

              <button class="btn btn-outline-light btn-lg px-5" type="submit">Sign Up</button>
              </form>

            <p><a href="./login.php">I have an account.</a></p>
            </div>

  </div>
</section>
    </body>
</html>