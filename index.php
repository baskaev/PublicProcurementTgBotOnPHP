
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search and Save Links</title>
</head>
<body>
    <!-- <h1>Search and Save Links</h1>
    <form action="index.php" method="post">
        <label for="search">Enter search term:</label>
        <input type="text" id="search" name="search" required>
        <button type="submit">Search</button>
    </form> -->
    <?php
    require 'C:\ospanel\domains\zakup.local\getContractsFromFTPs.php';
    //require 'C:\ospanel\domains\zakup.local\testing.php';
    getContractsforFirstTime();
    // require 'C:\ospanel\domains\zakup.local\bot.php';
    // unlink("zipzip/fuck.php");
    // rmdir("zipzip");


    
    // if ($_SERVER["REQUEST_METHOD"] == "POST") {
    //     $searchTerm = $_POST["search"];
    //     $xmlFiles = glob("notification/*.xml");
    //     $links = [];
    //     foreach ($xmlFiles as $xmlFile) {
    //         $xml = simplexml_load_file($xmlFile);
    //         foreach ($xml->xpath('//ns9:purchaseObjectInfo') as $purchaseObjectInfo) {
    //             $text = (string)$purchaseObjectInfo;

    //             $text = mb_strtolower($text, 'UTF-8');
    //             $searchTerm = mb_strtolower($searchTerm, 'UTF-8');

    //             if (strpos($text, $searchTerm) !== false) {
    //                 $href = $xml->xpath('//ns9:href')[0];
    //                 $links[] = (string)$href;
    //                 break; 
    //             }
    //         }
    //     }

    //     $file = 'ResearchLinks.csv';
    //     file_put_contents($file, '');

    //     $csvFileName = 'ResearchLinks.csv';
    //     $csvFile = fopen($csvFileName, 'w');
    //     foreach ($links as $link) {
    //         fputcsv($csvFile, [$link]);
    //     }
    //     fclose($csvFile);

    //     echo "<p>Links saved to <a href='$csvFileName'>$csvFileName</a></p>";

    //     echo "<h2>Список найденных ссылок:</h2>";
    //     echo "<ul>";
    //     foreach ($links as $link) {
    //         echo "<li><a href=\"$link\">$link</a></li>";
    //     }
    //     echo "</ul>";

    // }
    ?>
</body>
</html>
