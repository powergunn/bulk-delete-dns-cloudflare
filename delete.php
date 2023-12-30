<?php

// Konfigurasi Cloudflare API
$apiKey = 'YOUR_API_KEY';
$email = 'YOUR_EMAIL';
$zoneID = 'YOUR_ZONE_ID';

// Fungsi untuk menghapus DNS record
function deleteAllDNSRecords($apiKey, $email, $zoneID)
{
    $url = "https://api.cloudflare.com/client/v4/zones/$zoneID/dns_records";

    $headers = array(
        'X-Auth-Email: ' . $email,
        'X-Auth-Key: ' . $apiKey,
        'Content-Type: application/json',
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($httpCode == 200) {
        $records = json_decode($response, true)['result'];
        foreach ($records as $record) {
            $recordID = $record['id'];

            // Menghapus DNS record
            $deleteResult = deleteDNSRecord($apiKey, $email, $zoneID, $recordID);

            // Menampilkan hasil penghapusan
            echo "DNS record ({$record['type']}, {$record['name']}) deleted. Response Code: " . $deleteResult['code'] . "\n";
            if ($deleteResult['code'] != 200) {
                echo "Error: " . json_encode($deleteResult['response']) . "\n";
            }
        }
    } else {
        echo "Error retrieving DNS records. Response Code: $httpCode\n";
        echo "Error: " . json_encode(json_decode($response, true)) . "\n";
    }
}

// Fungsi untuk menghapus DNS record
function deleteDNSRecord($apiKey, $email, $zoneID, $recordID)
{
    $url = "https://api.cloudflare.com/client/v4/zones/$zoneID/dns_records/$recordID";

    $headers = array(
        'X-Auth-Email: ' . $email,
        'X-Auth-Key: ' . $apiKey,
        'Content-Type: application/json',
    );

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    return array('code' => $httpCode, 'response' => json_decode($response, true));
}

// Panggil fungsi untuk menghapus semua DNS records
deleteAllDNSRecords($apiKey, $email, $zoneID);

?>
