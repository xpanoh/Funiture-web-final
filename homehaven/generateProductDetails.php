<?php

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $itemId = $data['itemId'] ?? '';

    // Assuming you fetch the item's details from your database using $itemId
    // For the sake of example, let's assume we are generating a description for a chair
    $prompt = "Generate a product description for an ergonomic office chair designed for comfort and style.";

    $response = generateProductDetails($prompt);

    if (!empty($response['choices'][0]['text'])) {
        echo json_encode(['success' => true, 'details' => trim($response['choices'][0]['text'])]);
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

function generateProductDetails($prompt) {
    $apiKey = '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'; // Place your OpenAI API key here
    $url = 'https://api.openai.com/v1/completions';

    $data = [
        'model' => 'gpt-4-0613', // Adjust for the model you intend to use
        'prompt' => $prompt,
        'temperature' => 0.7,
        'max_tokens' => 150
    ];

    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer {$apiKey}"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        // Log or echo the error message for debugging
        error_log(curl_error($ch));
        // Respond with an error message
        echo json_encode(['success' => false, 'error' => curl_error($ch)]);
        if (!empty($response['choices'][0]['text'])) {
            $output = ['success' => true, 'details' => trim($response['choices'][0]['text'])];
            echo json_encode($output);
            // For debugging: Directly echo the output for inspection
            error_log(json_encode($output)); // Log it
        } else {
            $output = ['success' => false];
            echo json_encode($output);
            // For debugging: Directly echo the output for inspection
            error_log(json_encode($output)); // Log it
        }
        exit;
        
        exit;
    }
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
error_log("HTTP response code: " . $httpcode);

    curl_close($ch);

    return json_decode($response, true);
}
