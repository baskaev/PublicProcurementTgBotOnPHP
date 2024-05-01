<?php
function SearchTenders($region, $searchTerm){
    $searchTerm = mb_strtolower($searchTerm, 'UTF-8');
    $json_Tenders = file_get_contents('zakupkiJSON/'. $region . '.json');
    $tenders = json_decode($json_Tenders, true);
    $findedtenders = array();
    foreach ($tenders as $tender) {
        if (strpos($tender['text'], $searchTerm) !== false) {
            $findedtenders[] = $tender;
        }
    }
    $jsonTenders = json_encode($findedtenders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//json_encode($tenders, JSON_PRETTY_PRINT);
    return $jsonTenders;
}

// правила поиска - not это !, and и or нижним регистром 
// валидацию потом напишу (на правлильную поисковый запрос), это не трудно
function BoolSearchTenders($region, $searchTerm){
    $searchTerm = mb_strtolower($searchTerm, 'UTF-8');
    $json_Tenders = file_get_contents('zakupkiJSON/'. $region . '.json');
    $tenders = json_decode($json_Tenders, true);
    $findedtenders = array();
    foreach ($tenders as $tender) {
        if (testBool($searchTerm, $tender['text'])) {
            $findedtenders[] = $tender;
        }
    }
    $jsonTenders = json_encode($findedtenders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//json_encode($tenders, JSON_PRETTY_PRINT);
    return $jsonTenders;
}
function BoolSearchModifiedTenders($region, $searchTerm){
    $searchTerm = mb_strtolower($searchTerm, 'UTF-8');
    $json_Tenders = file_get_contents('zakupkiJSON/modified/'. $region . '.json');
    $tenders = json_decode($json_Tenders, true);
    $findedtenders = array();
    foreach ($tenders as $tender) {
        if (testBool($searchTerm, $tender['text'])) {
            $findedtenders[] = $tender;
        }
    }
    $jsonTenders = json_encode($findedtenders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//json_encode($tenders, JSON_PRETTY_PRINT);
    return $jsonTenders;
}

// Ваша функция для проверки наличия словосочетания
function checkPhrase($text, $phrase) {
    return strpos($text, $phrase) !== false;
}

function testBool($zapros, $tender){
    // Разбиваем строку на словосочетания в кавычках
    preg_match_all('/"(.*?)"/', $zapros, $matches);

    // Проходим по каждому словосочетанию в кавычках
    foreach ($matches[1] as $phrase) {
        // Применяем вашу функцию к словосочетанию
        $result = checkPhrase($tender, $phrase);
        // Заменяем исходное словосочетание на результат функции
        $zapros = str_replace("\"$phrase\"", $result ? 'true' : 'false', $zapros);
    }
    return eval("return $zapros;");
}
// Ваша исходная строка





