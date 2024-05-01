<?php

//  ВОТ ТУТ НЕ ПОЛУЧАЕТСЯ ОБОЙТИ ОГРАНИЧЕНИЕ FTP СЕРВЕРА (НА КОЛ-ВО СКАЧИВАЕМЫХ ФАЙЛОВ ОДНИМ ПОЛЬЗОВАТЕЛЕМ 
//  ЗА НЕКОТОРЫЙ ПРОМЕЖУТОК ВРЕМЕНИ, САМОЕ БОЛЬШОЕ КОЛ-ВО ФАЙЛОВ КОТОРОЕ У МЕНЯ ПОЛУЧИЛОСЬ 
//  СКАЧАТЬ - 108, ЗАТЕМ ФТП ПРОСТО ПЕРЕСТАВАЛ ДАВАТЬ ФАЙЛЫ), УЖЕ ВСЕ ПЕРЕПРОБОВАЛ, ТУТ ТОЛЬКО ОДНО РЕШЕНИЕ - СКАЧТЬ АРХИВЫ ВРУЧНУЮ
//  И КАЖДЫЙ РАЗ ПРОСТО ДОКАЧИВАТЬ НУЖНЫЕ АРХИВЫ (естественно автоматически, не руками)

//  ПРОМЕЖУТОЧНОЕ РЕШЕНИЕ НА ДАННЫЙ МОМЕНТ ЭТО РЕАЛИЗОВАТЬ ПОКА ТОЛЬКО ДЛЯ ОСЕТИИ ТАК КАК МНЕ ЛЕНЬ ПОКА КАЧАТЬ ВРУЧНУЮ СТОЛЬКО 
//  ДЛЯ ВСЕХ РЕГИОНОВ, ПОСЛЕ ПЕРВОЙ ЗАГРУЗКИ ОСНОВНОЙ МАССЫ АРХИВОВ ОБНОВЛЕНИЕ БУДЕТ САМО ПРОИСХОДИТЬ ТАК КАК ОБЫЧНО МАЛО НОВЫХ АРХИВОВ
//  ЗА ДЕНЬ ПОЯВЛЯЕТСЯ И ТУТ ОГРАНИЧЕНИЯ ФТП СЕРВЕРА НЕ МЕШАЮТ. НАДО БУДЕТ ЕЩЕ РАЗОБРАТЬСЯ ПОЧЕМУ НЕ РАБОТАЕТ АВТОЗАПУСК СКРИПТОВ
//  ВРОДЕ ВСЕ ПРАВИЛЬНО УКАЗАЛ В cron.ini 
function getZipforAllRegions(){  // версия только для currMonth и без многих наворотов что есть в рефреш 
    $json_regions = file_get_contents('regions.json');
    $regions = json_decode($json_regions, true);
    // Параметры FTP сервера
    $ftp_server = 'ftp.zakupki.gov.ru';
    $ftp_username = 'free';
    $ftp_password = 'free';
    // Подключение к FTP серверу
    $conn_id = ftp_connect($ftp_server);
    $login_result = ftp_login($conn_id, $ftp_username, $ftp_password);
    //$count = 0;
    //ftp_pasv($conn_id, true); // Включаем пассивный режим передачи данных

    foreach ($regions as $region) {
        // путь к директории с zip архивами на FTP сервере
        $remote_dir = '/fcs_regions/'.$region.'/notifications/currMonth';
        // Получение списка файлов в директории
        $files = ftp_nlist($conn_id, $remote_dir);
        if ($files !== false) {
            foreach ($files as $file) {
                // ПРОБОВАЛ ПЕРЕПОДКЛЮЧАТЬСЯ ЧТОБЫ ОБОЙТИ ОГРАНИЧЕНИЕ ФТП СЕРВЕРА, НЕ ПОЛУЧИЛОСЬ 
                // if ($count == 10) {
                //     ftp_close($conn_id);
                //     $conn_id = ftp_connect($ftp_server);
                //     $login_result = ftp_login($conn_id, $ftp_username, $ftp_password);
                //     $count = 0;
                // }
                // else{
                //     $count++;
                // }
                // Проверка, является ли файл zip архивом
                if (pathinfo($file, PATHINFO_EXTENSION) == 'zip') {
                    // Скачивание zip архива на локальную машину
                    if (!file_exists('zakupki/'.$region)) {
                        // Создаем папку с указанным именем
                        mkdir('zakupki/'.$region);
                    }
                    $local_file = 'zakupki/'.$region.'/'. basename($file);
                    if (ftp_get($conn_id, $local_file, $file, FTP_BINARY)) {
                        $zip = new ZipArchive;
                        if ($zip->open($local_file) === TRUE) {
                        } else {
                            echo 'Ошибка при открытии архива ' . $local_file;
                        }
                    } else {
                        echo 'Ошибка при скачивании файла ' . $file;
                    }
                }
            }
        } else {
            echo 'Ошибка при получении списка файлов в директории ' . $remote_dir;
        }
    }
    // Закрытие соединения с FTP сервером
    ftp_close($conn_id);
} // надо будет переделать по типу refreshZipAlania

