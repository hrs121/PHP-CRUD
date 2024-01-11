<?php
require('connection.php');

$error = '';

if (isset($_POST['email']) && isset($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $passHashQuery = "SELECT password FROM employeeReport WHERE email = ?";
    $stmt = mysqli_prepare($con, $passHashQuery);

    mysqli_stmt_bind_param($stmt, "s", $email);

    // Execute the statement
    mysqli_stmt_execute($stmt);

    // Bind the result
    mysqli_stmt_bind_result($stmt, $passHash);

    // Fetch the result
    mysqli_stmt_fetch($stmt);

    // Close the statement
    mysqli_stmt_close($stmt);

    // Check if both email and password are not empty
    if (!empty($email) && !empty($password)) {
        // Verify the hashed password using MD5
        if ($passHash !== null && md5($password) === $passHash) {
            // Password is correct, proceed with login
            $sql = "SELECT Id FROM employeeReport WHERE email = ?";
            $stmt = mysqli_prepare($con, $sql);

            // Bind the email parameter
            mysqli_stmt_bind_param($stmt, "s", $email);

            // Execute the statement
            mysqli_stmt_execute($stmt);

            // Get the result
            mysqli_stmt_store_result($stmt);
            $mysqli_num_rows = mysqli_stmt_num_rows($stmt);

            if ($mysqli_num_rows) {
                header('location: Home.php');
            } else {
                $error = "Invalid Email or Password";
            }

            // Close the statement
            mysqli_stmt_close($stmt);
        } else {
            $error = "Invalid Email or Password";
        }
    } else {
        $error = "Please fill out all the fields";
    }
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

              <h2 class="fw-bold mb-2 text-uppercase">Login</h2>
              <p class="text-white-50 mb-5">Please enter your email and password!</p>

              <!-- form starts-->
                <form action="login.php" method="post"> 

                <div class="form-outline form-white mb-4">
              <label class="form-label" for="typeEmailX">Email</label>
                <input type="email" name="email" id="typeEmailX" class="form-control form-control-lg" />  
              </div>

              <div class="form-outline form-white mb-4">
              <label class="form-label" for="typePasswordX">Password</label>
                <input type="password" name='password' id="typePasswordX" class="form-control form-control-lg" />
                </div>

              <button class="btn btn-outline-light btn-lg px-5" type="submit">Login</button>
                </form>

                <p><a href="./reg.php">Don't have an account?</a></p>
            </div>

  </div>
</section>
    </body>
</html>
