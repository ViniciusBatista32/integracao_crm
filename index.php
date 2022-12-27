<?php 

require 'vendor/autoload.php';

//Lê os dados da planilha google.
$client = new \Google_Client();
$client->setApplicationName('Google Sheets and PHP');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->setAuthConfig('credentials.json');
$service = new Google_Service_Sheets($client);
$spreadsheetId = '1561peowbKSAdg4iupvNZ-HTmeSzlx6SYqCkq-NnFeM0';

$range = 'COPAÍBA'; // here we use the name of the Sheet to get all the rows
$response = $service->spreadsheets_values->get($spreadsheetId, $range);
$values = $response->getValues();
var_dump($values);


// REALIZA A REQUISIÇÃO PARA O CV
$url  = 'https://auten.cvcrm.com.br/api/cvio/lead';
$data = ['key' => 'value'];
$ch   = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$result = curl_exec($ch);

curl_close($ch);
