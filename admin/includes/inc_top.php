<?php 
    debug_backtrace() || die ("Direct access not permitted");     
?>
<!-- Navigation -->
<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
    <div class="navbar-header">
        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
        </button>        
        <a href="<?php echo DOCBASE.ADMIN_FOLDER; ?>/">            
            <img class="logo" src="./images/logo-admin.png">
        </a>
        <div class="pull-right hidden-xs" id="info-header">
            <?php
                $user_id = $_SESSION['user']['id'];   
                $user_phone = "SELECT address, city, country, phone FROM pm_user WHERE id=$user_id";
                $result1 = $db->query($user_phone);
                
                if ($db->last_row_count() > 0) {
                    while($row_user = $result1->fetch()) {
                    echo "<div class='row mb10'><i class='fas fa-fw fa-phone' style='font-size: 21px'></i><b>: " .$row_user['phone']. "</b></div>";                    
                    }
                } else {
                    echo "0 results";
                }                                          
            ?>  
            <div class="row">                              
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-fw fa-globe-americas dropbtn" style="font-size: 21px"></i>
                    </a>

                    <div class="dropdown-menu language" aria-labelledby="dropdownMenuLink">
                        <table style="width:100%">
                            <tr class="pop-menu">
                                <td style="height:unset;text-align:left;padding-left:5px;<?php if($_SESSION['CHANGE_LANG']=='es.ini'){?>background-color:#37aede;<?php }?>" id="lang_spa">
                                    <img src="./images/Mexico.png">
                                    <a class="dropdown-item" id="lang_spa" href="#">Spanish</a>
                                </td>
                            </tr>
                            <tr class="pop-menu">
                                <td style="height:unset;text-align:left;padding-left:5px">
                                    <img src="./images/US.png" style="width:16px">
                                    <a class="dropdown-item" id="lang_eng" href="#">English(US)</a>
                                </td>
                            </tr>
                        </table>                        
                    </div>
                </div>
                <div class="dropdown" id="notification" >
                    <a class="dropdown-toggle" href="#" onclick="removeday()"role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                    
                        <i class="far fa-fw fa-bell dropbtn" style="font-size: 21px"></i>
                    </a>
                    <?php
                        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                        // Check connection
                        if (!$conn) {
                            die("Connection failed: " . mysqli_connect_error());
                        }
                        $sql3 = "SELECT message FROM pm_notification WHERE status !=0";
                        $result2 = $db->query($sql3);
                        
                        if ($db->last_row_count() > 0) {
                            echo "<small class='notification' id='notification_number'>". $db->last_row_count() ."</small>";
                            echo "<div class='dropdown-menu' aria-labelledby='dropdownMenuLink' style='width:200px'>";
                            while($row_user = $result2->fetch()) {                             
                            echo "<a href='#' class='dropdown-item dropdown-toggle' data-toggle='dropdown' style='text-aligh:left'>
                                    <span class='label label-pill label-danger count' style='border-radius:10px;'></span> 
                                    <p style='border-bottom: 1px solid;text-align:left;padding-left:10px'>" .$row_user['message'].                                         
                                "</p></a>";                            
                            }
                            echo "</div>";
                        } else {
                            
                        }
                        mysqli_close($conn);
                    ?> 
                </div>
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                    
                        <img src="<?php if(($_SESSION['IMAGE_PROFILE']) != '') 
                        {echo DOCBASE.ADMIN_FOLDER.'/includes/'.trim($_SESSION['IMAGE_PROFILE'], " ");} 
                        else{ echo DOCBASE.ADMIN_FOLDER.'/includes/uploads/images.png'; }?>" id="img-icon" width="28" height="28" style="border-radius:50%; margin-top: -9px"> &nbsp;
                    </a>
                    <div class="dropdown-menu user-account" aria-labelledby="dropdownMenuLink"> 
                        <div class="row">
                            <div class="col-sm-3" style="margin-top: 5px; padding: 8px">
                                <img src="<?php if(($_SESSION['IMAGE_PROFILE']) != '') 
                                {echo DOCBASE.ADMIN_FOLDER.'/includes/'.trim($_SESSION['IMAGE_PROFILE'], " ");} 
                                else{ echo DOCBASE.ADMIN_FOLDER.'/includes/uploads/images.png'; }?>" id="image-user" width="60" height="60">  
                            </div>  
                            <div class="col-sm-9" style="padding: 3px">                                      
                                <!-- <a href="#" class="dropdown-item" ><?php echo "number:"."<b>".$_SESSION['user']['id']."</b>"; ?></a><br> -->
                                <a href="#" class="dropdown-item user-account-item" ><?php echo $texts['LOGIN'].":"."<b>".$_SESSION['user']['login']."</b>"; ?></a><br>
                                <a href="#" class="dropdown-item user-account-item" ><?php echo $texts['EMAIL'].":"."<b>".$_SESSION['user']['email']."</b>"; ?></a><br> 
                                <a href="#" class="dropdown-item user-account-item" ><?php echo $texts['PHONE'].":"."<b>".$_SESSION['user']['phone']."</b>"; ?></a><br> 
                                <a href="#" class="dropdown-item user-account-item" ><?php echo $texts['TYPE'].":"."<b>".$_SESSION['user']['type']."</b>"; ?></a><br> 
                            </div>  
                        </div> 
                        <div class="row">
                            <div class="col-sm-6" style="margin-top: 5px; text-align: center;">   
                                <a class="dropdown-item" onclick="openForm()" style="margin-top:10px; border-bottom: 1px solid; font-weight: bolder; font-size: 15px; color: white">My account&nbsp;</a><br>                                                               
                            </div>
                            <div class="col-sm-6" style="text-align: center">
                                <a href="<?php echo DOCBASE.ADMIN_FOLDER; ?>/login.php?action=logout" class="dropdown-item" style="color: white"><i class="fas fa-fw fa-power-off dropbtn" style="margin-top: 10px"></i> <?php echo $texts['LOG_OUT']; ?></a>                                                
                            </div>
                        </div>
                    </div> 
                </div>                                                
            </div>            
        </div>
    </div>

    <div class="collapse navbar-collapse navbar-ex1-collapse">
        <ul class="nav navbar-nav side-nav">
            <li>
                <a href="<?php echo DOCBASE.ADMIN_FOLDER; ?>/"<?php if(strpos($_SERVER['SCRIPT_NAME'], ADMIN_FOLDER."/index.php") !== false) echo " class=\"active\""; ?>>
                    <i class="fas fa-fw fa-tachometer-alt"></i> <?php echo $texts['DASHBOARD']; ?>
                </a>
            </li>
            <li class="dropdown">
                <a data-target="#module-menu" data-toggle="collapse" href="#"><i class="fas fa-fw fa-th"></i> <?php echo $texts['MODULES']; ?> <i class="fas fa-fw fa-angle-down"></i></a>
                <ul class="<?php if(array_key_exists($dirname, $indexes)) echo "in"; else echo "collapse"; ?>" role="menu" id="module-menu">
                    <?php
                    foreach($modules as $module){

                        $title = $module->getTitle();
                        $name = $module->getName();
                        $dir = $module->getDir();
                        $icon = $module->getIcon();
                        $link = $dir."/index.php?view=list";
                        
                        if($icon == "") $icon = "puzzle-piece";
                        
                        $classname = ($dirname == $name) ? " class=\"active\"" : "";
                        
                        $rights = $module->getPermissions($_SESSION['user']['type']);
                        
                        if(!in_array("no_access", $rights) && !empty($rights))
                            echo "<li><a href=\"".$link."\"".$classname."><i class=\"fas fa-fw fa-".$icon."\"></i> ".$title."</a></li>";
                    } ?>
                    <!-- <li><a><i class="fas fa-user-friends"></i><b>Guest</b></a></li> -->
                </ul>
            </li>            
            <li><a href="<?php echo DOCBASE; ?>"><i class="fas fa-fw fa-eye"></i> <?php echo $texts['PREVIEW']; ?></a></li>
            <?php
            if($_SESSION['user']['type'] == "administrator"){ ?>
                <li>
                    <a href="<?php echo DOCBASE.ADMIN_FOLDER; ?>/settings.php"<?php if(strpos($_SERVER['SCRIPT_NAME'], "settings.php") !== false) echo " class=\"active\""; ?>>
                        <i class="fas fa-fw fa-cog"></i> <?php echo $texts['SETTINGS']; ?>
                    </a>
                </li>
                <?php
            } ?>
        </ul>
    </div>
