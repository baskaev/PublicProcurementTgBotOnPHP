<?php
class Tender {
    public $text;
    public $href;
    public $publishDate;
    public $endDate;
    public $price;

    public function __construct($text, $href, $publishDate, $endDate, $price) {
        $this->text = mb_strtolower((string)$text, 'UTF-8');
        $this->href = (string)$href;
        $this->publishDate = (string)$publishDate;
        $this->endDate = (string)$endDate;
        $this->price = (string)$price;
    }
}


function getTenders(){ // нужно будет переделать по типу getTendersAlania
    $json_regions = file_get_contents('regions.json');
    $regions = json_decode($json_regions, true);

    foreach ($regions as $region) {
        // Проходимся по содержимому архива и обрабатываем XML файлы
        $xml_files = glob('zakupki/'.$region.'*.xml');
        $tenders = array();
        foreach ($xml_files as $xml_file) {
            $xml = simplexml_load_file($xml_file);
            $purchaseObjectInfo= $xml->xpath('//ns9:purchaseObjectInfo');
            
            if (!empty($purchaseObjectInfo)){
                $text = (string)$purchaseObjectInfo;
                $text = mb_strtolower($text, 'UTF-8');
                $href = $xml->xpath('//ns9:href')[0];
                $href = (string)$href;
                $publishDate = $xml->xpath('//ns9:startDT')[0];
                $publishDate = (string)$publishDate;
                $endDate = $xml->xpath('//ns9:endDT')[0];
                $endDate = (string)$endDate;
                $price = $xml->xpath('//ns9:maxPrice')[0];
                $price = (string)$price;
                $newTender = new Tender($text, $href, $publishDate, $endDate, $price);
                $tenders[] = $newTender;
            }
        }
        $jsonTenders = json_encode($tenders, JSON_PRETTY_PRINT);
        // Указываем путь к файлу
        $filePath = 'zakupkiJSON/'.$region.'.json';
        // Сохраняем JSON в файл, если файл уже существует, он будет перезаписан
        file_put_contents($filePath, $jsonTenders);
    }
    
}


// вот тут была проблема с юникодом но 
// json_encode($tenders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
// помогло

// эта штука создает json по регионам, конкретно тут по Алании а еще создает json с обновлениями
// по регионам, тут конкретно Алания пока 
function getTendersAlania(){
    $tenders = array();
    $region ="Severnaja_Osetija-Alanija_Resp";

    $oldJson = file_get_contents('zakupkiJSON/'.$region.'.json');
    $oldJson = json_decode($oldJson);
    // Создание ассоциативного массива, используя 'href' в качестве ключа для старого JSON
    $oldArray = [];
    foreach ($oldJson as $item) {
        $oldArray[$item->href] = $item;
    }

    // Проходимся по содержимому архива и обрабатываем XML файлы
    $xml_files = glob('zakupki/'.$region.'/currMonthXML/**/*.xml');
    foreach ($xml_files as $xml_file) {
        $xml = simplexml_load_file($xml_file);
        $purchaseObjectInfo= $xml->xpath('//ns9:purchaseObjectInfo');
        //echo mb_strtolower((string)$purchaseObjectInfo[0], 'UTF-8');
        if (!empty($purchaseObjectInfo)){
            $text = mb_strtolower((string)$purchaseObjectInfo[0], 'UTF-8');
            // echo $text;
            // echo "<br> <br>";
            $href = $xml->xpath('//ns9:href')[0];
            $href = (string)$href;
            $publishDate = $xml->xpath('//ns9:startDT')[0];
            $publishDate = (string)$publishDate;
            $endDate = $xml->xpath('//ns9:endDT')[0];
            $endDate = (string)$endDate;
            $price = $xml->xpath('//ns9:maxPrice')[0];
            $price = (string)$price;
            $newTender = new Tender($text, $href, $publishDate, $endDate, $price);
            $tenders[] = $newTender;
        }
        $purchaseObjectInfo = array();
    }
    // Проходимся по содержимому архива и обрабатываем XML файлы
    $xml_files = glob('zakupki/'.$region.'/prevMonthXML/**/*.xml');
    foreach ($xml_files as $xml_file) {
        $xml = simplexml_load_file($xml_file);
        $purchaseObjectInfo= $xml->xpath('//ns9:purchaseObjectInfo');
        
        if (!empty($purchaseObjectInfo)){
            $text = mb_strtolower((string)$purchaseObjectInfo[0], 'UTF-8');
            $href = $xml->xpath('//ns9:href')[0];
            $href = (string)$href;
            $publishDate = $xml->xpath('//ns9:startDT')[0];
            $publishDate = (string)$publishDate;
            $endDate = $xml->xpath('//ns9:endDT')[0];
            $endDate = (string)$endDate;
            $price = $xml->xpath('//ns9:maxPrice')[0];
            $price = (string)$price;
            $newTender = new Tender($text, $href, $publishDate, $endDate, $price);
            $tenders[] = $newTender;
        }
        $purchaseObjectInfo = array();
    }
    // foreach ($tenders as $tender) {
    //     $encoding = mb_detect_encoding($tender->text);
    //     echo "Encoding: " . $encoding . "\n";
    // }
    //echo count($tenders); если интересно то насчитал на момент 30.04.2024 1424 тендера в папках двух последних месяцев
    $jsonTenders = json_encode($tenders, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);//json_encode($tenders, JSON_PRETTY_PRINT);
    // Указываем путь к файлу
    $filePath = 'zakupkiJSON/'.$region.'.json';
    // Сохраняем JSON в файл, если файл уже существует, он будет перезаписан
    file_put_contents($filePath, $jsonTenders);

    // проверка такая - комментируем строку рефреш в дейли
    //  перемещаем пару xml папок из курр - затем обновляем индекс - затем обратно 
    // возвращаем папки обратно в курр - опять обновляем . Это что-бы в модифайд что-то появилось
    // и мы провери обновления 
    // вот тут кстати бывает что меньше чем реально появляется, так как в notification 
    // зачастую бывают оповещения об одном и том же объекте, допустим если что-то
    // изменилось, так как у меня идет отбор по ключу ссылке а она бывает повторяется в разных
    // оповещениях, поэтому бывает такое что объект один а нотификейшенс много, но это никак ни на что
    // не влиет так как меня интересуют именно объекты а они у меня никуда не теряются
    $newItems = [];
    foreach ($tenders as $item) {
        if (!isset($oldArray[$item->href])) {
            $newItems[] = $item;
        }
    }

    // Запись новых элементов в новый JSON файл
    file_put_contents('zakupkiJSON/modified/'.$region.'.json', json_encode($newItems, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
