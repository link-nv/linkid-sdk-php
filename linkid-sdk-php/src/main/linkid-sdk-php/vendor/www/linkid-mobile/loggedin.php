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

        if (!$as->isAuthenticated()) {

            header("Location: ./index.php");

        } else {

            $authDataArray = $as->getAuthDataArray();
            $userId        = $authDataArray['saml:sp:NameID']['Value'];
            $attributes    = $as->getAttributes();

            print "<h2>Attributes</h2>";

            print_r($attributes);

            print "<h2>Authentication statements</h2>";

            print_r($authDataArray);

            $authnStatements = $authDataArray['saml:sp:AuthnStatements'];

            print "<br/><br/>";

            print_r($authnStatements);

            print "<h2>Authed with</h2>";

            foreach ($authnStatements as $as) {
                print "Device: " . $as->getAuthnContext() . " @ " . $as->getAuthnInstant() . "<br/>";
            }

            print "<h2>Payment response</h2>";

            print_r($authDataArray['linkID:paymentResponse']);
        }

        ?>

        <p>
            <a href="./logout.php">Logout</a>
        </p>

    </body>
</html>

