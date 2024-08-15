<?php
include 'config.php';

function clean($input, $maxlength)
{
    $input = substr($input, 0, $maxlength);
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); // Use htmlspecialchars to prevent XSS
    return $input;
}

$email = isset($_POST['email']) ? clean($_POST['email'], 255) : '';
$password = isset($_POST['password']) ? clean($_POST['password'], 255) : '';

// Initialize an array to store errors
$errors = array();

// Check if the email is not empty
if (!empty($email) && !empty($password)) {
    // Check if the email and password match in the doctor_acc table
    $sql = "SELECT * FROM doctor_acc WHERE email='$email'";
    $result = $con->query($sql);

    if ($result && $result->num_rows > 0) {
        // Fetch the user's data
        $row = $result->fetch_assoc();
        $hashed_password = $row['password'];

        // Verify the provided password against the hashed password in the database
        if (password_verify($password, $hashed_password)) {
            // Passwords match, user is authenticated
            header("Location: main2.html");
            exit();
        } else {
            // Passwords do not match
            $errors[] = "Invalid password. Please try again.";
        }
    } else {
        // Email not found in the doctor_acc table
        $errors[] = "Invalid email or password. Please try again.";
    }
} else {
    // Email or password is empty
    $errors[] = "Email and password cannot be empty. Please enter valid credentials.";
}

$con->close();

// Display errors using JavaScript
if (!empty($errors)) {
    echo '<script>alert("' . implode('\n', $errors) . '"); window.location.replace("logindoctor.html");</script>';
}
?>
