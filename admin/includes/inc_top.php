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
                    <a class="dropdown-toggle" href="#" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-fw fa-globe-americas dropbtn" style="font-size: 21px"></i>
                    </a>

                    <div class="dropdown-menu language" aria-labelledby="dropdownMenuLink">
                        <a class="dropdown-item" id="lang_spa" href="#" style='text-align: center; border-right: 1px solid'>Spanish</a>
                        <a class="dropdown-item" id="lang_eng" href="#">English</a>                        
                    </div>
                </div>
                <div class="dropdown" id="notification">
                    <a class="dropdown-toggle" href="#" onclick="removeday()"role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">                    
                        <i class="far fa-fw fa-bell dropbtn" style="font-size: 21px"></i>
                    </a>
                    <?php
                        $sql3 = "SELECT message FROM pm_notification WHERE status=1";
                        $result2 = mysqli_query($conn1, $sql3);
                        
                        if (mysqli_num_rows($result2) > 0) {
                            echo "<small class='notification' id='notification_number'>". mysqli_num_rows($result2) ."</small>";
                            echo "<div class='dropdown-menu' aria-labelledby='dropdownMenuLink'>";
                            while($row_user = $result2->fetch_assoc()) {                             
                            echo "<a href='#' class='dropdown-item dropdown-toggle' data-toggle='dropdown' style='text-align: center'>
                                    <span class='label label-pill label-danger count' style='border-radius:10px;'></span> 
                                    <p style='border-bottom: 1px solid'>" .$row_user['message'].                                         
                                "</p></a>";                            
                            }
                            echo "</div>";
                        } else {
                            
                        }
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
                        <a class="dropdown-item" data-toggle="modal" data-target="#myModal">My account&nbsp;</a><br>                    
                        <?php    
                            $sql1 = "SELECT address, city, country, phone FROM pm_user WHERE id=$user_id";
                            $result1 = mysqli_query($conn1, $sql1);
                            
                            if (mysqli_num_rows($result1) > 0) {
                              // output data of each row
                              while($row_user = mysqli_fetch_assoc($result1)) {
                                // echo "<a href='#' class='dropdown-item' > address: " .$row_user['address']. "</a><br>";
                                // echo "<a href='#' class='dropdown-item' > city: " .$row_user['city']. "</a><br>";
                                // echo "<a href='#' class='dropdown-item' > country: " .$row_user['country']. "</a><br>";
                                echo "<a href='#' class='dropdown-item' > phone: " .$row_user['phone']. "</a><br>";
                              }
                            } else {
                              echo "0 results";
                            }
                            
                            mysqli_close($conn1);
                        ?>                                                                   
                        <a href="<?php echo DOCBASE.ADMIN_FOLDER; ?>/login.php?action=logout" class="dropdown-item"><i class="fas fa-fw fa-power-off dropbtn"></i> <?php echo $texts['LOG_OUT']; ?></a>                                                
                    </div> 
                </div>                                                
            </div>            
        </div>
    </div>
    <div class="modal fade" id="myModal" role="dialog" style="z-index:100">
        <div class="modal-dialog">        
        <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Modal Header</h4>
                </div>
                <div class="modal-body">
                    <p>Some text in the modal.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
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
    $("#notification").click(function(){
        
            console.log($("#notification_number").text());
            $("#notification_number").text(0);

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
        
    });    
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
