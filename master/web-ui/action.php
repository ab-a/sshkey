<?php

function checkValue($json, $vUser, $vKey) {
    foreach($json['keys'] as $key => $value) {
        if ($vUser == $value['username'] && $vKey == $value['key']) {
            return $key;
        }
    }
    return FALSE;
}

function searchEntry($json, $vUser, $vKey) {
    $i = 0;
    foreach($json['keys'] as $key => $value) {
        if ($vUser == $value['username'] && $vKey == $value['key']) {
            return $i;
        }
        $i++;
    }
    return FALSE;
}

$file = file_get_contents('db.json');
$json = @json_decode($file, TRUE);

if(!is_array($json)){ $json = []; }
if(!array_key_exists("keys", $json)){$json["keys"] = ['SSH Key List'];}

$vUser = $_GET["username"];
$vKey = $_GET["key"];
$vStatus = $_GET["status"];
$status = false;

if($vStatus == "enable"){
  $status = true;
}

$vKey = str_replace(array("\n", "\r"), '', $vKey);
$vValue = checkValue($json, $vUser, $vKey);

if ($vValue !== FALSE && $vStatus == "delete") {
    $i = searchEntry($json, $vUser, $vKey);
    array_splice($json["keys"], $i, 1);
} else if ($vValue !== FALSE) {
   $json["keys"][$vValue]["enabled"] = (int)$status;
} else if ($vValue == FALSE && $vStatus == "enable") {
    $obj = array(
        'username'=>$vUser,
        'key'=>$vKey,
        'enabled'=>(int)$status,
    );
    $json["keys"][] = $obj;
}

$data = json_encode($json, JSON_PRETTY_PRINT);

file_put_contents('db.json',$data);
Header("Location: index.html");

?>
