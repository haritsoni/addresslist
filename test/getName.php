<?php
//ini_set('display_errors', 1);
require_once "../nusoap_lib/nusoap.php";
$namespace = "http://localhost/addressbook/lib/addressbook.php";
$client = new nusoap_client($namespace);

$error = $client->getError();
if ($error) {
    echo "<h2>Constructor error</h2><pre>" . $error . "</pre>";
}
$request = array("id" => 2);
$result = $client->call("Contact.getName",$request);
print_r($result);
die("success");
//$result2 = $client->call("getProd", array("category" => "games"));

if ($client->fault) {
    echo "<h2>Fault</h2><pre>";
    print_r($result);
    echo "</pre>";
} else {
    $error = $client->getError();
    if ($error) {
        echo "<h2>Error</h2><pre>" . $error . "</pre>";
    } else {
        echo "<h2>Books</h2><pre>";
        echo $result;
        echo "</pre>";

        echo "<h2>Games</h2><pre>";
        echo $result2;
        echo "</pre>";
    }
}
