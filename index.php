<?php



$url = 'https://min-api.cryptocompare.com/data/top/totalvolfull';
$parameters = [
    'api_key' => 'e00b85d046d2c39f84d8f3fd5ec7fb0abcf04536116d7ce8a384f7c3ecf85d54',
    'tsym' => 'USD',
    'limit' => '100'
];
/*
$headers = [
    'Accepts: application/json',
    'X-CMC_PRO_API_KEY: 8d119fb9-d191-4d85-88e8-49074df08f02'
];
*/
$headers = [
    'Content-Type:application/json',
    'Accepts: application/json',
];    

$qs = http_build_query($parameters); // query string encode the parameters
$request = "{$url}?{$qs}"; // create the request URL


$curl = curl_init(); // Get cURL resource
// Set cURL options
curl_setopt_array($curl, array(
    CURLOPT_URL => $request,            // set the request URL
    CURLOPT_HTTPHEADER => $headers,     // set the headers
    CURLOPT_RETURNTRANSFER => 1         // ask for raw response instead of bool
));

$response = curl_exec($curl); // Send the request, save the response
//print_r($response);
 $jsonobj=(json_decode($response)); // print json decoded response
saveToDB($jsonobj);
curl_close($curl); // Close request







function saveToDB ($crypto_data) {
    
    $host = "localhost";
    $username = "markus";
    $password = "macfly314";
    $dbname = "cryptodata";
    
    $conn = mysqli_connect($host, $username, $password, $dbname);
    
    if (!$conn) {
        die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
    }
    
    
    
    
    // Daten in Datenbank speichern
    foreach ($crypto_data->Data as $row) {
       //echo $i++;
        
       $CoinShortcode = $row->CoinInfo->Name;
        $CoinName = $row->CoinInfo->FullName;
        $CoinMenge = $row->RAW->USD->MKTCAP;
       
        date($format, $timestamp);
        
        $sql = "INSERT INTO `cryptokurs` (`CoinID`, `KursWert`, `DateTimeStamp`, `Umsatz`, `KaufVolumen`, `VerkaufVolumen`, `Typ`, `MKTCAP`) VALUES ( '1', '1', '2023-02-14 18:41:50.000000', '1', '1', '1', 'TAG', '1');";
        
        echo $sql;
       
        if (mysqli_query($conn, $sql)) {
            echo "Daten erfolgreich in Datenbank gespeichert.";
        } else {
            echo "Fehler beim Speichern der Daten: " . mysqli_error($conn);
        }
        
    }
    
    // Verbindung zur Datenbank schließen
    mysqli_close($conn);
}
?>

