<?php

require_once('LinkIDLoginConfig.php');

/*
 * linkID Configuration
 */
$linkIDHost = "192.168.5.14:8443";
// $linkIDHost = "demo.linkid.be";

$linkIDAppName = "demo-test";

// language to be used in the iframe
$linkIDLanguage = "en";

// username/password
$linkIDWSUsername = "demo-test";
$linkIDWSPassword = "08427E9F-6355-4DE4-B992-B1AB93CEE9D4";

// location of this page, linkID will post its authentication response back to this location.
$loginPage = "http://localhost/~wvdhaute/linkid-sdk-php/LinkIDLogin.php";

/*
 * linkID authentication context session attribute
 *
 * After a successful authentication with linkID this will hold the returned
 * AuthenticationProtocolContext object which contains the linkID user ID,
 * used authentication device(s) and optionally the returned linkID attributes
 * for the application.
 */
$authnContextParam = "linkID.authnContext";

// creates authentication request and handles incoming authentication responses
handleLinkID($authnContextParam, $linkIDHost, $linkIDAppName, $linkIDLanguage, $loginPage, $linkIDWSUsername, $linkIDWSPassword);


?>