</nav>
<div class="form-popup" id="myForm">
    <div class="form-container">
        <h3 class="user-profile-header" style="font-weight: bold; font-size: 22px">My User Profile</h3>
        <div class="preview" style="text-align: center"> 
            <label for="file">           
                <img src="<?php if(($_SESSION['IMAGE_PROFILE']) != '') 
                {echo DOCBASE.ADMIN_FOLDER.'/includes/'.trim($_SESSION['IMAGE_PROFILE'], " ");} 
                else{ echo DOCBASE.ADMIN_FOLDER.'/includes/uploads/images.png'; }?>" id="img" width="120" height="120">            
                <i class="fas fa-edit image-upload" style="font-size: 21px"></i>
            </label>
        </div>       
        <?php    
            $user_info = "SELECT *FROM pm_user WHERE id=$user_id";
            $user_account = $db->query($user_info);
            
            if ($db->last_row_count() > 0) {
                while($row_user = $user_account->fetch()) {
        ?>
        <input type="hidden" id="user-id" value="<?php echo $row_user['id']?>">            
        <div class="row" style="padding-top: 10px">
            <label class="col-sm-3 user-profile-label" for="email" style="margin-top: 7px"><?php echo ($texts['FIRSTNAME'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-firstname" type="text" placeholder="Enter Firstname" name="create_name" value="<?php echo $row_user['firstname']?>">
            </div>
        </div>
        <div class="row">
            <label class="col-sm-3 user-profile-label" for="email" style="margin-top: 7px"><?php echo ($texts['LASTNAME'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-lastname" type="text" placeholder="Enter Lastname" name="create_name" value="<?php echo $row_user['lastname']?>">
            </div>
        </div>
        <div class="row">
            <label class="col-sm-3 user-profile-label" for="email" style="margin-top: 7px"><?php echo ($texts['LOGIN'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-login" type="text" placeholder="Enter Login" name="create_email" value="<?php echo $row_user['login']?>">
            </div>
        </div>
        <div class="row">
            <label class="col-sm-3 user-profile-label" for="" style="margin-top: 7px"><?php echo ($texts['EMAIL'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-email" type="text" placeholder="Enter Email" name="" value="<?php echo $row_user['email']?>">
            </div>
        </div>
        <div class="row">
            <label for="psw" class="col-sm-3 user-profile-label" style="margin-top: 7px"><?php echo ($texts['PASSWORD'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-password" type="password" placeholder="Enter Password" name="password">
            </div>
        </div>
        <div class="row">
            <label class="col-sm-3 user-profile-label" for="" style="margin-top: 7px"><?php echo ($texts['COUNTRY'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-country" type="text" placeholder="Enter Country" name="" value="<?php echo $row_user['country']?>">
            </div>
        </div>
        <div class="row">
            <label class="col-sm-3 user-profile-label" for="" style="margin-top: 7px"><?php echo ($texts['ADDRESS'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-address" type="text" placeholder="Enter Address" name="" value="<?php echo $row_user['address']?>">
            </div>
        </div>
        <div class="row">
            <label class="col-sm-3 user-profile-label" for="" style="margin-top: 7px"><?php echo ($texts['POSTCODE'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-postcode" type="text" placeholder="Enter PostCode" name="" value="<?php echo $row_user['postcode']?>">
            </div>
        </div>
        <div class="row">
            <label class="col-sm-3 user-profile-label" for="" style="margin-top: 7px"><?php echo ($texts['CITY'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-city" type="text" placeholder="Enter City" name="" value="<?php echo $row_user['city']?>">
            </div>
        </div>            
        <div class="row">
            <label class="col-sm-3 user-profile-label" for="" style="margin-top: 7px"><?php echo ($texts['MOBILE'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-mobile" type="text" placeholder="Enter MobileNumber" name="" value="<?php echo $row_user['mobile']?>">
            </div>
        </div>
        <div class="row">
            <label class="col-sm-3 user-profile-label" for="" style="margin-top: 7px"><?php echo ($texts['PHONE'] . ":"); ?></label>
            <div class="col-sm-9 text-left">
                <input id="user-phone" type="text" placeholder="Enter PhoneNumber" name="" value="<?php echo $row_user['phone']?>">
            </div>
        </div>                  
        <?php
            }
            } else {
                echo "0 results";
            }
        ?>

        <form method="post" action="" enctype="multipart/form-data" style="display: none;">        
            <div class="row" style="padding-top: 10px; padding-bottom: 10px;">
                <div class="col-sm-9">
                    <input type="file" id="file" name="file" />
                </div>
                <div class="col-sm-3">
                    <input type="button" class="button" value="Upload" id="but_upload">
                </div>
            </div>
        </form>

        <div class="row" style="padding-top: 5px">
            <div class="col-sm-6">
                <button type="submit" class="btn" id="btn-save" style="border-radius: 5px;">save</button>
            </div>
            <div class="col-sm-6">
                <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
            </div>  
        </div>
    </div>
</div>

<script>
    jQuery(document).ready(function() {
        interval = setInterval("checkNewUpdate()", 4000);
    });

    function checkNewUpdate() {
        $.ajax({
            url: '<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/check_notification.php',
            type : "POST",
            asynchronous : true,
            dataType : 'json',
            success: function(response)
            {        
                // // var data = JSON.parse(response);
                // alert(response[1].message);
            }
        });      
    }

    $("#notification").click(function(){
        $("#notification_number").text(0);
        var flag = 0;

        $.ajax
        ({
            url: '<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/select.php',
            type : "POST",
            cache : false,
            data : "flag=" + flag,
            success: function(response)
            {        
                
            }
        });        
    });

    $("#lang_spa").on("click", function() {
        $.ajax({
            url: '<?php echo DOCBASE.ADMIN_FOLDER;?>/includes/change_lang.php',
            type:'post',
            data: {
                lang : 'es.ini',
                check_id : 1
            },
            success: function(res){
               location.reload();
            }

        });
    });

    $("#lang_eng").on("click", function() {
        $.ajax({
            url: '<?php echo DOCBASE.ADMIN_FOLDER;?>/includes/change_lang.php',
            type:'post',
            data: {
                lang : 'en.ini',
                check_id : 2
            },
            success: function(res){
               location.reload();
            }

        });
    });

    $("#file").change(function(){

        var fd = new FormData();
        var files = $('#file')[0].files[0];
        fd.append('file',files);

        $.ajax({
            url: '<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/upload.php',
            type: 'post',
            data: fd,
            contentType: false,
            processData: false,
            success: function(response){
                if(response != 0){                        
                    var res = response.split(" ").join("");
                    $("#img").attr("src", '<?php echo DOCBASE.ADMIN_FOLDER;?>' + '/includes/' + res);
                    $("#img-icon").attr("src", '<?php echo DOCBASE.ADMIN_FOLDER;?>' + '/includes/' + res);
                    $("#image-user").attr("src", '<?php echo DOCBASE.ADMIN_FOLDER;?>' + '/includes/' + res); 
                    $(".preview img").show(); // Display image element
                }else{
                    alert('file not uploaded');
                }
            },
        });
    });
    
    $("#btn-save").click(function(){
        
        console.log($("#user-id").val());
        var user_id = $("#user-id").val();
        var firstname = $("#user-firstname").val();
        var lastname = $("#user-lastname").val();
        var login = $("#user-login").val();
        var email = $("#user-email").val();
        var password = $("#user-password").val();
        var country = $("#user-country").val();
        var address = $("#user-address").val();
        var postcode = $("#user-postcode").val();
        var city = $("#user-city").val();
        var mobile = $("#user-mobile").val();
        var phone = $("#user-phone").val();
        $.ajax
        (
            {
            url: '<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/update.php',
            type : "POST",
            cache : false,
            data : "firstname=" + firstname+ "&lastname=" + lastname+ "&login=" + login+ "&email=" + email+ "&password=" + password+ "&country=" + country+ "&address=" + address+ "&postcode=" + postcode+ "&city=" + city+ "&mobile=" + mobile+ "&phone=" + phone+ "&user_id=" + user_id,
            success: function(response)
                {
                    location.reload();
                    <?php $_SESSION['msg_success'][] = 'A new password has been sent to your e-mail.';?>

                }
            }
        );    
    });   

    function openForm() {
        document.getElementById("myForm").style.display = "block";
    }

    function closeForm() {
        document.getElementById("myForm").style.display = "none";
    }    
   
</script>
