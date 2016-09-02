<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST' && $_SERVER['REQUEST_METHOD'] != 'DELETE') {
  http_response_code(405);
  exit;
}

$json = file_get_contents('php://input');

if (!$json) {
  http_response_code(400);
  exit;
}

$object = json_decode($json);

if (!property_exists($object, 'time') || !property_exists($object, 'weekday')) {
  http_response_code(400);
  exit;
}

require_once '../sql.php';
$sql = new SQL();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  $query = 'UPDATE openings SET "time" = $1, weekday = $2 WHERE "time" = $1 AND weekday = $2 AND member = $3';
  $sql->query($query, [$object->time, $object->weekday, $object->member]);
  $query = 'INSERT INTO openings ("time", weekday, member) SELECT $1, $2, $3 WHERE NOT EXISTS (SELECT 1 FROM openings WHERE "time" = $1 AND weekday = $2 AND member = $3)';
  $sql->query($query, [$object->time, $object->weekday, $object->member]);
}
else {
  $query = 'DELETE FROM openings WHERE time = $1 AND weekday = $2 AND member = $3';
  $sql->query($query, [$object->time, $object->weekday, $object->member]);
}

header('Content-Type: application/json');
http_response_code(200);
echo '{"status":"sccess"}';
?>
