s<?php
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


$sqllist = "SELECT * FROM `cryptokurs` WHERE `CoinID` = $coinID";
$resultListe = mysqli_query($conn, $sqllist);
echo $resultListe->num_rows . ' Coins sind in der Liste ';
$zeile = $resultListe->fetch_array(MYSQLI_ASSOC); 
while ( $zeile ) { // && $lvw++<3

   
    if ($result2->num_rows>0) {
        
    
    
 
    $zeile = $resultListe->fetch_array(MYSQLI_ASSOC);
}
mysqli_close($conn);