//вроде как теперь getZip и не нужна
//вот тут еще бы дописать что если что-то левое есть в currMonthXML чего нет в currMonth то удалить, но если в currMonthXML
//не лезьть самому то там ничего левого и не будет, мне лень пока дописывать, это проблема меня в будущем
//а, еще тут реализован пока только фтп 44, нужно просто добавить фтп 223 и поменять пути, но это потом, сейчас бы поиск и бота
//добить для минимального работающего приложения
//можно поиграться пару архивов удалить или добавить всякую фигню функция сама все поправит (в curr и prev обычных)

//эта штука синхронизирует инфу на ftp и локально, заодно распаковывает xml ки, что-то качает что-то удаляет
function refreshZipAlania(){
    $region ="Severnaja_Osetija-Alanija_Resp";
    // Параметры FTP сервера
    $ftp_server = 'ftp.zakupki.gov.ru';
    $ftp_username = 'free';
    $ftp_password = 'free';
    // Подключение к FTP серверу
    $conn_id = ftp_connect($ftp_server);
    $login_result = ftp_login($conn_id, $ftp_username, $ftp_password);
    //$count = 0;
    //ftp_pasv($conn_id, true); // Включаем пассивный режим передачи данных



    // CURR MONTH 
    // путь к директории с zip архивами на FTP сервере
    $remote_dir = '/fcs_regions/'.$region.'/notifications/currMonth';
    // Получение списка файлов в директории
    $filesFTP = ftp_nlist($conn_id, $remote_dir);
    // Укажите путь к директории
    $directory = 'zakupki\Severnaja_Osetija-Alanija_Resp\currMonth';
    // Получаем список файлов в директории
    $filesLocal = scandir($directory);
    // Фильтруем результаты, чтобы удалить ссылки на текущую и родительскую директории
    $filesLocal = array_diff($filesLocal, array('.', '..'));

    // Подстрока, которую нужно удалить
    $substring_to_remove = '/fcs_regions/Severnaja_Osetija-Alanija_Resp/notifications/currMonth/';
    // Удаляем подстроку из каждого элемента массива
    $filesFTP = array_map(function($file) use ($substring_to_remove) {
        return str_replace($substring_to_remove, '', $file);
    }, $filesFTP);

    // Формируем список файлов, которые есть на FTP сервере, но отсутствуют локально
    $downloadThisFiles = array_diff($filesFTP, $filesLocal);
    // Формируем список файлов, которые есть локально, но отсутствуют на FTP сервере
    $deleteThisFiles = array_diff($filesLocal, $filesFTP);

    // Строка, которую нужно добавить перед каждым элементом
    $prefix = '/fcs_regions/Severnaja_Osetija-Alanija_Resp/notifications/currMonth/';
    // Добавляем строку перед каждым элементом массива
    $filesFTP = array_map(function($file) use ($prefix) {
        return $prefix . $file;
    }, $filesFTP);
    // Добавляем строку перед каждым элементом массива
    $downloadThisFiles = array_map(function($file) use ($prefix) {
        return $prefix . $file;
    }, $downloadThisFiles);



    if ($downloadThisFiles !== false) {
        foreach ($downloadThisFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'zip') {
                // Скачивание zip архива на локальную машину
                if (!file_exists('zakupki/'.$region)) {
                    // Создаем папку с указанным именем
                    mkdir('zakupki/'.$region);
                }
                if (!file_exists('zakupki/'.$region.'/currMonth')) {
                    // Создаем папку с указанным именем
                    mkdir('zakupki/'.$region.'/currMonth');
                }
                if (!file_exists('zakupki/'.$region.'/currMonthXML')) {
                    // Создаем папку с указанным именем
                    mkdir('zakupki/'.$region.'/currMonthXML');
                }
                

                $local_file = 'zakupki/'.$region.'/currMonth'.'/'. basename($file);
                if (ftp_get($conn_id, $local_file, $file, FTP_BINARY)) {
                    $zip = new ZipArchive;
                    if ($zip->open($local_file) === TRUE) {
                    } else {
                        echo 'Ошибка при открытии архива ' . $local_file;
                    }
                } else {
                    echo 'Ошибка при скачивании файла ' . $file;
                }
            }
        }
    } else {
        echo 'Ошибка при получении списка файлов в директории ' . $remote_dir;
    }
    // Строка, которую нужно добавить перед каждым элементом
    // $prefix = 'C:/ospanel/domains/zakup.local/zakupki/Severnaja_Osetija-Alanija_Resp/currMonth/';
    // // Добавляем строку перед каждым элементом массива
    // $deleteThisFiles = array_map(function($file) use ($prefix) {
    //     return $prefix . $file;
    // }, $deleteThisFiles);
    foreach ($deleteThisFiles as $file) {
        if (is_file('zakupki/Severnaja_Osetija-Alanija_Resp/currMonth/'.$file)) {
            unlink('zakupki/Severnaja_Osetija-Alanija_Resp/currMonth/'.$file); // Удаляем каждый файл
        }
        deleteDirectory('zakupki/Severnaja_Osetija-Alanija_Resp/currMonthXML/'.$file);
    }
    unZip('zakupki/'.$region.'/currMonth','zakupki/'. $region .'/currMonthXML');



    // PREV MONTH 
    // путь к директории с zip архивами на FTP сервере
    $remote_dir = '/fcs_regions/'.$region.'/notifications/prevMonth';
    // Получение списка файлов в директории
    $filesFTP = ftp_nlist($conn_id, $remote_dir);
    // Укажите путь к директории
    $directory = 'zakupki\Severnaja_Osetija-Alanija_Resp\prevMonth';
    // Получаем список файлов в директории
    $filesLocal = scandir($directory);
    // Фильтруем результаты, чтобы удалить ссылки на текущую и родительскую директории
    $filesLocal = array_diff($filesLocal, array('.', '..'));

    // Подстрока, которую нужно удалить
    $substring_to_remove = '/fcs_regions/Severnaja_Osetija-Alanija_Resp/notifications/prevMonth/';
    // Удаляем подстроку из каждого элемента массива
    $filesFTP = array_map(function($file) use ($substring_to_remove) {
        return str_replace($substring_to_remove, '', $file);
    }, $filesFTP);

    // Формируем список файлов, которые есть на FTP сервере, но отсутствуют локально
    $downloadThisFiles = array_diff($filesFTP, $filesLocal);
    // Формируем список файлов, которые есть локально, но отсутствуют на FTP сервере
    $deleteThisFiles = array_diff($filesLocal, $filesFTP);

    // Строка, которую нужно добавить перед каждым элементом
    $prefix = '/fcs_regions/Severnaja_Osetija-Alanija_Resp/notifications/prevMonth/';
    // Добавляем строку перед каждым элементом массива
    $filesFTP = array_map(function($file) use ($prefix) {
        return $prefix . $file;
    }, $filesFTP);
    // Добавляем строку перед каждым элементом массива
    $downloadThisFiles = array_map(function($file) use ($prefix) {
        return $prefix . $file;
    }, $downloadThisFiles);



    if ($downloadThisFiles !== false) {
        foreach ($downloadThisFiles as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'zip') {
                // Скачивание zip архива на локальную машину
                if (!file_exists('zakupki/'.$region)) {
                    // Создаем папку с указанным именем
                    mkdir('zakupki/'.$region);
                }
                if (!file_exists('zakupki/'.$region.'/prevMonth')) {
                    // Создаем папку с указанным именем
                    mkdir('zakupki/'.$region.'/prevMonth');
                }
                if (!file_exists('zakupki/'.$region.'/prevMonthXML')) {
                    // Создаем папку с указанным именем
                    mkdir('zakupki/'.$region.'/prevMonthXML');
                }
                

                $local_file = 'zakupki/'.$region.'/prevMonth'.'/'. basename($file);
                if (ftp_get($conn_id, $local_file, $file, FTP_BINARY)) {
                    $zip = new ZipArchive;
                    if ($zip->open($local_file) === TRUE) {
                    } else {
                        echo 'Ошибка при открытии архива ' . $local_file;
                    }
                } else {
                    echo 'Ошибка при скачивании файла ' . $file;
                }
            }
        }
    } else {
        echo 'Ошибка при получении списка файлов в директории ' . $remote_dir;
    }
    // Строка, которую нужно добавить перед каждым элементом
    // $prefix = 'C:/ospanel/domains/zakup.local/zakupki/Severnaja_Osetija-Alanija_Resp/prevMonth/';
    // Добавляем строку перед каждым элементом массива
    // $deleteThisFiles = array_map(function($file) use ($prefix) {
    //     return $prefix . $file;
    // }, $deleteThisFiles);
    foreach ($deleteThisFiles as $file) {
        if (is_file('zakupki/Severnaja_Osetija-Alanija_Resp/prevMonth/'.$file)) {
            unlink('zakupki/Severnaja_Osetija-Alanija_Resp/prevMonth/'.$file); // Удаляем каждый файл
        }
        deleteDirectory('zakupki/Severnaja_Osetija-Alanija_Resp/prevMonthXML/'.$file);
    }
    unZip('zakupki/'.$region.'/prevMonth','zakupki/'. $region .'/prevMonthXML');
    
    // Закрытие соединения с FTP сервером
    ftp_close($conn_id);
}

