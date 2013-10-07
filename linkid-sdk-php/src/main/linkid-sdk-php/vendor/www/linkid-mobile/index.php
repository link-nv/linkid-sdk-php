<?php

session_start();

require_once('../_include.php');

$as = new SimpleSAML_Auth_Simple('example-mobile');

?>

<html>
    <head>

        
        <h1>linkID Mobile POC</h1>
    
    </head>

    <body>

        <?php
   
       if ($as->isAuthenticated()) {

            header("Location: ./loggedin.php");

        } else { 
        
        ?>

        <div class="linkid-login">
            <iframe id="linkid" style="display: none;" class="linkid-frame"></iframe>
        </div>
        <div class="linkid-login-link">
            <a id="linkid-login-link" class="linkid-login btn btn-large btn-danger" data-login-href="https://192.168.5.14/example-mobile/login.php"
                data-mobile-minimal="linkid" data-completion-href="./done">Login met linkID</a>
        </div>

        <?php } ?>

        <div class="linkid-download">
            <a href="https://itunes.apple.com/us/app/linkid-for-mobile/id522371545?mt=8" target="_blank">
                <img src="./img/appstore.png">
            </a>
            <a href="https://play.google.com/store/apps/details?id=net.link.qr" target="_blank">
                <img src="./img/playstore.png">
            </a>
        </div>

        <script type="text/javascript" id="linkid-login-script" src="https://demo.linkid.be/linkid-static/js/linkid.js"></script>
    </body>
</html>

