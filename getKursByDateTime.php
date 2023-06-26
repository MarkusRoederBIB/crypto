<?php
$host = "localhost";
$username = "markus";
$password = "macfly314";
$dbname = "cryptodata";
$conn = mysqli_connect($host, $username, $password, $dbname);
$last = 0;
$lvv = 0;
$lvw = 0;
if (!$conn) {
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
}

$coursedate = $_GET['date'];
$coursetime = $_GET['time'];
$coinid     = $_GET['coinid'];
$typ        = $_GET['typ'];
//$typ = $_GET['typ'];

if (!$coursedate || !$coursetime) die ("Keine Datums-Zeitangabe");
$jsonobj = new stdClass();
$jsonobj->success = "OK";
$jsonobj->data = array();

$sqllist = 'SELECT * FROM `cryptokurs` WHERE `DateTimeStamp` = "'.$coursedate.' '.$coursetime.'" AND `Typ` = "'.$typ .'" AND `CoinID` = '.$coinid;
echo $sqllist;
$resultListe = mysqli_query($conn, $sqllist);

$zeile = $resultListe->fetch_array(MYSQLI_ASSOC); 
while ( $zeile ) { // && $lvw++<3
    
    $jsonobj->data[] = $zeile;
    
    $zeile = $resultListe->fetch_array(MYSQLI_ASSOC);
}
mysqli_close($conn);
echo json_encode($jsonobj);