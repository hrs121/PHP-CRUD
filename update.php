<?php
// Include config file
require_once "connection.php";
 
// Define variables and initialize with empty values
$name = $designation = $salary = "";
$name_err = $designation_err = $salary_err = "";
 
// Processing form data when form is submitted
if(isset($_POST["id"]) && !empty($_POST["id"])){
    // Get hidden input value
    $id = $_POST["id"];
    
    // Validate name
    $input_name = trim($_POST["name"]);
    if(empty($input_name)){
        $name_err = "Please enter a name.";
    } elseif(!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))){
        $name_err = "Please enter a valid name.";
    } else{
        $name = $input_name;
    }
    
    // Validate address address
    $input_designation = trim($_POST["designation"]);
    if(empty($input_designation)){
        $designation_err = "Please enter an Designation.";     
    } else{
        $designation = $input_designation;
    }
    
    // Validate salary
    $input_salary = trim($_POST["salary"]);
    if(empty($input_salary)){
        $salary_err = "Please enter the salary amount.";     
    } elseif(!ctype_digit($input_salary)){
        $salary_err = "Please enter a positive integer value.";
    } else{
        $salary = $input_salary;
    }

    if (isset($_FILES['img']) && $_FILES['img']['error'] == 0) {
        // Validate image
        $img = $_FILES['img']['name'];
        $temp_name = $_FILES['img']['tmp_name'];
        $upload_folder = 'uploads/'; // Specify image upload path

        $allowed_types = array('jpg', 'jpeg', 'png', 'gif');
        $img_extension = strtolower(pathinfo($img, PATHINFO_EXTENSION));

        if (!in_array($img_extension, $allowed_types)) {
            $img_err = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        } elseif ($_FILES['img']['size'] > 500000) { // Limit to 500 KB
            $img_err = "File size must be less than 500 KB.";
        } else {
            // Move the uploaded image
            if (move_uploaded_file($temp_name, $upload_folder . $img)) {
                // Prepare the image path for database update
                $image_path = $upload_folder . $img;
            } else {
                $img_err = "Sorry, there was an error uploading your file.";
            }
        }
    }
    
    // Check input errors before inserting in database
    if (empty($name_err) && empty($designation_err) && empty($salary_err) && empty($img_err)) {
        // Prepare an update statement
        $sql = "UPDATE employeeReport SET name=?, Designation=?, salary=?, image=? WHERE id=?";
    
        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement as parameters
            // Bind all variables, including $param_image_path, which will be NULL if no image is uploaded
            mysqli_stmt_bind_param($stmt, "ssssi", $param_name, $param_designation, $param_salary, $param_image_path, $param_id);
    
            // Set parameters
            $param_name = $name;
            $param_designation = $designation;
            $param_salary = $salary;
            $param_id = $id;
    
            // Assign $param_image_path only if a new image is uploaded
            if (isset($image_path)) {
                $param_image_path = $image_path;
            }
    
            // Attempt to execute the prepared statement
    
            
    if (mysqli_stmt_execute($stmt)) {
                // Records updated successfully. Redirect to landing page
                header("location: Home.php");
                exit();
            } else {
                echo
     
    "Oops! Something went wrong. Please try again later.";
            }
        }
    
        // Close statement
        mysqli_stmt_close($stmt);
    }
    // Close connection
    mysqli_close($con);
} else{
    // Check existence of id parameter before processing further
    if(isset($_GET["id"]) && !empty(trim($_GET["id"]))){
        // Get URL parameter
        $id =  trim($_GET["id"]);
        
        // Prepare a select statement
        $sql = "SELECT * FROM employeeReport WHERE id = ?";
        if($stmt = mysqli_prepare($con, $sql)){
            // Bind variables to the prepared statement as parameters
            mysqli_stmt_bind_param($stmt, "i", $param_id);
            
            // Set parameters
            $param_id = $id;
            
            // Attempt to execute the prepared statement
            if(mysqli_stmt_execute($stmt)){
                $result = mysqli_stmt_get_result($stmt);
    
                if(mysqli_num_rows($result) == 1){
                    /* Fetch result row as an associative array. Since the result set
                    contains only one row, we don't need to use while loop */
                    $row = mysqli_fetch_array($result, MYSQLI_ASSOC);
                    
                    // Retrieve individual field value
                    $name = $row["name"];
                    $designation = $row["Designation"];
                    $salary = $row["salary"];
                } else{
                    // URL doesn't contain valid id. Redirect to error page
                    header("location: error.php");
                    exit();
                }
                
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
        }
        
        // Close statement
        mysqli_stmt_close($stmt);
        
        // Close connection
        mysqli_close($con);
    }  else{
        // URL doesn't contain id parameter. Redirect to error page
        header("location: error.php");
        exit();
    }
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Record</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .wrapper{
            width: 600px;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="mt-5">Update Record</h2>
                    <p>Please edit the input values and submit to update the employee record.</p>
                    <form action="<?php echo htmlspecialchars(basename($_SERVER['REQUEST_URI'])); ?>" method="post">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Designation</label>
                            <textarea name="designation" class="form-control <?php echo (!empty($designation_err)) ? 'is-invalid' : ''; ?>"><?php echo $designation; ?></textarea>
                            <span class="invalid-feedback"><?php echo $designation_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Salary</label>
                            <input type="text" name="salary" class="form-control <?php echo (!empty($salary_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $salary; ?>">
                            <span class="invalid-feedback"><?php echo $salary_err;?></span>
                        </div>

                        <div class="form-group">
                            <label>Image</label>
                            <input type="file" name="img">
                            <?php if (isset($img_err)) echo "<span class='invalid-feedback'>$img_err</span>"; ?>
                        </div>
                        <input type="hidden" name="id" value="<?php echo $id; ?>"/>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="Home.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>