<?php
$mysqli = new mysqli('localhost','root','','diago');
if ($mysqli->connect_errno) {
    fwrite(STDERR, 'CONNECT_ERR: ' . $mysqli->connect_error);
    exit(1);
}
$res = $mysqli->query('SELECT id, cron_secret_key, auto_backup FROM sch_settings ORDER BY id ASC LIMIT 1');
if (!$res) { fwrite(STDERR, 'QUERY_ERR: ' . $mysqli->error); exit(1); }
$row = $res->fetch_assoc();
if (!$row) { echo "[NO_ROW]\n"; exit(0); }
echo '[ID]'.$row['id']."\n";
echo '[KEY]'.($row['cron_secret_key'] ?? '') . "\n";
echo '[AUTO]'.($row['auto_backup'] ?? '') . "\n";
