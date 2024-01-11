<?php
session_start();
// Replace these with your own values
$clientId = '';  // client id
$clientSecret = ''; // client secret
$redirectUri = 'http://ip/process-oauth.php'; // where u want to resdrite


// Check for errors
if (isset($_GET['error'])) {
    header('Location: index.php');
    exit();
}

// Exchange the authorization code for an access token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://discord.com/api/oauth2/token');
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
    'client_id' => $clientId,
    'client_secret' => $clientSecret,
    'code' => $_GET['code'],
    'grant_type' => 'authorization_code',
    'redirect_uri' => $redirectUri,
    'scope' => 'identify' // Replace with the scopes your bot needs
]));

curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    exit();
}

curl_close($ch);

// Debug: Print the response data
echo 'Response Data: ' . $response . '<br>';

// Parse the response
$responseArray = json_decode($response, true);

// Check for Discord API errors
if (isset($responseArray['error'])) {
    echo 'Discord API error: ' . $responseArray['error'];
    // Handle the error, redirect to an error page, or take appropriate action
    exit();
}

$accessToken = $responseArray['access_token'];
$_SESSION['access_token'] = $accessToken;

// Make API requests with the access token
// For example:
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, 'https://discord.com/api/users/@me');
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'Authorization: Bearer ' . $accessToken
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    echo 'Curl error: ' . curl_error($ch);
    exit();
}

curl_close($ch);

// Debug: Print the user data response
echo 'User Data Response: ' . $response . '<br>';

// Parse the user response
// ...

// Parse the user response
$user = json_decode($response);

// Convert the user object to an associative array
$userArray = json_decode($response, true);

$_SESSION['user'] = $userArray;


// Redirect to the dashboard
header('Location: dashboard.php');

?>
