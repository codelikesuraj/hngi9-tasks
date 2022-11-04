<?php

header('Content-type: application/json');

$request_method = strtolower($_SERVER['REQUEST_METHOD']);
$result = 0;
$operation = null;
$x = 0;
$y = 0;

if ($request_method == 'post') {
  if (isset($_POST) && array_key_exists('operation_type', $_POST)) {
    $data = $_POST;
  } else {
    $data = (array)json_decode(file_get_contents('php://input'));
  }

  // check operand
  if (!isset($data['operation_type'])) {
    http_response_code(400);
    echo json_encode(['error' => 'operation type cannot be empty']);
    exit();
  }

  $op_type = $data['operation_type'];
  if (strpos(strtolower($op_type), 'add') || strpos(strtolower($op_type), 'sum') || strpos(strtolower($op_type), 'plus') || strpos(strtolower($op_type), '+')) {
    $operation = 'addition';
  } else if (strpos(strtolower($op_type), 'subtract') || strpos(strtolower($op_type), 'minus') || strpos(strtolower($op_type), 'remove') || strpos(strtolower($op_type), '-')) {
    $operation = 'subtraction';
  } else if (strpos(strtolower($op_type), 'multipl') || strpos(strtolower($op_type), 'times') || strpos(strtolower($op_type), 'product') || strpos(strtolower($op_type), '*')) {
    $operation = 'multiplication';
  }

  if (is_null($operation)) {
    http_response_code(400);
    echo json_encode([
      'error' => 'invalid operation type'
    ]);
  }

  // check if values are set
  $errors = [];
  if (!isset($data['x']) || empty(trim($data['x'])) || !is_numeric(trim($data['x']))) {
    $errors[] = 'invalid value for x';
  }
  if (!isset($data['y']) || empty(trim($data['y'])) || !is_numeric(trim($data['y']))) {
    $errors[] = 'invalid value for y';
  }
  if (count($errors)) {
    preg_match_all('!\d+!', $op_type, $numbers);
    if (is_null($numbers) || count($numbers[0]) !== 2) {
      $errors[] = 'values cannot be empty';
      echo json_encode([
        'errors' => $errors
      ]);
      exit();
    }

    if (strpos(strtolower($op_type), 'from')) {
      $y = $numbers[0][0];
      $x = $numbers[0][1];
    } else {
      $x = $numbers[0][0];
      $y = $numbers[0][1];
    }
  } else {
    $x = intval($data['x']);
    $y = intval($data['y']);
  }


  switch ($operation) {
    case 'addition':
      $result = $x + $y;
      break;
    case 'subtraction':
      $result = $x - $y;
      break;
    case 'multiplication':
      $result = $x * $y;
      break;

    default:
      $result = $x + $y;
      break;
  }

  echo json_encode([
    "slackUsername" => "codelikesuraj",
    "result" => intval($result),
    "operation_type" => $operation
  ]);
  exit();
}

if ($request_method == 'get') {
  echo json_encode([
    "slackUsername" => "codelikesuraj",
    "backend" => true,
    "age" => 22,
    "bio" => "Backend developer"
  ]);
  exit();
}

echo json_encode([
  'message' => 'What are you doing here?'
]);
