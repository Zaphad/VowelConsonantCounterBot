<?php

$table = 'userpostslist';
$result = pg_query($db_connection, "SELECT * FROM $table");
if (!$result) {
  echo "Произошла ошибка.\n";
  exit;
}
echo pg_last_error($db_connection);

?>