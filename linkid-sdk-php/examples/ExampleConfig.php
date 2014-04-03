<?php

/**
 * Configuration for the example pages, linkID hostname, appname, username/password, ...
 */

$linkIDHost = "192.168.5.14:8443";
// $linkIDHost = "demo.linkid.be";

$linkIDAppName = "demo-test";

$linkIDLanguage = "en";

// username/password
$linkIDWSUsername = "demo-test";
$linkIDWSPassword = "08427E9F-6355-4DE4-B992-B1AB93CEE9D4";

/*
 * linkID authentication context session attribute
 *
 * After a successful authentication with linkID this will hold the returned
 * AuthenticationProtocolContext object which contains the linkID user ID,
 * used authentication device(s) and optionally the returned linkID attributes
 * for the application.
 */
$authnContextParam = "linkID.authnContext";

// UserId to be used in the WS examples
$userId = "2b35dbab-2ba2-403b-8c36-a8399c3af3d5";

?>
