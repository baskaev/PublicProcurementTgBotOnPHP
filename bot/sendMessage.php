<?php
// Функция для отправки сообщения через API Telegram
function sendMessage($chatId, $message) {
    $botToken = "7032500028:AAHyW0tn2uUfvRmgEdHW02r9B0Xmgj-Ke-U";
    $url = "https://api.telegram.org/bot" . $botToken . "/sendMessage";
    $data = array(
        "chat_id" => $chatId,
        "text" => $message
    );
    $options = array(
        "http" => array(
            "method" => "POST",
            "header" => "Content-Type: application/json",
            "content" => json_encode($data)
        )
    );
    $context = stream_context_create($options);
    $result = file_get_contents($url, false, $context);
    //$result = fopen($url, 'r', false, $context);
    return $result;

    // $url = $url . "?" . http_build_query($data);
    // $response = json_decode(file_get_contents($url), JSON_OBJECT_AS_ARRAY);
    // var_dump($response);
}