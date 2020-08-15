<?php
    require_once("../../common/config.php");
    
    $conn4 = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    // // Check connection
    if (!$conn4) {
        die("Connection failed: " . mysqli_connect_error());
    }           
    
    $firstname = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $login = $_POST['login'];
    $email = $_POST['email'];
    $userpassword = md5($_POST['password']);
    $country = $_POST['country'];
    $address = $_POST['address'];
    $postcode = $_POST['postcode'];
    $city = $_POST['city'];
    $mobile = $_POST['mobile'];
    $phone = $_POST['phone'];
    $user_id = $_POST['user_id'];
     
    // $user_update = "UPDATE pm_user SET password=$user_pw WHERE id=1";
    $user_update = "UPDATE pm_user SET firstname='$firstname', lastname='$lastname', login='$login', email='$email', pass='$userpassword', country='$country', address='$address', postcode='$postcode', city='$city', mobile='$mobile', phone='$phone' WHERE id='$user_id'";
    if (mysqli_query($conn4, $user_update)) {
    // echo "success";
    } else {
    // echo "Error updating record: " . mysqli_error($conn4);
    }
    

    $header  = "From:".SENDER_NAME."\r\n"; 
    $header .= "Content-type: text/html\r\n";
    // $header ="From:".SENDER_NAME;
    $username = $login;
    var_export($username);
    $result2 = "SELECT * FROM pm_email_content WHERE id=5";
    $result_email = $conn4->query($result2);
    if($result_email !==false && $result_email->num_rows > 0) {
        $row_email = $result_email->fetch_assoc();
        $mailContent = $row_email['content'].
        'Username: '.$username.'<br>'.
        'Password: '.$userpassword;
        $subject = $row_email['subject'];
    }
    if(mail($email, $subject, $mailContent, $header) !== false)
    $_SESSION['msg_success'][] = 'A new password has been sent to your e-mail.'; 
    if (mysqli_query($conn4, $result2)) {
    // echo "success";
    } else {
    // echo "Error updating record: " . mysqli_error($conn4);
    }

    mysqli_close($conn4);  
?>