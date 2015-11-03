<?php

require_once('../../LinkIDClient.php');
require_once('../ExampleConfig.php');

date_default_timezone_set('UTC'); // needed for parsing dates

$client = new LinkIDClient($linkIDHost, $linkIDWSUsername, $linkIDWSPassword);

/**
 * Fetch themes
 */

$applicationName = "example-mobile";

$themes = $client->getThemes($applicationName);

print("<h2>Themes for " . $applicationName . "</h2>");
print("<ul>");
foreach ($themes->themes as $theme) {
    print("<li>");
    print($theme->name . " (default? " . ($theme->defaultTheme ? "yes" : "no") . ")<br/>");
    print("<ul>");
    print("<li>backgroundColor: " . $theme->backgroundColor . "</li>");
    print("<li>textColor: " . $theme->textColor . "</li>");
    if (isset($theme->logo)) {
        print("<li>logo</li>");
        print("<ul>");
        foreach ($theme->logo as $language => $images) {
            foreach ($images as $image) {
                print("<li>url=" . $image->url . "</li>");
                print("<li>language=" . $image->language . "</li>");
            }
        }
        print("</ul>");
    }
    print("</ul>");
    print("</li>");
}
print("</ul>");

/**
 * Fetch localization
 */

$keys = array("urn:linkid:wallet:coin:coffee", "urn:linkid:wallet:leaseplan");

$localizations = $client->getLocalization($keys);

print("<h2>Localizations</h2>");
print("<ul>");
foreach ($localizations as $localization) {
    print("<li>");
    print("Key: " . $localization->key . " - type: " . $localization->keyType);
    print("<ul>");
    foreach ($localization->values as $language => $value) {
        print("<li>" . $language . ": " . $value);

        print("</li>");
    }
    print("</ul>");
    print("</li>");
}
print("</ul>");


?>
