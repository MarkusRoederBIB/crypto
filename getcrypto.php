<?php
$host = "localhost";
$username = "markus";
$password = "macfly314";
$dbname = "cryptodata";
$conn = mysqli_connect($host, $username, $password, $dbname);
$last = 0;
$lvv = 0;
$lvw = 0;
if (! $conn) {
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
}

$coinID = $_GET['coinid'];
if (!$coinID) die ("keinCoinangegeben - nutze ?coinid=123");
$jsonobj = new stdClass();
$jsonobj->success = "OK";
$jsonobj->data = array();

$sqllist = "SELECT * FROM `coininfo` WHERE `CoinID` = $coinID";
$resultListe = mysqli_query($conn, $sqllist);

$zeile = $resultListe->fetch_array(MYSQLI_ASSOC); 
while ( $zeile ) { // && $lvw++<3
    
    $jsonobj->data[] = $zeile;
    
    $zeile = $resultListe->fetch_array(MYSQLI_ASSOC);
}
mysqli_close($conn);
echo json_encode($jsonobj);


