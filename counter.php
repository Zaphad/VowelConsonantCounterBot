composer require irazasyed/telegram-bot-sdk ^2.0<?php

function calc_von($haystack) {
    $needle = array("а","е","ё","и","о","у","ы","э","ю","я","А","Е","Ё","И","О","У","Ы","Э","Ю","Я");
    $count = 0;
    foreach ($needle as $substring) {
            $count += substr_count( $haystack, $substring);
    }
    return $count;
}

function calc_con($haystack) {
    $needle = array("б","в","г","д","ж","з","й","к","л","м","н","п","р","с","т","ф","х","ц","ч","ш","щ","Б","В","Г","Д","Ж","З","Й","К","Л","М","Н","П","Р","С","Т","Ф","Х","Ц","Ч","Ш","Щ");
    $count = 0;
    foreach ($needle as $substring) {
        $count += substr_count( $haystack, $substring);
    }
    return $count;
}
?>