<?php
require 'getUpdates.php';
require 'sendRequest.php';
$lastUpdateID = 0;
while (true) {
    $params = [
        'offset' => $lastUpdateID + 1
    ];
    $update = getUpdates($params);

    // Проверяем, есть ли обновления
    if (!empty($update['result'])) {
        foreach ($update['result'] as $updateItem) {
            // Получаем идентификатор чата и текст последнего сообщения
            $chatId = $updateItem['message']['chat']['id'];
            $message = $updateItem['message']['text'];
            sendRequest($message, $chatId);
            // Обновляем lastUpdateID
            $lastUpdateID = $updateItem['update_id'];
        }
    } 
}
?>
