<?php
$host = "localhost";
$username = "markus";
$password = "macfly314";
$dbname = "cryptodata";
$conn = mysqli_connect($host, $username, $password, $dbname);
$last = 0;
$lvv = 0;
if (! $conn) {
    die("Verbindung fehlgeschlagen: " . mysqli_connect_error());
}
$symbol="BTC";
do {
    $lvv ++;
    $url = 'https://min-api.cryptocompare.com/data/v2/histoday';
    if ($last == 0) {
        $parameters = [
            'api_key' => 'e00b85d046d2c39f84d8f3fd5ec7fb0abcf04536116d7ce8a384f7c3ecf85d54',
            'fsym' => $symbol,
            'tsym' => 'USD',
            'limit' => '2'
        ];
    } else {
        $parameters = [
            'api_key' => 'e00b85d046d2c39f84d8f3fd5ec7fb0abcf04536116d7ce8a384f7c3ecf85d54',
            'fsym' => 'BTC',
            'tsym' => 'USD',
            'limit' => '500',
            'toTs' => $last
        ];
    }
    /*
     * $headers = [
     * 'Accepts: application/json',
     * 'X-CMC_PRO_API_KEY: 8d119fb9-d191-4d85-88e8-49074df08f02'
     * ];
     */
    $headers = [
        'Content-Type:application/json',
        'Accepts: application/json'
    ];

    $qs = http_build_query($parameters); // query string encode the parameters
    $request = "{$url}?{$qs}"; // create the request URL

    $curl = curl_init(); // Get cURL resource
                         // Set cURL options
    curl_setopt_array($curl, array(
        CURLOPT_URL => $request, // set the request URL
        CURLOPT_HTTPHEADER => $headers, // set the headers
        CURLOPT_RETURNTRANSFER => 1 // ask for raw response instead of bool
    ));
    print_r("Sende request");
    $response = curl_exec($curl); // Send the request, save the response
                                  // print_r($response);
    $jsonobj = (json_decode($response)); // print json decoded response

    $last = saveToDB($jsonobj, $conn, $last);
    curl_close($curl); // Close request
} while ($last != 0 && $lvv < 5);
mysqli_close($conn);

function saveToDB($crypto_data, $conn, $last)
{
    $savenow = 1;

    if (count($crypto_data->Data->Data) > 0) {
        $last = $crypto_data->Data->Data[0]->time;
        // Daten in Datenbank speichern

        print_r(gmdate("Y-m-d\TH:i:s\Z", $last) . "<br>");

        foreach ($crypto_data->Data->Data as $row) {
            // echo $i++;

            $KursWert = $row->high;
            $DateTimeStamp = $row->time;
            $Umsatz = $row->volumeto;
            $KaufVolumen = 0;
            $VerkaufVolumen = 0;
            $MktCap = 0;
            $Typ = "TAG";

            // $datestr = date("Y-m-d H:i:s", $DateTimeStamp);
            $datestr = gmdate("Y-m-d\TH:i:s\Z", $DateTimeStamp);

            $sql = "INSERT INTO `cryptokurs` (
                                `CoinID`, 
                                `KursWert`,
                                `DateTimeStamp`, 
                                `Umsatz`, 
                                `KaufVolumen`, 
                                `VerkaufVolumen`, 
                                `Typ`, 
                                `MKTCAP`) 
                    VALUES (    '1', 
                                '$KursWert', 
                                '$datestr', 
                                '$Umsatz', 
                                '$KaufVolumen', 
                                '$VerkaufVolumen', 
                                '$Typ', 
                                '$MktCap');";

            // echo "$sql.<br>";
            echo "*";
            if ($savenow) {

                $check = "SELECT * FROM cryptokurs WHERE DateTimeStamp = '$datestr'";
                $result= mysqli_query($conn,$check);
                echo $result->num_rows;
                if ($result->num_rows<1) {
                    if (mysqli_query($conn, $sql)) {
                        // echo "Daten erfolgreich in Datenbank gespeichert.";
                    } else {
                        echo "Fehler beim Speichern der Daten: ";
                    }
                }
            }
        }
    }

return $last;
    // Verbindung zur Datenbank schließen
   }
?>

