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
                <!-- <?php echo "<b>".$_SESSION['user']['login']."</b> (".$_SESSION['user']['type'].")"; ?> -->              
                <div class="dropdown">
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-fw fa-globe-americas dropbtn" style="font-size: 21px"></i>
                    </a>

                    <div class="dropdown-menu language" aria-labelledby="dropdownMenuLink">
                        <table style="width:100%">
                            <tr class="pop-menu">
                <td style="height:unset;text-align:left;padding-left:5px;<?php if($_SESSION['CHANGE_LANG']=='es.ini'){?>background-color:#37aede;<?php }?>" id="lang_spa">
                                    <img src="./images/Mexico.png">
                                    <a class="dropdown-item">Spanish</a>
                                </td>
                            </tr>
                            <tr class="pop-menu">
                                <td style="height:unset;text-align:left;padding-left:5px;<?php if($_SESSION['CHANGE_LANG']=='en.ini'){?>background-color:#37aede;<?php }?>" id="lang_eng">
                                    <img src="./images/US.png" style="width:16px">
                                    <a class="dropdown-item">English(US)</a>
                                </td>
                            </tr>
                        </table>                        
                    </div>
                </div>
                <div class="dropdown" id="notification">
                    <a class="dropdown-toggle" href="#" onclick="removeday()"role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                    
                        <i class="far fa-fw fa-bell dropbtn" style="font-size: 21px"></i>
                    </a>
                    <?php
                        // require_once("../../common/config.php");
                        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
                        // Check connection
                        if (!$conn) {
                          die("Connection failed: " . mysqli_connect_error());
                        }
                        $sql3 = "SELECT message FROM pm_notification WHERE status=1";
                        $result2 = mysqli_query($conn, $sql3);
                        
                        if (mysqli_num_rows($result2) > 0) {
                            echo "<small class='notification' id='notification_number'>". mysqli_num_rows($result2) ."</small>";
                            echo "<div class='dropdown-menu' aria-labelledby='dropdownMenuLink'>";
                            while($row_user = mysqli_fetch_assoc($result2)) {                             
                            echo "<a href='#' class='dropdown-item dropdown-toggle' data-toggle='dropdown' style='text-align: center'>
                                    <span class='label label-pill label-danger count' style='border-radius:10px;'></span> 
                                    <p style='border-bottom: 1px solid'>" .$row_user['message'].                                         
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
                        <i class="fas fa-fw fa-user dropbtn" style="font-size: 21px"></i> &nbsp;
                    </a>
                    <div class="dropdown-menu user-account" aria-labelledby="dropdownMenuLink">                                              
                        <a href="#" class="dropdown-item" ><?php echo "number:"."<b>".$_SESSION['user']['id']."</b>"; ?></a><br>
                        <a href="#" class="dropdown-item" ><?php echo "name:"."<b>".$_SESSION['user']['login']."</b> (".$_SESSION['user']['type'].")"; ?></a><br>
                        <a href="#" class="dropdown-item" ><?php echo "email:"."<b>".$_SESSION['user']['email']."</b>"; ?></a><br>                                             
                        <?php    
                            $sql1 = "SELECT address, city, country, phone FROM pm_user WHERE id=$user_id";
                            $result1 = $db->query($sql1);
                            
                            if ($db->last_row_count() > 0) {
                              while($row_user = $result1->fetch()) {
                                // echo "<a href='#' class='dropdown-item' > address: " .$row_user['address']. "</a><br>";
                                // echo "<a href='#' class='dropdown-item' > city: " .$row_user['city']. "</a><br>";
                                // echo "<a href='#' class='dropdown-item' > country: " .$row_user['country']. "</a><br>";
                                echo "<a href='#' class='dropdown-item' > phone: " .$row_user['phone']. "</a><br>";
                              }
                            } else {
                              echo "0 results";
                            }
                        ?>     
                        <a class="dropdown-item" onclick="openForm()" style="margin-top:10px; border-bottom: 1px solid; font-weight: bolder; font-size: 15px;">My account&nbsp;</a><br>                                                               
                        <a href="<?php echo DOCBASE.ADMIN_FOLDER; ?>/login.php?action=logout" class="dropdown-item"><i class="fas fa-fw fa-power-off dropbtn" style="margin-top: 10px"></i> <?php echo $texts['LOG_OUT']; ?></a>                                                
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
    <?php    
        $user_info = "SELECT *FROM pm_user WHERE id=$user_id";
        $user_account = $db->query($user_info);
        
        if ($db->last_row_count() > 0) {
            while($row_user = $user_account->fetch()) {
    ?>
            <input type="hidden" id="user-id" value="<?php echo $row_user['id']?>">
             <h1>Login</h1>
            <div class="row">
                <label class="col-sm-3" for="email" style="margin-top: 7px"><?php echo ($texts['FIRSTNAME'] . ":"); ?></label>
                <div class="col-sm-9 text-left">
                    <input id="user-firstname" type="text" placeholder="Enter Firstname" name="create_name" value="<?php echo $row_user['firstname']?>">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-3" for="email" style="margin-top: 7px"><?php echo ($texts['LASTNAME'] . ":"); ?></label>
                <div class="col-sm-9 text-left">
                    <input id="user-lastname" type="text" placeholder="Enter Lastname" name="create_name" value="<?php echo $row_user['lastname']?>">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-3" for="email" style="margin-top: 7px"><?php echo ($texts['LOGIN'] . ":"); ?></label>
                <div class="col-sm-9 text-left">
                    <input id="user-login" type="text" placeholder="Enter Login" name="create_email" value="<?php echo $row_user['login']?>">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-3" for="" style="margin-top: 7px"><?php echo ($texts['EMAIL'] . ":"); ?></label>
                <div class="col-sm-9 text-left">
                    <input id="user-email" type="text" placeholder="Enter Email" name="" value="<?php echo $row_user['email']?>">
                </div>
            </div>
            <div class="row">
                <label for="psw" class="col-sm-3" style="margin-top: 7px"><?php echo ($texts['PASSWORD'] . ":"); ?></label>
                <div class="col-sm-9 text-left">
                    <input id="user-password" type="password" placeholder="Enter Password" name="password">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-3" for="" style="margin-top: 7px"><?php echo ($texts['COUNTRY'] . ":"); ?></label>
                <div class="col-sm-9 text-left">
                    <input id="user-country" type="text" placeholder="Enter Country" name="" value="<?php echo $row_user['country']?>">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-3" for="" style="margin-top: 7px"><?php echo ($texts['ADDRESS'] . ":"); ?></label>
                <div class="col-sm-9 text-left">
                    <input id="user-address" type="text" placeholder="Enter Address" name="" value="<?php echo $row_user['address']?>">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-3" for="" style="margin-top: 7px"><?php echo ($texts['POSTCODE'] . ":"); ?></label>
                <div class="col-sm-9 text-left">
                    <input id="user-postcode" type="text" placeholder="Enter PostCode" name="" value="<?php echo $row_user['postcode']?>">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-3" for="" style="margin-top: 7px"><?php echo ($texts['CITY'] . ":"); ?></label>
                <div class="col-sm-9 text-left">
                    <input id="user-city" type="text" placeholder="Enter City" name="" value="<?php echo $row_user['city']?>">
                </div>
            </div>            
            <div class="row">
                <label class="col-sm-3" for="" style="margin-top: 7px"><?php echo ($texts['MOBILE'] . ":"); ?></label>
                <div class="col-sm-9 text-left">
                    <input id="user-mobile" type="text" placeholder="Enter MobileNumber" name="" value="<?php echo $row_user['mobile']?>">
                </div>
            </div>
            <div class="row">
                <label class="col-sm-3" for="" style="margin-top: 7px"><?php echo ($texts['PHONE'] . ":"); ?></label>
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
    <button type="submit" class="btn" id="btn-save">save</button>
    <button type="button" class="btn cancel" onclick="closeForm()">Close</button>
  </div>
</div>

<script>
<<<<<<< HEAD
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
        // location.reload();
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
        // location.reload();
    });
=======
    $("#notification").click(function(){
        
        console.log($("#notification_number").text());
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
            alert(response);
            }
        });
        
    });

