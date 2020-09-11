<?php

$db_connection = pg_connect("host=ec2-54-175-77-250.compute-1.amazonaws.com dbname=dfrb7hd4vrdb2f user=mvuqkoprzfqivh password=ad80b1dc386178820ce37652ddb0cf4c41a30e44e81203ae9ce036161a2bb840")
or die("Не удалось соединиться с сервером");
$table = 'userpostslist';
$result = pg_query($db_connection, "SELECT * FROM $table");
if (!$result) {
  echo "Произошла ошибка.\n";
  exit;
}
echo pg_last_error($db_connection);

?>