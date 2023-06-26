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
if (!$coinID) die ("keinCoinangegeben - nutze ?coinID=123");
$jsonobj = new stdClass();
$jsonobj->success = "OK";
$jsonobj->data = array();

$now = getdate();
if ($now["mon"]>9) {$monat= $now["mon"];} else {$monat= "0".$now["mon"];}
if ($now["mday"]>9) {$day= $now["mday"];} else {$day= "0".$now["mday"];}

$DateString= $now["year"].'-'.$monat.'-'.$day.' ';


$sqllist = "SELECT * FROM `cryptokurs` WHERE `CoinID` = $coinID AND `Typ` = 'MINUTE' AND `DateTimeStamp` LIKE '".$DateString."%'";
// echo $sqllist; // Vorsicht beim Entkommentaren können die Daten nocht mehr gelesen werden.
$resultListe = mysqli_query($conn, $sqllist);

$zeile = $resultListe->fetch_array(MYSQLI_ASSOC); 
while ( $zeile ) { // && $lvw++<3
    
    $jsonobj->data[] = $zeile;
    
    $zeile = $resultListe->fetch_array(MYSQLI_ASSOC);
}
mysqli_close($conn);
echo json_encode($jsonobj);


