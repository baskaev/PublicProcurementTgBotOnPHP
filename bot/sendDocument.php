<?php

function sendDocument($chat_id, $jsonData){
    $bot_token ="7032500028:AAHyW0tn2uUfvRmgEdHW02r9B0Xmgj-Ke-U";
    $temp_html_file = json2table($jsonData);
    $request_params = [
        'chat_id' => $chat_id,
        'document' =>  new CURLFile($temp_html_file, 'text/html', 'your_filename.html'),
        'caption' => 'ваш HTML файл!'
    ];
    $request_url = "https://api.telegram.org/bot".$bot_token."/sendDocument";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $request_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $request_params);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);
    // if ($response) {
    //     echo "Файл успешно отправлен!";
    // } else {
    //     echo "Ошибка при отправке файла.";
    // }
}





// уже есть в сендОбновления
// function json2table($jsonData){
//     // Преобразуем JSON в массив
//     $data = $jsonData;

//     // Создаем таблицу HTML
//     $tableHtml = '<table border="1">
//                     <tr>
//                         <th>ID</th>
//                         <th>Name</th>
//                         <th>Age</th>
//                     </tr>';

//     foreach ($data as $row) {
//         $tableHtml .= '<tr>';
//         $tableHtml .= '<td>' . $row["text"] . '</td>';
//         $tableHtml .= '<td>' . $row["href"] . '</td>';
//         $tableHtml .= '<td>' . $row["publishDate"] . '</td>';
//         $tableHtml .= '<td>' . $row["endDate"] . '</td>';
//         $tableHtml .= '<td>' . $row["price"] . '</td>';
//         $tableHtml .= '</tr>';
//     }

//     $tableHtml .= '</table>';

//     // Создаем временный HTML файл
//     $tempHtmlFile = tempnam(sys_get_temp_dir(), 'html');
//     file_put_contents($tempHtmlFile, $tableHtml);

//     return $tempHtmlFile;
// }

// function sendDocument($userId, $file){
//     callTelegramAPI("sendDocument", $userId, $file );
// }
// // Функция для отправки запросов к Telegram Bot API
// function callTelegramAPI($method, $userId, $file ) {
//     $params = array(
//         "chat_id" => $userId,
//         "document" => $file,
//     );
//     $botToken = "7032500028:AAHyW0tn2uUfvRmgEdHW02r9B0Xmgj-Ke-U";
//     $telegramApiUrl = "https://api.telegram.org/bot".$botToken;
//     $url = $telegramApiUrl . "/" . $method;

//     $ch = curl_init();
//     curl_setopt($ch, CURLOPT_URL, $url);
//     curl_setopt($ch, CURLOPT_POST, count($params));
//     curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
//     curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//     $result = curl_exec($ch);
//     curl_close($ch);
//     return $result;
// }







// // Отправка документа пользователю
// $response = callTelegramAPI("sendDocument", array(
//     "chat_id" => $userId,
//     "document" => new CURLFile(realpath($filePath)),
// ));

// // Проверка результата
// if (!$response) {
//     echo "Ошибка отправки файла через Telegram Bot API";
// } else {
//     echo "Файл успешно отправлен пользователю!";
// }
?>
