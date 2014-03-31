<?php

require_once('LinkIDAuthnContext.php');

if (!isset($_SESSION)) {
    session_start();
}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" >
    <head>
        <title>linkID Mobile Demo</title>
    </head>

    <body>

<?php

$authnContextParam = "linkID.authnContext";
$authnContext = $_SESSION[$authnContextParam];

print("<h2>User: " . $authnContext->userId . "</h2>");

print("<h3>Attributes</h3>");
print("<p>");
    print_r($authnContext->attributes);
print("</p>");


print("<h3>Payment response</h3>");
print("<p>");
print_r($authnContext->paymentResponse);
print("</p>");


?>

<a href="logout.php">Logout</a>

    </body>
</html>