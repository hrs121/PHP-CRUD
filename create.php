<?php
// Include connection file
require_once "connection.php";

// Define variables and initialize with empty values
$name = $designation = $salary = $img = "";
$name_err = $designation_err = $img_err = $salary_err = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    $input_name = trim($_POST["name"]);
    if (empty($input_name)) {
        $name_err = "Please enter a name.";
    } elseif (!filter_var($input_name, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))) {
        $name_err = "Please enter a valid name.";
    } else {
        $name = $input_name;
    }

    // Validate designation
    $input_designation = trim($_POST["designation"]);
    if (empty($input_designation)) {
        $designation_err = "Please enter a name.";
    } elseif (!filter_var($input_designation, FILTER_VALIDATE_REGEXP, array("options"=>array("regexp"=>"/^[a-zA-Z\s]+$/")))) {
        $designation_err = "Please enter a valid name.";
    } else {
        $designation = $input_designation;
    }

    // Validate salary
    $input_salary = trim($_POST["salary"]);
    if (empty($input_salary)) {
        $salary_err = "Please enter the salary amount.";
    } elseif (!ctype_digit($input_salary)) {
        $salary_err = "Please enter a positive integer value.";
    } else {
        $salary = $input_salary;
    }

    if (isset($_FILES['img'])) {
        $img = $_FILES['img']['name'];
        $temp_name = $_FILES['img']['tmp_name'];
        $upload_folder = 'uploads/';
    
        // Check if image file is an actual image
        $allowed_types = array('jpg', 'jpeg', 'png', 'gif'); // Allow common image types
        $img_extension = strtolower(pathinfo($img, PATHINFO_EXTENSION));
        if (!in_array($img_extension, $allowed_types)) {
            $img_err = "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.";
        } else {
            // Check file size
            if ($_FILES['img']['size'] > 500000) { // Limit to 500 KB
                $img_err = "File size must be less than 500 KB.";
            } else {
                // Move the uploaded image
                if (move_uploaded_file($temp_name, $upload_folder . $img)) {
                    // Image uploaded successfully
                } else {
                    $img_err = "Sorry, there was an error uploading your file.";
                }
            }
        }
    } else {
        $img_err = "Please select an image to upload.";
    }
    
    



    
    // Check input errors before inserting in database
    if (empty($name_err) && empty($designation_err) && empty($salary_err) && empty($img_err)) {
        // Prepare an insert statement with the image column
        $sql = "INSERT INTO employeeReport (name, designation, salary, Image) VALUES (?, ?, ?, ?)";

        if ($stmt = mysqli_prepare($con, $sql)) {
            // Bind variables to the prepared statement
            mysqli_stmt_bind_param($stmt, "ssss", $param_name, $param_address, $param_salary, $param_img);

            // Set parameters, including the image path
            $param_name = $name;
            $param_address = $designation;
            $param_salary = $salary;
            $param_img = $upload_folder . $img;;

            // Attempt to execute the prepared statement
            if (mysqli_stmt_execute($stmt)) {
                // Records created successfully. Redirect to landing page
                header("location: Home.php");
                exit();
            } else {
                echo "Oops! Something went wrong. Please try again later.";
            }
        }

        // Close statement
        mysqli_stmt_close($stmt);
    }

    // Close connection
    mysqli_close($con);
}
?>



 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Create Record</title>
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
                    <h2 class="mt-5">Create Record</h2>
                    <p>Please fill this form and submit to add employee record to the database.</p>
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                        <div class="form-group">
                            <label>Name</label>
                            <input type="text" name="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                            <span class="invalid-feedback"><?php echo $name_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Designation</label>
                            <input type="text" name="designation" class="form-control <?php echo (!empty($designation_err)) ? 'is-invalid' : ''; ?>"><?php echo $designation; ?></input>
                            <span class="invalid-feedback"><?php echo $designation_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Salary</label>
                            <input type="text" name="salary" class="form-control <?php echo (!empty($salary_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $salary; ?>">
                            <span class="invalid-feedback"><?php echo $salary_err;?></span>
                        </div>
                        <div class="form-group">
                            <label>Select Image to Upload</label>
                            <input type="file" name="img" class="form-control <?php echo (!empty($img_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $img; ?>">
                            <span class="invalid-feedback"><?php echo $img_err;?></span>
                        </div>
                        <input type="submit" class="btn btn-primary" value="Submit">
                        <a href="Home.php" class="btn btn-secondary ml-2">Cancel</a>
                    </form>
                </div>
            </div>        
        </div>
    </div>
</body>
</html>