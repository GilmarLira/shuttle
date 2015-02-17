<?php

$link = mysql_connect('http://external-db.s153394.gridserver.com', 'db153394', 'cluster66	');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
echo 'Connected successfully';

$db_selected = mysql_select_db('db153394_AAU', $link);
if (!$db_selected) {
    die ('Can\'t use foo : ' . mysql_error());
}


mysql_close($link);

?>