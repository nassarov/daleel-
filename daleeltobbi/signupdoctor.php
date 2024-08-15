<?php
include 'config.php';

define('MAX_LENGTH', 255);

function clean($input, $maxlength)
{
    $input = substr($input, 0, $maxlength);
    
    $input = htmlspecialchars($input, ENT_QUOTES);
    return $input;
}

// Initialize an array to store errors
$errors = array();

// Check if the required data is set
if (isset($_POST["email"], $_POST["password"], $_POST["confirmPassword"])) {
    // Validate and sanitize user input
    $email = clean($_POST["email"], MAX_LENGTH);
    $password = clean($_POST["password"], MAX_LENGTH);
    $confirmPassword = clean($_POST["confirmPassword"], MAX_LENGTH);

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $errors[] = " Passwords do not match.";
    }

    // Check password security conditions
    $passwordPattern = '/^(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*])[a-zA-Z\d!@#$%^&*]{8,}$/';
    if (!preg_match($passwordPattern, $password)) {
        $errors[] = " Password must meet security conditions - 8 characters minimum, 1 uppercase letter, 1 number, and 1 symbol.";
    }

    // Check if the email already exists in the database
    $checkEmailQuery = "SELECT * FROM doctor_acc WHERE email = '$email'";
    $resultCheckEmail = mysqli_query($con, $checkEmailQuery);

    if (mysqli_num_rows($resultCheckEmail) > 0) {
        $errors[] = " Email already exists.";
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Execute the SQL query for doctors
        $insertQuery = "INSERT INTO doctor_acc (email, password) VALUES ('$email', '$hashed_password')";
        $resultInsert = mysqli_query($con, $insertQuery);

        if ($resultInsert) {
            echo '<script>alert("Doctor registered successfully.");
            window.location.replace("doctor_registration.html");</script>';
            exit();
        } else {
            $errors[] = "Error: " . mysqli_error($con);
        }
    }
} else {
    $errors[] = "Invalid request";
}

// Display errors using JavaScript
if (!empty($errors)) {
    echo '<script>alert("' . implode('\n', $errors) . '");</script>';
}

// Close the database connection
mysqli_close($con);
?>
