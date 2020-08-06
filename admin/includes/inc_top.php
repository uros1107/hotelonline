<?php debug_backtrace() || die ("Direct access not permitted"); ?>
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
            <div class="row mb10"><i class="fas fa-fw fa-phone" style="font-size: 21px"></i><b>: +52 123456789</b></div>
            <div class="row">
                <!-- <?php echo "<b>".$_SESSION['user']['login']."</b> (".$_SESSION['user']['type'].")"; ?> -->
                <?php
                    $user_id = $_SESSION['user']['id'];
                    $servername = "localhost";
                    $username = "root";
                    $password = "";
                    $dbname = "hotel-manager";
                    
                    // Create connection
                    $conn1 = mysqli_connect($servername, $username, $password, $dbname);
                    // Check connection
                    if (!$conn1) {
                        die("Connection failed: " . mysqli_connect_error());
                    }                            
                ?>
                <div class="dropdown">
                    <i class="fas fa-fw fa-globe-americas dropbtn" style="font-size: 21px"></i>
                    <div class="dropdown-content">
                        <a id="lang_spa" href="#">Spanish</a>
                        <a id="lang_eng" href="#">English</a>
                    </div> 
                </div>
                <div class="dropdown">
                    <i class="far fa-fw fa-bell dropbtn" style="font-size: 21px"></i>
                    <?php
                        $sql3 = "SELECT message FROM pm_notification WHERE status=1";
                        $result2 = mysqli_query($conn1, $sql3);
                        
                        if (mysqli_num_rows($result2) > 0) {
                            // var_export($row_user);
                            // output data of each row
                            echo "<small class='notification'>". mysqli_num_rows($result2) ."</small>";
                            echo "<div class='dropdown-content'>";
                            while($row_user = $result2->fetch_assoc()) {                             
                            echo "<a href='#' class='dropdown-toggle' data-toggle='dropdown'>
                                        <span class='label label-pill label-danger count' style='border-radius:10px;'></span> 
                                        <span class='glyphicon glyphicon-bell' style='font-size:18px;'></span>" .$row_user['message'].                                         
                                "</a>";                            
                            }
                            echo "</div>";
                        } else {
                            echo "0 results";
                        }
                    ?>

                    <!-- <small class="notification">3</small>
                    <div class="dropdown-content">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill label-danger count" style="border-radius:10px;"></span> <span class="glyphicon glyphicon-bell" style="font-size:18px;"></span></a>
                    </div>  -->
                </div>
                <div class="dropdown">
                    <i class="fas fa-fw fa-user dropbtn" style="font-size: 21px"></i> &nbsp;
                    <div class="dropdown-content">                                              
                        <a href="#"><?php echo "number:"."<b>".$_SESSION['user']['id']."</b>"; ?></a>
                        <a href="#"><?php echo "name:"."<b>".$_SESSION['user']['login']."</b> (".$_SESSION['user']['type'].")"; ?></a>
                        <a href="#"><?php echo "email:"."<b>".$_SESSION['user']['email']."</b>"; ?></a>                        
                        <?php    
                            $sql1 = "SELECT address, city, country, phone FROM pm_user WHERE id=$user_id";
                            $result1 = mysqli_query($conn1, $sql1);
                            
                            if (mysqli_num_rows($result1) > 0) {
                              // output data of each row
                              while($row_user = mysqli_fetch_assoc($result1)) {
                                echo "<a href='#'> address: " .$row_user['address']. "</a>";
                                echo "<a href='#'> city: " .$row_user['city']. "</a>";
                                echo "<a href='#'> country: " .$row_user['country']. "</a>";
                                echo "<a href='#'> phone: " .$row_user['phone']. "</a>";
                              }
                            } else {
                              echo "0 results";
                            }
                            
                            mysqli_close($conn1);
                        ?>                                                                   
                        <a href="<?php echo DOCBASE.ADMIN_FOLDER; ?>/login.php?action=logout"><i class="fas fa-fw fa-power-off dropbtn"></i> <?php echo $texts['LOG_OUT']; ?></a>                                                
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

<script>
    document.getElementById("lang_spa").onclick = function() {
        var element_es = document.body.querySelector("option[value='es.ini']");
        var element_en = document.body.querySelector("option[value='en.ini']");
        console.log(element_es);
        console.log(element_en);
        element_es.setAttribute("selected", "selected");
        element_en.removeAttribute("selected");
        console.log(element_es);
        console.log(element_en);
    };

    document.getElementById("lang_eng").onclick = function() {
        var element_es = document.body.querySelector("option[value='es.ini']");
        var element_en = document.body.querySelector("option[value='en.ini']");
        console.log(element_es);
        console.log(element_en);
        element_en.setAttribute("selected", "selected");
        element_es.removeAttribute("selected");
        console.log(element_es);
        console.log(element_en);
    };
</script>
