<?php

// echo '{"success":"OK", "symbol":"BTC", "wert":22222}';

// Besser mit Objekten arbeiten
$jsonobj = new stdClass();
$jsonobj->success = "OK";
$jsonobj->symbol = "BTC";
$jsonobj->wert = 22222;


echo json_encode($jsonobj);

