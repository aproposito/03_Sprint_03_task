<?php
declare(strict_types =1);

class Task extends Model {

private static string $file = __DIR__ . '/../../data/tasks.json';

public static function getAll(): array {
  $json = file_get_contents(self::$file);
  $data = json_decode($json, true);
  return $data;
}

public static function getById(int $id): ?array
{
    $json = file_get_contents(self::$file);
    $data = json_decode($json, true);
    $taskFetched = null;

    foreach ($data as $task) {
        if ($task["id"] == $id) {
            return $task;
        }
    }

    return $taskFetched;
}

public static function create(array $task): void {
    $json = file_get_contents(self::$file);
    $data = json_decode($json, true);
    
    $maxId = max(array_column($data, 'id'));
    $newId = $maxId +1;
    $task ["id"] = $newId;
    $task['start_time'] = date('Y-m-d H:i:s');
    $data [] = $task;
    $newJson = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents(self::$file, $newJson);
}

public static function destroy(int $id): void {
    $json = file_get_contents(self::$file);
    $data = json_decode($json, true);
    $data = array_filter($data, function($task) use ($id) {
        return $task["id"] !== $id;
        });
    $data = array_values($data);
    $newJson = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents(self::$file, $newJson);
}

public static function update(array $task): void {
    $json = file_get_contents(self::$file);
    $data = json_decode($json, true);
    $id = $task["id"];
    foreach ($data as $index => $CurrentTask) {
    if ($CurrentTask["id"] === $id) {
        $data[$index] = $task;
    if ($task["status"] === 'completed' && empty($task["end_time"])) {
        $data[$index]["end_time"] = date('Y-m-d H:i:s');
    }
        break;
    }
}
    $newJson = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents(self::$file, $newJson);
}

public static function updateStatus(int $id, string $status): void {
    $json = file_get_contents(self::$file);
    $data = json_decode($json, true);
    foreach ($data as $index => $CurrentTask) {
    if ($CurrentTask["id"] === $id) {
        $data[$index]["status"] = $status;
        if ($status === 'completed') {
        $data[$index]["end_time"] = date('Y-m-d H:i:s');
        break;
    }
}
    $newJson = json_encode($data, JSON_PRETTY_PRINT);
    file_put_contents(self::$file, $newJson);
    }
}}