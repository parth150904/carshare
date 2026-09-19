<?php
require '../config.php';
session_start();

// Enable error reporting for debugging, but ensure JSON output
ini_set('display_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', 'bot_error.log');
header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    echo json_encode(['reply' => 'Please log in to use the chat assistant.']);
    exit;
}

$user_id = $_SESSION['id'];

// Load .env
$env_file = '../.env';
if (file_exists($env_file)) {
    $lines = file($env_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        list($name, $value) = explode('=', $line, 2);
        $_ENV[trim($name)] = trim($value);
    }
}

$api_key = isset($_ENV['GEMINI_API_KEY']) ? $_ENV['GEMINI_API_KEY'] : '';
if (empty($api_key) || $api_key == 'your_google_gemini_api_key_here') {
    echo json_encode(['reply' => 'The AI system is offline. The Administrator needs to configure the Gemini API key in the .env file.']);
    exit;
}

// Read input
$input_data = json_decode(file_get_contents('php://input'), true);
$user_message = isset($input_data['message']) ? trim($input_data['message']) : '';

if (empty($user_message)) {
    echo json_encode(['reply' => 'I did not receive a message.']);
    exit;
}

// Initialize chat history if not exists
if (!isset($_SESSION['chat_history'])) {
    $_SESSION['chat_history'] = [];
}

// Append user message to history
$_SESSION['chat_history'][] = [
    'role' => 'user',
    'parts' => [['text' => $user_message]]
];

// Tools (Functions) Definition
$tools = [
    [
        'function_declarations' => [
            [
                'name' => 'get_user_bookings',
                'description' => 'Retrieves the list of rides the user has booked.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => new stdClass(),
                ]
            ],
            [
                'name' => 'cancel_booking',
                'description' => 'Cancels a specific ride booking for the user.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'booking_id' => [
                            'type' => 'INTEGER',
                            'description' => 'The ID of the booking to cancel.'
                        ]
                    ],
                    'required' => ['booking_id']
                ]
            ],
            [
                'name' => 'search_available_rides',
                'description' => 'Searches for available rides between two cities.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'from_city' => ['type' => 'STRING'],
                        'to_city' => ['type' => 'STRING']
                    ],
                    'required' => ['from_city', 'to_city']
                ]
            ],
            [
                'name' => 'book_a_ride',
                'description' => 'Books a ride for the user after they have selected one from the search results.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'ride_id' => ['type' => 'INTEGER'],
                        'seats' => ['type' => 'INTEGER', 'description' => 'Number of seats to book (default 1)']
                    ],
                    'required' => ['ride_id']
                ]
            ],
            [
                'name' => 'add_new_ride',
                'description' => 'Creates a new ride offer for a driver.',
                'parameters' => [
                    'type' => 'OBJECT',
                    'properties' => [
                        'from_city' => ['type' => 'STRING'],
                        'to_city' => ['type' => 'STRING'],
                        'date' => ['type' => 'STRING', 'description' => 'Format YYYY-MM-DD'],
                        'start_time' => ['type' => 'STRING', 'description' => 'Format HH:MM'],
                        'price' => ['type' => 'STRING', 'description' => 'Price per seat'],
                        'capacity' => ['type' => 'INTEGER', 'description' => 'Total seats available']
                    ],
                    'required' => ['from_city', 'to_city', 'date', 'start_time', 'price', 'capacity']
                ]
            ]
        ]
    ]
];

$system_instruction = [
    'parts' => [
        [
            'text' => "You are the CarShare AI Assistant. You help users manage, search, book, and offer rides.
                       LANGUAGE RULE: You are fully multi-lingual. ALWAYS reply in the exact language the user speaks to you (e.g., if they speak Hindi, reply in Hindi; if Gujarati, reply in Gujarati; if English, reply in English).
                       CANCELLATION RULE: If a user asks to cancel a ride, you MUST FIRST use the get_user_bookings tool to find their booking ID. Then ask for confirmation before canceling.
                       BOOKING RULE: If they want to book a ride, use search_available_rides first, present the options, then use book_a_ride when they confirm.
                       OFFERING RULE: If they want to offer/add a ride, use add_new_ride."
        ]
    ]
];

