<?php
    require_once("../../common/config.php");
    
    $conn3 = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    // // Check connection
    if (!$conn3) {
        die("Connection failed: " . mysqli_connect_error());
    }   
    
    $notification = "SELECT message FROM pm_notification WHERE status !=0";
    $result = mysqli_query($conn3, $notification);
    $data = array();
    while ($row = mysqli_fetch_assoc($result))
    {
        $data[] = $row;
    }
    echo json_encode($data);
    mysqli_close($conn3);  
?>