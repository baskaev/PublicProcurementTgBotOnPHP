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

function makeContract(){
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

        // $files = glob('zakupki/*'); // Получаем список файлов в директории zakupki
        // foreach ($files as $file) {
        //     if (is_file($file)) {
        //         unlink($file); // Удаляем каждый файл
        //     }
        // }
    }
    
}
