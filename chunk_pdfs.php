<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';
use Smalot\PdfParser\Parser;

function chunkText($text, $chunkSize = 500)
{
    $words = str_word_count($text, 1);
    $wordChunks = array_chunk($words, $chunkSize);
    $textChunks = array_map(function ($chunk) {
        return implode(' ', $chunk);
    }, $wordChunks);

    return $textChunks;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
    $pdfFilePath = $_FILES['pdf']['tmp_name'];

    try {
        $parser = new Parser();
        $pdf = $parser->parseFile($pdfFilePath);

        $text = $pdf->getText();
        $textChunks = chunkText($text, 500);

        $jsonChunks = [];
        foreach ($textChunks as $index => $chunk) {
            echo $index;
            $jsonChunks[] = [
                'label' => 'chunk_' . ($index + 1),
                'content' => $chunk
            ];
        }

        header('Content-Type: application/json');
        echo json_encode($jsonChunks);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => 'An error occurred while processing the PDF.', 'details' => $e->getMessage()]);
    }
} else {
    $errorMessage = 'Invalid request: ';
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        $errorMessage .= 'Not a POST request';
    } elseif (!isset($_FILES['pdf'])) {
        $errorMessage .= 'Missing "pdf" key in $_FILES';
    } elseif ($_FILES['pdf']['error'] !== UPLOAD_ERR_OK) {
        $errorMessage .= 'Error with file upload (code: ' . $_FILES['pdf']['error'] . ')';
    } else {
        $errorMessage .= 'Unknown error';
    }

    http_response_code(400);
    echo json_encode(['error' => $errorMessage]);
}
?>
