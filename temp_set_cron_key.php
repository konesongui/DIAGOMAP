<?php
$mysqli = new mysqli('localhost','root','','diago');
if ($mysqli->connect_errno) {
    fwrite(STDERR, 'CONNECT_ERR: ' . $mysqli->connect_error);
    exit(1);
}
$key = bin2hex(random_bytes(20));
$stmt = $mysqli->prepare('UPDATE sch_settings SET cron_secret_key = ? ORDER BY id ASC LIMIT 1');
// MySQLi doesn't support ORDER BY in prepared statement with LIMIT for UPDATE in that way; use a subquery to get id
$res = $mysqli->query('SELECT id FROM sch_settings ORDER BY id ASC LIMIT 1');
if (!$res) { fwrite(STDERR, 'SELECT_ERR: '.$mysqli->error); exit(1); }
$row = $res->fetch_assoc();
if (empty($row['id'])) { fwrite(STDERR, 'NO_ROW'); exit(1); }
$id = (int)$row['id'];
$stmt = $mysqli->prepare('UPDATE sch_settings SET cron_secret_key = ? WHERE id = ?');
if (!$stmt) { fwrite(STDERR, 'PREP_ERR: '.$mysqli->error); exit(1); }
$stmt->bind_param('si', $key, $id);
if (!$stmt->execute()) { fwrite(STDERR, 'EXEC_ERR: '.$stmt->error); exit(1); }
echo $key . "\n";