function deleteDirectory($dir) {
    if (!file_exists($dir)) {
        return true;
    }

    if (!is_dir($dir)) {
        return unlink($dir);
    }

    foreach (scandir($dir) as $item) {
        if ($item == '.' || $item == '..') {
            continue;
        }

        if (!deleteDirectory($dir . DIRECTORY_SEPARATOR . $item)) {
            return false;
        }
    }

    return rmdir($dir);
}

function unZip($archiveDirectory, $extract) {
    // Получаем список всех файлов в директории
    $files = glob($archiveDirectory . '/*.zip');
    
    // Перебираем каждый архив
    foreach ($files as $file) {
        // Получаем имя файла без расширения (имя архива)
        $filename = pathinfo($file, PATHINFO_FILENAME);
        
        // Создаем папку для извлечения архива
        $extractPath = $extract .'/'. $filename.'.zip';
        if (!file_exists($extractPath)) {
            mkdir($extractPath, 0777, true);
        }
        
        // Создаем объект ZipArchive
        $zip = new ZipArchive;

        // Открываем архив
        if ($zip->open($file) === TRUE) {
            $zip->extractTo($extractPath);
            $zip->close();
            //echo "Архив $file успешно распакован в $extractPath.\n";
        } else {
            echo "Ошибка при открытии архива $file.\n";
        }
    }
}