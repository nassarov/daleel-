<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $drName = isset($_POST['drName']) ? $_POST['drName'] : null;
    $drMajor = isset($_POST['major']) ? $_POST['major'] : null;
    $drPhone = isset($_POST['drPhone']) ? $_POST['drPhone'] : null;
    $drLocation = isset($_POST['drLocation']) ? $_POST['drLocation'] : null;
    $drRegion = isset($_POST['region']) ? $_POST['region'] : null;
    $drProfileName = isset($_FILES['drProfile']) ? basename($_FILES['drProfile']['name']) : null;
    $drProfileSize = isset($_FILES['drProfile']) ? $_FILES['drProfile']['size'] : null;
    $drProfileType = isset($_FILES['drProfile']) ? $_FILES['drProfile']['type'] : null;

    // Check if an image is uploaded
    $uploadsDirectory = 'images/';
    if ($drProfileName) {
        // Check file type
        $allowedTypes = array('image/jpeg', 'image/jpg', 'image/png');
        if (!in_array($drProfileType, $allowedTypes)) {
            echo '<script>alert("Error: Only JPG, JPEG, and PNG images are allowed !!!");window.location.href = "doctor_registration.html";</script>';
            exit;
        }

        // Check iamge size limit
        $maxFileSize = 1 * 1024 * 1024; // 1 MB
        if ($drProfileSize > $maxFileSize) {
            echo '<script>alert("Error: File size exceeds the limit of 1 MB !!!");window.location.href = "doctor_registration.html";</script>';
            exit;
        }

         // Check if the same image already exists in the 'images/' directory

        if (file_exists($uploadsDirectory . $drProfileName)) {
            echo '<script>alert("Error: The same image already exists. Please choose a different image or change its name to your name !!!");window.location.href = "doctor_registration.html";</script>';
            exit;
        }

        $targetPath = $uploadsDirectory . $drProfileName;

        // Move the uploaded profile to the images directory
        if (move_uploaded_file($_FILES['drProfile']['tmp_name'], $targetPath)) {
            // File uploaded successfully
        } else {
            echo '<script>alert("Error uploading file. Please try again !!!");window.location.href = "doctor_registration.html";</script>';
            exit;
        }
    } else {
        // If no file is uploaded, set a default profile image path
        $drProfileName = 'default-image.jpg';
    }

    if (isValidGoogleMapsLink($drLocation)) {
        if ($drMajor !== null && $drRegion !== null) {
            $query = "INSERT INTO doctors (drname, drmajor, drphone, drlocation, drprofile, drregion) VALUES ('$drName', '$drMajor', '$drPhone', '$drLocation', '$drProfileName', '$drRegion')";

            $result = mysqli_query($con, $query);

            if ($result) {
                
                // Display welcome message using JavaScript alert
                echo '<script>alert("Registration successful! Welcome, Dr. ' . $drName . '.");window.location.href = "logindoctor.html";</script>';
                // Redirect to main2.html using JavaScript
               // echo '<script>window.location.href = "main2.html";</script>';
                exit;
            } else {
                echo '<p>Error during registration. Please try again. Error: ' . mysqli_error($con) . '</p>';
            }

            mysqli_close($con);
        } else {
            echo '<p>Error: Doctor major and region cannot be null !!!</p>';
        }
    } else {
        echo '<script>alert("Invalid Google Maps link. Please enter a valid link !!!");window.location.href = "doctor_registration.html";</script>';
    }
}

function isValidGoogleMapsLink($link)
{
    return strpos($link, "https://maps.app.goo.gl/") !== false;
}
?>