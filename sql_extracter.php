<?php
$time_start = microtime(true);// время старта скрипта
include "config.php"; //Данные для подключения к БД

if(empty($argv[1])) // Если нет параметров
    echo "must be 1 or 2 parametr";
else
{
    $link=mysqli_connect(DB_HOST,DB_ADMIN,DB_PASS,DB_USER);
// формирование запроса
    mysqli_query($link, 'SET NAMES utf8'); //кодировка, ожидаемая от БД
    $query="select ci.name as city, co.name as country, re.title as region
            from us_city ci join us_regions re join us_country  co
            ON ci.region_id=re.id and re.country_id=co.id";
    if(isset($argv[2])) // Если существует второй параметр
        $query.=" LIMIT 0, " . intval($argv[2] . ";");

// запись запроса в массив
    $result=mysqli_query($link,$query);
    while($row = mysqli_fetch_array($result,MYSQLI_ASSOC))
    {
        $arr[] = $row; // новый эл-т массива получает значения
    }
    mysqli_close($link);

// полный путь к записываемому файлу
    $dir = dirname($_SERVER['SCRIPT_FILENAME']);
    $path = $dir . "/" . $argv[1] . ".csv";

// проверка, существует ли файл с таким же названием
    $i = 1;
    while(file_exists($path))
    {
        $add="("."$i".")";
        $path = $dir . "/" . $argv[1]. $add . ".csv";
        $i++;
    }

// запись массива в файл
    for($i = 0 ; $i < count($arr) ; $i++)
    {
        $current=iconv("UTF-8","cp1251",$arr[$i]['city'] . ";" . $arr[$i]['country'] . ";" . $arr[$i]['region'] . PHP_EOL);
        file_put_contents($path,$current, FILE_APPEND);
    }

// проверка, записался ли файл
    if(file_exists($path))
        echo "File created\n";
    else echo "File don't created\n";
}

$time_end = microtime(true);
$time = $time_end - $time_start;
// вывести время исполнения скрипта
echo round($time,6);
?>