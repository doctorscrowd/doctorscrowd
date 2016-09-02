<?php
require_once '../sql.php';
$sql = new SQL();
$raw = file_get_contents('php://input');
$json = json_decode($raw);
mb_convert_variables('EUC-JP', 'UTF-8', $json);
$query = 'INSERT INTO messages ("id", body, "user", member, "timestamp") VALUES (uuid_generate_v4(), $1, $2, NOW())';
$sql->query($query, [$json->body, $json->user, $json->member]);
?>
