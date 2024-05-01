<?php
//require 'search.php'; в индексе уже добавил поэтому закоментил тут
require 'bot\sendDocument.php';
require 'bot\sendMessage.php';
// потом просто перепишу для всех регионов
function sendObnovleniaToUsersAlania(){
    $region ="Severnaja_Osetija-Alanija_Resp";
    $userPodpiski = file_get_contents('UsersData\userPodpiski.json');
    $userPodpiski = json_decode($userPodpiski, true);


    foreach ($userPodpiski as $podpiska){
        $request_json = BoolSearchModifiedTenders($podpiska['region'], $podpiska['searchTerm'] );

        // Декодируем JSON в массив
        $data = json_decode($request_json, true);

        // Проверяем, не пуст ли массив
        if (!empty($data)) {
            //sendDocument($podpiska['chatId'], json2table($data)); //почему-то телеграмм не 
            // хочет отправлять html файлы, не знаю почему :(
            // попробуем отправить обычными сообщениями!
            sendMessage($podpiska['chatId'], "Для вас обновления по вашему поиску :".$podpiska->searchTerm);
            foreach ($data as $item){
                $message = $item['text'] . $item['href'];
                // echo $podpiska['chatId'];
                // echo "<br>";
                // echo $message;
                sendMessage($podpiska['chatId'], $message);
            }
            

            //echo("ДА");
        }
        
    }

}

function json2table($jsonData){
    // Преобразуем JSON в массив
    $data = $jsonData;

    // Создаем таблицу HTML
    $tableHtml = '<table border="1">
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Age</th>
                    </tr>';

    foreach ($data as $row) {
        $tableHtml .= '<tr>';
        $tableHtml .= '<td>' . $row["text"] . '</td>';
        $tableHtml .= '<td>' . $row["href"] . '</td>';
        $tableHtml .= '<td>' . $row["publishDate"] . '</td>';
        $tableHtml .= '<td>' . $row["endDate"] . '</td>';
        $tableHtml .= '<td>' . $row["price"] . '</td>';
        $tableHtml .= '</tr>';
    }

    $tableHtml .= '</table>';

    // Создаем временный HTML файл
    $tempHtmlFile = tempnam(sys_get_temp_dir(), 'html');
    file_put_contents($tempHtmlFile, $tableHtml);

    return $tempHtmlFile;
}