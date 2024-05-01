<?php 
//require 'sendMessage.php'; уже объявил в sendObnov...  дважды нельзя
$botToken = "7032500028:AAHyW0tn2uUfvRmgEdHW02r9B0Xmgj-Ke-U";
// Функция для отправки запросов к API Telegram
function apiRequest($method, $parameters) {
    $url = "https://api.telegram.org/bot" . $GLOBALS['botToken'] . "/" . $method;

    if ($parameters) {
        $url .= '?' . http_build_query($parameters);
    }

    $response = file_get_contents($url);
    return $response;
}

function sendRequest($message,$chatId ) {
    $messageArr = explode("%", $message);
    // Отвечаем на приветственное сообщение
    if ($message == "/start") {
        sendMessage($chatId, "Привет! Я бот на PHP!");
        $keyboard = [
            'keyboard' => [
                [['text' => '/start']]
            ],
            'resize_keyboard' => true,
            'one_time_keyboard' => true
        ];
        apiRequest("sendMessage", ['chat_id' => $chatId, /*'text' => '', */'reply_markup' => json_encode($keyboard)]);
        $responsetoUser = "ИНСТРУКЦИЯ: \n\nформат запроса команда%регион%запрос : \nsearch%Severnaja_Osetija-Alanija_Resp%\"поставка\" or \"услуг\"  \n\nподписаться на обновления : \nadd%Severnaja_Osetija-Alanija_Resp%\"поставка\" or \"услуг\"   \n\nмои подпики : spisok  \n\n ПОКА РАБОТАЕТ ТОЛЬКО ОДИН РЕГИОН";
        sendMessage($chatId, $responsetoUser);


    } else if (($messageArr[0] == "search") && ($messageArr[1] =="Severnaja_Osetija-Alanija_Resp")) {
        $request_json = BoolSearchTenders($messageArr[1], $messageArr[2] );

        // Декодируем JSON в массив
        $data = json_decode($request_json, true);

        // Проверяем, не пуст ли массив
        if (!empty($data)) {
            sendDocument($chatId,$data); //почему-то телеграмм не 
            // хочет отправлять html файлы, не знаю почему :(
            // sendMessage($chatId, "Для вас обновления по вашему поиску :".$messageArr[2]);
            // foreach ($data as $item){
            //     $messageItem = $item['text'] . $item['href'];
            //     sendMessage($chatId, $messageItem);
            // }
        }else{
            sendMessage($chatId, "Ничего не нашел! :(  ");
        }
    }else if (($messageArr[0] == "add") && ($messageArr[1] =="Severnaja_Osetija-Alanija_Resp")) {
        // Читаем содержимое файла
        $fileContents = file_get_contents('UsersData\userPodpiski.json');
        // Декодируем JSON в массив PHP
        $data = json_decode($fileContents, true);
        // Создаем новый элемент
        $newElement = array(
            "searchTerm" => $messageArr[2],
            "region" => $messageArr[1],
            "chatId" => $chatId // ваш chatId
        );

        // Добавляем новый элемент в массив
        $data[] = $newElement;

        // Кодируем обновленный массив в JSON
        $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);

        // Записываем JSON обратно в файл
        file_put_contents('UsersData\userPodpiski.json', $jsonData);
    } else if ($message == "spisok"){
        $fileContents = file_get_contents('UsersData\userPodpiski.json');
        $data = json_decode($fileContents, true);
        $text = "";
        foreach ($data as $item){
            if ($item['chatId'] == $chatId){
                $text = $text . $item['searchTerm'] . "\n";
            }
        }
        sendMessage($chatId, "Ваши подписки: \n" . $text);
    }else{
        sendMessage($chatId, "такой команды нет");
    }

    // echo "Chat ID последнего сообщения: " . $chatId . "<br>";
    // echo "Текст последнего сообщения: " . $message;
    // echo "<br>";
}