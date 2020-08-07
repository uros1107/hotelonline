<?php
    $servername = "localhost";
    $username = "hotelriazor";
    $password = "rjo@512515!";
    $dbname = "hotel_online";
    $conn3 = mysqli_connect($servername, $username, $password, $dbname);
    // Check connection
    if (!$conn3) {
        die("Connection failed: " . mysqli_connect_error());
    }    
    $sql4 = "UPDATE pm_notification SET status=0 WHERE status=1";
    if (mysqli_query($conn3, $sql4)) {
        echo "Record updated successfully";
      } else {
        echo "Error updating record: " . mysqli_error($conn);
      }
    mysqli_close($conn3);
    // return "success";    
?>