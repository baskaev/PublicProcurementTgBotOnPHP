
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- не сохранять кеш -->
    <meta http-equiv="cache-control" content="no-cache, must-revalidate, max-age=0">
    <meta http-equiv="cache-control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="pragma" content="no-cache">

    <title>Search and Save Links</title>
</head>
<body>
    <h1>Search and Save Links</h1>
    <form action="index.php" method="post">
        <label for="search">Enter search term:</label>
        <input type="text" id="search" name="search" required>
        <button type="submit">Search</button>
    </form>
    <?php
    require 'Program/Dayly.php';
    require 'Program/search.php';
    DaylyAlania();

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $searchTerm = $_POST["search"];
        //$jsonFindedTenders = SearchTenders('Severnaja_Osetija-Alanija_Resp', $searchTerm);
        $jsonFindedTenders = BoolSearchTenders('Severnaja_Osetija-Alanija_Resp', $searchTerm);
        $FindedTenders = json_decode($jsonFindedTenders, true);
        foreach($FindedTenders as $tender){
            echo '<a href="' . $tender['href'] . '">' . $tender['text'] . '</a>';

            echo "<br><br>";
            
        }
        echo count($FindedTenders);
    }
    
    // ПОСЛЕ ЗАПУСКА У ВАС МИНУТУ БУДЕТ РАБОТАТЬ БОТ (пока что не на вебхуках  а long polling)
    require 'bot\bot.php';

    // мне не нужно хранить json по каждому поиску пользователя чтобы искать что же появилось нового
    // я могу просто сформировывать каждый день новый json по всем тендерам и смотреть что же изменилось
    // по сравнению с предыдущим а затем из новых тендеров смотреть что же можно отправить пользователям
    // нового по их запросам
    ?>
</body>
</html>
