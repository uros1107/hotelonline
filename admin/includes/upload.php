<?php

require_once("../../common/config.php");
require_once("../../common/setenv.php");
/* Getting file name */
$filename = $_FILES['file']['name'];
$location = 'uploads/'.$filename;
$uploadOk = 1;
$imageFileType = pathinfo($location,PATHINFO_EXTENSION);
$valid_extensions = array("jpg","jpeg","png");
/* Check file extension */
if( !in_array(strtolower($imageFileType),$valid_extensions) ) {
   $uploadOk = 0;
}

if($uploadOk == 0){
   echo 0;
}else{
   /* Upload file */
    if(move_uploaded_file($_FILES['file']['tmp_name'], $location)){
        
        $conn5 = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

        if (!$conn5) {
            die("Connection failed: " . mysqli_connect_error());
        }  
        session_start();        
        $insert = "UPDATE pm_user SET name='".$location."' WHERE id='".$_SESSION['user']['id']."'";
        if (mysqli_query($conn5, $insert)) {
            $_SESSION['IMAGE_PROFILE'] = $location;
        } else {       
        }
        mysqli_close($conn5);

        echo $location;
    }else{
        echo 0;
    }
}
?>