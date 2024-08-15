<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the selected disease, region, and doctor name from the AJAX request
    $selectedDisease = isset($_POST['disease']) ? $_POST['disease'] : null;
    $selectedRegion = isset($_POST['region']) ? $_POST['region'] : null;
    $doctorName = isset($_POST['doctorName']) ? $_POST['doctorName'] : null;

    $htmlContent = '';
    $counter = 0;

    if ($doctorName) {
        $query = "SELECT * FROM doctors WHERE drname LIKE '%$doctorName%' ORDER BY drname";
    } elseif ($selectedDisease && $selectedRegion) {
        $query = "SELECT * FROM doctors WHERE drmajor = '$selectedDisease' AND drregion = '$selectedRegion' ORDER BY drname";
    } elseif ($selectedRegion) {
        $query = "SELECT * FROM doctors WHERE drregion = '$selectedRegion' ORDER BY drname";
    } elseif ($selectedDisease) {
        $query = "SELECT * FROM doctors WHERE drmajor = '$selectedDisease' ORDER BY drname";
    } else {
        $query = "SELECT * FROM doctors ORDER BY drname";
    }

    $result = mysqli_query($con, $query);

    while ($row = mysqli_fetch_array($result)) {
        $htmlContent .= '<div class="doctor-box">';
        $htmlContent .= '<div class="doctor-image-container">';
        
        if (isset($row['drprofile'])) {
            $htmlContent .= '<img src="/images/'. $row['drprofile'].' " alt="Doctor Image">';
        } else if ($row['drprofile'] == null) {
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

        $htmlContent = '<div class="Total"><p>Total Doctors: ' . $counter . '</p></div>' . $htmlContent;

    
    echo $htmlContent;

    // Check if no doctors were found and display the counter at the bottom
    if ($counter === 0) {
        echo '<div class="No"><p>No doctor with such name is found !!!</p></div>';
        
    }
}
?>
