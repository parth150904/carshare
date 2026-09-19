<?php
session_id('test000');
session_start();
$_SESSION['id'] = 1; 
session_write_close();
$ch = curl_init('http://localhost/carshare/user/api_chatbot.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['message' => 'How many rides are booked?']));
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json', 'Cookie: PHPSESSID=test000']);
$result = curl_exec($ch);
echo '::' . $result . '::';

