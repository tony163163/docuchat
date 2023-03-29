<?php
header('Content-Type: application/json');
require_once('config.php');

$endpoint = 'https://api.openai.com/v1/chat/completions';

$headers = array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . strval($api_key)
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $text = $data['text'];

    $data = array(
            'model' => 'gpt-3.5-turbo',

            'messages' => array(
                array('role' => 'system', 'content' => 
                'Generate five very short questions based on the following text:' . $text),
            ),

            'temperature' => 0,
            'max_tokens' => 150,
            'frequency_penalty' => 0,
            'presence_penalty' => 0
            );

        $ch = curl_init($endpoint);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $complete = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($complete, true)['choices'][0]['message']['content'];
        $questions = array_map(function($question) {
            return preg_replace('/^\d+\.\s*/', '', $question);
        }, explode("\n", trim($result)));

        
        // Check if the generated text has the expected format
        if (count($questions) < 5) {
            $generic_questions = [
                "What is the main idea of the text?",
                "What are the key points mentioned?",
                "What is the author's opinion on the subject?"
            ];
            echo json_encode($generic_questions);
        } else {
            echo json_encode($questions);
        }
    }
?>

