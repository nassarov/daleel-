<?php
define("db_SERVER", "localhost");
define("db_USER", "id21742575_daleeltobbi");
define("db_PASSWORD", "Daleeltobbi@23");
define("db_DBNAME", "id21742575_daleeltobbi");


$con = mysqli_connect(db_SERVER, db_USER, db_PASSWORD, db_DBNAME);
if ($con) {
 //   echo (" Connection done  ");
} else {

    echo ("Error connecting the server " . mysqli_connect_error());
}
?>