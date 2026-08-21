<?php
$mysqli = new mysqli('localhost','root','','diago');
if ($mysqli->connect_errno) { fwrite(STDERR,'CONNECT_ERR'); exit(1); }
$res = $mysqli->query('SELECT auto_backup, backup_time, backup_frequency, backup_weekday, cron_secret_key FROM sch_settings ORDER BY id ASC LIMIT 1');
if (!$res) { fwrite(STDERR,'QUERY_ERR'); exit(1); }
$row = $res->fetch_assoc();
echo json_encode($row);
