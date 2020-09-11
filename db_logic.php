<?php

function saveMessage($db_connection, $table, $post, $name){
    if($name==''){
        return false;
    }
    $res = pg_insert($db_connection, $table, array('post'=>$post, 'name'=>$name));
    if ($res) {
        echo "Сообщение успешно записано в лог пользователя: $name\n";
    } else {
        echo "Ошибка записи saveMessage\n";
    }
}

function getLastUserMessage($db_connection, $table, $name){
    $query = "SELECT post FROM " . $table . " WHERE name = '" . $name . "' order by id desc LIMIT 1";
    $result = pg_query($db_connection, $query);
    if (!$result) {
        $val = "Произошла ошибка.\n";
    }else{
        $val  = pg_fetch_result($result,0);
    }
    return $val;
}

function getLastTenUserMessages($db_connection, $table, $name){
    $query = "SELECT post FROM " . $table . " WHERE name = '" . $name . "'";
    $result = pg_query($db_connection, $query);
    if (!$result) {
        $arr = "Произошла ошибка.\n";
    }else{
        $arr = pg_fetch_all_columns($result);
    }
    return $arr;
}

function deleteMessages($db_connection, $table,$name){
    $query = "DELETE FROM " . $table . " WHERE name = '" . $name . "' and id < 
    (select id from
    (select id from " . $table . " 
    WHERE name = '" . $name . "' 
    order by id desc limit 10) 
    as derivedTable order by id asc limit 1)";
    $result = pg_query($db_connection, $query);
}
?>