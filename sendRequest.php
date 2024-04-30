<?php 
require 'C:\ospanel\domains\zakup.local\sendMessage.php';
function sendRequest($message,$chatId ) {
// Отвечаем на приветственное сообщение
    if ($message == "/start") {
        sendMessage($chatId, "Привет! Я бот на PHP. Как дела?");
    } else {
        // Отвечаем на другие сообщения
        sendMessage($chatId, "Вы написали: " . $message);
    }

    // echo "Chat ID последнего сообщения: " . $chatId . "<br>";
    // echo "Текст последнего сообщения: " . $message;
    // echo "<br>";
}