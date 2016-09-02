<?php
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
  http_response_code(405);
  exit;
}

$json = file_get_contents('php://input');

if (!$json) {
  http_response_code(400);
  exit;
}

$object = json_decode($json);

if (!property_exists($object, 'date') ||
    !property_exists($object, 'open') ||
    !property_exists($object, 'time') ||
    !property_exists($object, 'weekday') ||
    !property_exists($object, 'member')) {
  http_response_code(400);
  exit;
}

require_once '../sql.php';
$sql = new SQL();
$query = 'SELECT * FROM schedules WHERE date = $1 AND time = $2 AND $3 = member';
$schedule = $sql->query($query, [$object->date, $object->time, $object->member]);

if ($schedule) {
  $query = 'UPDATE schedules SET date = $1, open = $2, time = $3, weekday = $4, member = $5 WHERE date = $1 AND time = $3 AND member = $5';
  $sql->query($query, [$object->date, $object->open, $object->time, $object->weekday, $object->member]);
}
else {
  $query = 'INSERT INTO schedules (date, open, time, weekday, member) VALUES ($1, $2, $3, $4, $5)';
  $sql->query($query, [$object->date, $object->open, $object->time, $object->weekday, $object->member]);
}

header('Content-Type: application/json');
http_response_code(200);
echo '{"status":"sccess"}';
?>