<<<<<<< HEAD
    $("#btn-save").click(function(){
=======
            $.ajax
            ({
                url: '<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/select.php',
                type : "POST",
                cache : false,
                data : 0,
                success: function(response)
                {
                    // alert(response);
                }
            });
>>>>>>> 649e4251bc8708a1fe44d0416f467c8c30bb7d9d
        
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
        var flag = 1;
        $.ajax
        ({
            url: '<?php echo DOCBASE.ADMIN_FOLDER; ?>/includes/update.php',
            type : "POST",
            cache : false,
            data : "firstname=" + firstname+ "&lastname=" + lastname+ "&login=" + login+ "&email=" + email+ "&password=" + password+ "&country=" + country+ "&address=" + address+ "&postcode=" + postcode+ "&city=" + city+ "&mobile=" + mobile+ "&phone=" + phone+ "&user_id=" + user_id,
            success: function(response)
            {
            alert(response);
            }
        });    
    });

        

    function openForm() {
        document.getElementById("myForm").style.display = "block";
    }

    function closeForm() {
        document.getElementById("myForm").style.display = "none";
    }
    document.getElementById("lang_spa").onclick = function() {
        <?php $config_tmp['admin_lang_file'] = "es.ini";?>
        console.log("<?php echo $config_tmp['admin_lang_file'];?>");
        location.reload();
    };

    document.getElementById("lang_eng").onclick = function() {
        <?php $config_tmp['admin_lang_file'] = "en.ini";?>
        console.log("<?php echo $config_tmp['admin_lang_file'];?>");
        location.reload();
    };
>>>>>>> 50017cce17711ab2a5f1d7e285f094cd163ec84a
   
</script>
