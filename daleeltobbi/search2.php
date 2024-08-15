<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected disease, region, and doctor name from the AJAX request
    $selectedDisease = isset($_POST['disease']) ? $_POST['disease'] : null;
    $selectedRegion = isset($_POST['region']) ? $_POST['region'] : null;
    $doctorName = isset($_POST['doctorName']) ? $_POST['doctorName'] : null;

    $htmlContent = '';
    $counter = 0;
    $displayedDoctorsCount = 3; // Number of doctors to display initially

    // Use prepared statements to prevent SQL injection
    $stmt = $con->prepare("SELECT * FROM doctors WHERE drname LIKE ? AND drmajor = ? AND drregion = ? ORDER BY drname LIMIT ?");
    $likePattern = "%$doctorName%";

    if (!$stmt) {
        die("Error in statement preparation: " . $con->error);
    }

    if ($selectedDisease && $selectedRegion) {
        $stmt->bind_param("sssi", $likePattern, $selectedDisease, $selectedRegion, $displayedDoctorsCount);
    } elseif ($selectedRegion) {
        $stmt->bind_param("sssi", $likePattern, $selectedDisease, $selectedRegion, $displayedDoctorsCount);
    } elseif ($selectedDisease) {
        $stmt->bind_param("sssi", $likePattern, $selectedDisease, $selectedRegion, $displayedDoctorsCount);
    } else {
        $stmt->bind_param("sssi", $likePattern, $selectedDisease, $selectedRegion, $displayedDoctorsCount);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    while ($row = mysqli_fetch_array($result)) {
        $htmlContent .= '<div class="doctor-box">';
        $htmlContent .= '<div class="doctor-image-container">';
        
        if (isset($row['drprofile'])) {
            $htmlContent .= '<img src="/images/'. $row['drprofile'].' " alt="Doctor Image">';
        } else {
            $htmlContent .= '<img src="/images/default-image.jpg" alt="Default Image">';
        }
        
        $htmlContent .= '</div>';
        $htmlContent .= '<div class="doctor-info">';
        $htmlContent .= '<p> Name:Doctor ' . $row['drname'] . '</p>';
        $htmlContent .= '<p> Major: ' . $row['drmajor'] . '</p>';
        $htmlContent .= '<p>Phone Number: ' . $row['drphone'] . '</p>';
        $htmlContent .= '</div>';
        $htmlContent .= '<div class="doctor-info2">';
        $htmlContent .= '<p>Region: ' . $row['drregion'] . '</p>';
        $htmlContent .= '<p>Location: <button onclick="openLocation(\'' . $row['drlocation'] . '\')" class="location-button"><i class="fas fa-map-marker-alt"></i></button></p>';
        $htmlContent .= '</div>';
        $htmlContent .= '</div>';
        
        $counter++;
    }

    // Check if there are more doctors to fetch
    if ($counter >= $displayedDoctorsCount) {
        $htmlContent .= '<div class="Total"><p>Total Doctors: ' . $counter . '</p></div>';
        $htmlContent .= '<div class="ViewMore" onclick="viewMore()"><p>View More</p></div>';
    } else {
        $htmlContent = '<div class="Total"><p>Total Doctors: ' . $counter . '</p></div>' . $htmlContent;
    }

    echo $htmlContent;

    // Check if no doctors were found and display the counter at the bottom
    if ($counter === 0) {
        echo '<div class="No"><p>No doctor with such name is found !!!</p></div>';
    }
}
?>
