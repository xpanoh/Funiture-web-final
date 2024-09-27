<?php
// Start session
session_start();
$response = array();
if (isset($_SESSION['email']))
{
    $response['exists'] = true;
    $response['value'] = $_SESSION['$email'];
} else 
{
    $response['exists'] = false;
    $response['value'] = null;
}
header('Content-Type: application/json');
$json_response = json_encode($response);
