<?php
$mysqli = new mysqli('localhost','root','','diago');
if ($mysqli->connect_errno) {
    fwrite(STDERR, 'CONNECT_ERR: ' . $mysqli->connect_error);
    exit(1);
}
$res = $mysqli->query('SELECT cron_secret_key FROM sch_settings ORDER BY id ASC LIMIT 1');
if (!$res) {
    fwrite(STDERR, 'QUERY_ERR: ' . $mysqli->error);
    exit(1);
}
$row = $res->fetch_assoc();
$key = isset($row['cron_secret_key']) ? $row['cron_secret_key'] : null;
if ($key === null || $key === '') {
    echo "[EMPTY]\n";
} else {
    echo "[KEY]" . $key . "\n";
}
