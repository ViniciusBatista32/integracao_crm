<?php 

require 'vendor/autoload.php';

$possible_cols = ["nome", "email", "telefone", "moradia", "cpf", "investimento"];
$mandatory_cols = ["nome" => false, "email" => false, "telefone" => false, "moradia" => false];
$empreendimentos = ["copaíba" => 3];
$jump_cols = [];




//INSTANCIA E CONFIGURA DOCUMENTO GOOGLE SHEETS
$client = new \Google_Client();
$client->setApplicationName('Google Sheets and PHP');
$client->setScopes([\Google_Service_Sheets::SPREADSHEETS]);
$client->setAccessType('offline');
$client->setAuthConfig('credentials.json');
$service = new Google_Service_Sheets($client);




// PEGA OS LEADS DA PLANILHA DE PRODUÇÃO
$page = 'COPAÍBA';
$spreadsheetId = '1561peowbKSAdg4iupvNZ-HTmeSzlx6SYqCkq-NnFeM0';
$response = $service->spreadsheets_values->get($spreadsheetId, $page);
$sheet_data = $response->getValues();




$header = [];
$rows = [];

foreach($sheet_data[0] as $idx => $col)
{
    $col = strtolower($col);

    if(!in_array($col, $possible_cols))
        $jump_cols[] = $idx;
    else if(isset($mandatory_cols[$col]))
        $mandatory_cols[$col] = true;
}

// CASO UMA COLUNA OBRIGATÓRIA NÃO FOI ENVIADA, CÓDIGO PARA
if(in_array(false, $mandatory_cols))
    die("COLUNA OBRIGATORIA NAO ENVIADA");

$header = array_shift($sheet_data);

foreach($sheet_data as $row_idx => $row)
{
    $rows[$row_idx] = [];
    var_dump($row);

    foreach($header as $col_idx => $col)
    {
        if(!in_array($col_idx, $jump_cols))
        {
            $val = isset($row[$col_idx]) ? $row[$col_idx] : "";

            if($col == "investimento")
            {
                $col = "idempreendimento";
                $val = $empreendimentos[$val];
            }

            $rows[$row_idx][$col] = $val;

        }
        else
            continue;
    }
}

var_dump($header);
var_dump($rows);


// REALIZA A REQUISIÇÃO PARA O CV
$url  = 'https://auten.cvcrm.com.br/api/cvio/lead';
$data = ['key' => 'value'];
$ch   = curl_init();

curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);

$result = curl_exec($ch);

curl_close($ch);
