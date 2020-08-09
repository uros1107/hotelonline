<?php
    require_once("../../common/config.php");
    
    $conn3 = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    // // Check connection
    if (!$conn3) {
        die("Connection failed: " . mysqli_connect_error());
    }   
    
    if($_POST['flag'] == 0) {
      $notification = "UPDATE pm_notification SET status=0 WHERE status=1";
    }    
    
    if (mysqli_query($conn3, $notification)) {
        echo "Record updated successfully";
    } else {
      echo "Error updating record: " . mysqli_error($conn3);
    }
    mysqli_close($conn3);  
?>