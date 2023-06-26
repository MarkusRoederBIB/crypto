<?php

function buildDailyResponse ($symbol) {
    $url = 'https://min-api.cryptocompare.com/data/v2/histominute';
    
    $parameters = [
        'api_key' => 'e00b85d046d2c39f84d8f3fd5ec7fb0abcf04536116d7ce8a384f7c3ecf85d54',
        'fsym' => $symbol,
        'tsym' => 'USD',
        'limit' => '1440',
        
    ];
    $headers = [
        'Content-Type:application/json',
        'Accepts: application/json'
    ];
    
    $qs = http_build_query($parameters); // query string encode the parameters
    $request = $url.'?'.$qs; // create the request URL
    $curl = curl_init(); // Get cURL resource
    // Set cURL options
    curl_setopt_array($curl, array(
        CURLOPT_URL => $request, // set the request URL
        CURLOPT_HTTPHEADER => $headers, // set the headers
        CURLOPT_RETURNTRANSFER => 1 // ask for raw response instead of bool
    ));
    print_r('Sende request: ');
    $response = curl_exec($curl);                       // Send the request, save the response
    curl_close($curl); // Close request
    
    return $response;
}






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
} else {
    echo "Connection OK";
}

$sqllist = "SELECT * FROM coininfo";
$resultListe = mysqli_query($conn, $sqllist);
echo $resultListe->num_rows . ' Coins sind in der Liste ';
$zeile = $resultListe->fetch_array(MYSQLI_ASSOC); 
while ( $zeile ) { // && $lvw++<3

    $symbol = $zeile["CoinShortcode"];
    $coinID = $zeile["CoinID"];
    echo $symbol . " " . $coinID . "<br>";
    $response = buildDailyResponse($symbol);
    $jsonobj = json_decode($response);                
    if ( $jsonobj!=NULL ) {
        $last = saveToDB($jsonobj, $conn, $last , $coinID);
    }
    
    $zeile = $resultListe->fetch_array(MYSQLI_ASSOC);
}
mysqli_close($conn);









function saveToDB($crypto_data, $conn, $last, $coinID)
{   print_r("CoinID ". $coinID);
    $savenow = 1;

    if (count($crypto_data->Data->Data) > 0) {
        $last = $crypto_data->Data->Data[0]->time;
        // Daten in Datenbank speichern

        // print_r(gmdate("Y-m-d\TH:i:s\Z", $last) . "<br>");

        foreach ($crypto_data->Data->Data as $row) {
            // echo $i++;

            $KursWert = $row->high;
            if ($row->high==0) break;
            $DateTimeStamp = $row->time;
            $Umsatz = $row->volumeto;
            $KaufVolumen = 0;
            $VerkaufVolumen = 0;
            $MktCap = 0;
            $Typ = 'MINUTE';
            $datestr = gmdate('Y-m-d\TH:i:s', $DateTimeStamp);

            $sql = "INSERT INTO `cryptokurs` (
                                `CoinID`, 
                                `KursWert`,
                                `DateTimeStamp`, 
                                `Umsatz`, 
                                `KaufVolumen`, 
                                `VerkaufVolumen`, 
                                `Typ`, 
                                `MKTCAP`) 
                    VALUES (    '$coinID', 
                                '$KursWert', 
                                '$datestr', 
                                '$Umsatz', 
                                '$KaufVolumen', 
                                '$VerkaufVolumen', 
                                '$Typ', 
                                '$MktCap');";

            if (!$savenow) echo "$coinID - $sql<br>";
            
            if ($savenow) {

                // Achtung der Check muss immer auch noch später den Typ überprüfen
                // für die ersten Tests braucht es das noch nicht
                
                $check = "SELECT * FROM cryptokurs WHERE DateTimeStamp = '$datestr' AND CoinID = '$coinID'";
                $result = mysqli_query($conn, $check);
                echo $result->num_rows;
                if ($result->num_rows < 1) {
                    if (mysqli_query($conn, $sql)) {
                        // echo "Daten erfolgreich in Datenbank gespeichert.";
                    } else {
                        echo "Fehler beim Speichern der Daten: <br>";
                    }
                }
            }
        }
    }

    return $last;
    // Verbindung zur Datenbank schließen
}
?>

