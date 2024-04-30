<?php
ini_set('max_execution_time', 30); // Устанавливаем максимальное время выполнения в 120 секунд
require 'zipMaster.php';
require 'makeContract.php';

//ищет с нуля все контракты
function getContractsforFirstTime(){
    refreshZipAlania();
    //unzip();
    //makeContract();
}




