<?php
require_once('config.php');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
use Smalot\PdfParser\Parser;

function embed($text) {
    global $api_key;
    $endpoint = 'https://api.openai.com/v1/embeddings';
    $headers = array(
        'Content-Type: application/json',
        'Authorization: Bearer ' . $api_key
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

function chunkText($text, $chunkSize = 500)
{
    $words = str_word_count($text, 1);
    $wordChunks = array_chunk($words, $chunkSize);
    $textChunks = array_map(function ($chunk) {
        return implode(' ', $chunk);
    }, $wordChunks);

    return $textChunks;
}

function processPdf($pdfFilePath)
{
    $parser = new Parser();
    $pdf = $parser->parseFile($pdfFilePath);
    $text = $pdf->getText();

    $textChunks = chunkText($text, 500);

    $jsonChunks = [];
    foreach ($textChunks as $index => $chunk) {
        $embedding = embed($chunk);

        $jsonChunks[] = [
            'label' => 'chunk_' . ($index + 1),
            'content' => $chunk,
            'embedding' => $embedding
        ];
    }

    return $jsonChunks;
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf'])) {
    if ($_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
        $pdfFilePath = $_FILES['pdf']['tmp_name'];

        // Instantiate Smalot\PdfParser\Parser
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfFilePath);

        // Get the number of pages
        $pages = $pdf->getPages();
        $numPages = count($pages);

        if ($numPages > 25) {
            http_response_code(400);
            echo "The uploaded PDF is too long. It should not exceed 30 pages.";
        } else {
            try {
                $text = processPdf($pdfFilePath);
                echo json_encode($text); // Convert newline characters to HTML <br> tags
            } catch (Exception $e) {
                http_response_code(500);
                echo "An error occurred while processing the PDF.";
            }
        }
    } else {
        http_response_code(400);
        echo "An error occurred during the file upload.";
    }
} else {
    http_response_code(400);
    echo "Invalid request.";
}
?>
