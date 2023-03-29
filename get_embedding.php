<?php

require_once('config.php');

function embed($text) {
    global $api_key;
    $endpoint = 'https://api.openai.com/v1/embeddings';
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . strval($api_key)
    );
    $data = array(
        'model' => 'text-embedding-ada-002',
        'input' => $text
    );

    $ch = curl_init($endpoint);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    $result = json_decode($response, true);
    $embedding = $result["data"][0]["embedding"];

    return $embedding;
}

$input = json_decode(file_get_contents('php://input'), true);
$text = $input['text'];

try {
    $embedding = embed($text);
    echo json_encode($embedding);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'An error occurred while fetching the embedding.', 'details' => $e->getMessage()]);
}

?>
