<?php
// Получаем обновления от API Telegram
function getUpdates($params) {
    // вставь токен
    $botToken = "";
    
    $upurl = "https://api.telegram.org/bot" . $botToken . "/getUpdates". '?' .http_build_query($params);
    //echo $upurl;
    //echo "<br>";
    $update = file_get_contents($upurl);
    $update = json_decode($update, true);
    return $update;
}