// Define Function Handlers
function handle_get_user_bookings($con, $user_id) {
    $q = mysqli_query($con, "SELECT rb.b_id as booking_id, cr.r_from, cr.r_to, cr.date, cr.start_time, rb.conform as status 
                             FROM ride_book rb 
                             JOIN car_ride cr ON rb.ride_id = cr.r_id 
                             WHERE rb.book_by = '$user_id'");
    $bookings = [];
    while($row = mysqli_fetch_assoc($q)) {
        $bookings[] = $row;
    }
    if (empty($bookings)) {
        return ["status" => "No bookings found for this user."];
    }
    return ["status" => "success", "bookings" => $bookings];
}

function handle_cancel_booking($con, $user_id, $booking_id) {
    // Strictly cast to integer to completely prevent SQL injection from AI hallucinations
    $booking_id = (int)$booking_id;
    $user_id = (int)$user_id;

    // Verify booking belongs to user
    $chk = mysqli_query($con, "SELECT b_id FROM ride_book WHERE b_id='$booking_id' AND book_by='$user_id'");
    if(mysqli_num_rows($chk) == 0) {
        return ["status" => "Error: Booking ID not found or does not belong to the user."];
    }
    
    $del = mysqli_query($con, "DELETE FROM ride_book WHERE b_id='$booking_id' AND book_by='$user_id'");
    if($del) {
        return ["status" => "Successfully canceled booking #$booking_id"];
    } else {
        return ["status" => "Database error while canceling."];
    }
}

function handle_search_available_rides($con, $from_city, $to_city) {
    $from_city = mysqli_real_escape_string($con, $from_city);
    $to_city = mysqli_real_escape_string($con, $to_city);
    $q = mysqli_query($con, "SELECT r_id, r_from, r_to, date, start_time, ppc, seat FROM car_ride WHERE r_from LIKE '%$from_city%' AND r_to LIKE '%$to_city%' LIMIT 5");
    $rides = [];
    while($row = mysqli_fetch_assoc($q)) {
        $rides[] = $row;
    }
    if (empty($rides)) {
        return ["status" => "No rides found matching this route."];
    }
    return ["status" => "success", "available_rides" => $rides];
}

function handle_book_a_ride($con, $user_id, $ride_id, $seats) {
    $ride_id = (int)$ride_id;
    $seats = (int)$seats;
    // Check if ride exists
    $chk = mysqli_query($con, "SELECT r_id FROM car_ride WHERE r_id='$ride_id'");
    if(mysqli_num_rows($chk) == 0) {
        return ["status" => "Error: Ride ID $ride_id not found."];
    }
    $ins = mysqli_query($con, "INSERT INTO ride_book (book_by, ride_id, person, conform) VALUES ('$user_id', '$ride_id', '$seats', 'No')");
    if($ins) {
        return ["status" => "Successfully booked ride #$ride_id for $seats seats!"];
    } else {
        return ["status" => "Database error while booking."];
    }
}

function handle_add_new_ride($con, $user_id, $from, $to, $date, $time, $price, $capacity) {
    $from = mysqli_real_escape_string($con, $from);
    $to = mysqli_real_escape_string($con, $to);
    $date = mysqli_real_escape_string($con, $date);
    $time = mysqli_real_escape_string($con, $time);
    $price = mysqli_real_escape_string($con, $price);
    $capacity = (int)$capacity;
    
    $city = $from; // using origin as base city
    $ins = mysqli_query($con, "INSERT INTO car_ride (owner, city, r_from, r_via, r_to, ride_type, ppc, seat, start_time, end_time, date) 
                               VALUES ('$user_id', '$city', '$from', 'None', '$to', 'Car', '$price', '$capacity', '$time', 'TBD', '$date')");
    if($ins) {
        $new_id = mysqli_insert_id($con);
        return ["status" => "Successfully created new ride offer! Ride ID: $new_id"];
    } else {
        return ["status" => "Database error while creating ride offer."];
    }
}

function call_gemini($api_key, $history, $tools, $system_instruction) {
    $url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-3.6-flash:generateContent?key=' . $api_key;
    
    $payload = [
        'contents' => $history,
        'systemInstruction' => $system_instruction,
        'tools' => $tools
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    
    $response = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    
    if ($error) {
        return ['error' => $error];
    }
    
    return json_decode($response, true);
}

// 1. First Call to Gemini
$response = call_gemini($api_key, $_SESSION['chat_history'], $tools, $system_instruction);

// Helper to safely extract function calls
$functionCall = null;
if (isset($response['candidates'][0]['content']['parts'])) {
    foreach ($response['candidates'][0]['content']['parts'] as $key => $part) {
        if (isset($part['functionCall'])) {
            if (isset($part['functionCall']['args']) && empty($part['functionCall']['args'])) {
                $response['candidates'][0]['content']['parts'][$key]['functionCall']['args'] = new stdClass();
            }
            $functionCall = $response['candidates'][0]['content']['parts'][$key]['functionCall'];
            break;
        }
    }
}

// 2. Handle Function Call (if any)
if ($functionCall) {
    $fn_name = $functionCall['name'];
    $fn_args = isset($functionCall['args']) ? $functionCall['args'] : [];
    
    // Add the model's exact parts to history
    $_SESSION['chat_history'][] = [
        'role' => 'model',
        'parts' => $response['candidates'][0]['content']['parts']
    ];

    // Execute the local function
    $fn_response_data = [];
    if ($fn_name == 'get_user_bookings') {
        $fn_response_data = handle_get_user_bookings($con, $user_id);
    } elseif ($fn_name == 'cancel_booking') {
        $b_id = isset($fn_args['booking_id']) ? $fn_args['booking_id'] : 0;
        $fn_response_data = handle_cancel_booking($con, $user_id, $b_id);
    } elseif ($fn_name == 'search_available_rides') {
        $from = isset($fn_args['from_city']) ? $fn_args['from_city'] : '';
        $to = isset($fn_args['to_city']) ? $fn_args['to_city'] : '';
        $fn_response_data = handle_search_available_rides($con, $from, $to);
    } elseif ($fn_name == 'book_a_ride') {
        $r_id = isset($fn_args['ride_id']) ? $fn_args['ride_id'] : 0;
        $seats = isset($fn_args['seats']) ? $fn_args['seats'] : 1;
        $fn_response_data = handle_book_a_ride($con, $user_id, $r_id, $seats);
    } elseif ($fn_name == 'add_new_ride') {
        $from = isset($fn_args['from_city']) ? $fn_args['from_city'] : '';
        $to = isset($fn_args['to_city']) ? $fn_args['to_city'] : '';
        $date = isset($fn_args['date']) ? $fn_args['date'] : '';
        $time = isset($fn_args['start_time']) ? $fn_args['start_time'] : '';
        $price = isset($fn_args['price']) ? $fn_args['price'] : '';
        $capacity = isset($fn_args['capacity']) ? $fn_args['capacity'] : 1;
        $fn_response_data = handle_add_new_ride($con, $user_id, $from, $to, $date, $time, $price, $capacity);
    } else {
        $fn_response_data = ["status" => "Unknown function"];
    }

    // Add function response to history
    $_SESSION['chat_history'][] = [
        'role' => 'user', // function responses must be role: user in Gemini API
        'parts' => [
            [
                'functionResponse' => [
                    'name' => $fn_name,
                    'response' => $fn_response_data
                ]
            ]
        ]
    ];

    // 3. Second Call to Gemini with the function results
    $response2 = call_gemini($api_key, $_SESSION['chat_history'], $tools, $system_instruction);
    
    if (isset($response2['error'])) {
        if (isset($response2['error']['code']) && $response2['error']['code'] == 429) {
            $final_text = "I am receiving too many requests right now! Please wait about 1 minute and try asking me again.";
        } else {
            $final_text = 'API Error (Call 2): ' . json_encode($response2['error']);
        }
        $_SESSION['chat_history'] = []; // Clear broken history
    } elseif (isset($response2['candidates'][0]['content']['parts'][0]['text'])) {
        $final_text = $response2['candidates'][0]['content']['parts'][0]['text'];
    }
    
    $_SESSION['chat_history'][] = [
        'role' => 'model',
        'parts' => [['text' => $final_text]]
    ];
    
    echo json_encode(['reply' => $final_text]);
    exit;
}

// 4. Handle Standard Text Reply
$final_text = 'I encountered an error trying to respond.';
if (isset($response['error'])) {
    if (isset($response['error']['code']) && $response['error']['code'] == 429) {
        $final_text = "I am receiving too many requests right now! Please wait about 1 minute and try asking me again.";
    } elseif (is_array($response['error'])) {
        $final_text = 'API Error: ' . json_encode($response['error']);
    } else {
        $final_text = 'API Error: ' . $response['error'];
    }
    $_SESSION['chat_history'] = []; // Clear broken history
} elseif (isset($response['candidates'][0]['content']['parts'][0]['text'])) {
    $final_text = $response['candidates'][0]['content']['parts'][0]['text'];
}

$_SESSION['chat_history'][] = [
    'role' => 'model',
    'parts' => [['text' => $final_text]]
];

echo json_encode(['reply' => $final_text]);
exit;
?>

