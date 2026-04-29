<?php
declare(strict_types =1);

class Task extends Model {

    private static string $file = __DIR__ . '/../../data/tasks.json';

    public static function getAll(): array {
        $json = file_get_contents(self::$file);
        $data = json_decode($json, true);
        return $data ?? [];
    }

    public static function getByUser($userId): array
    {
        $json = file_get_contents(self::$file);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return [];
        }

        $filtered = array_filter($data, function ($task) use ($userId) {
            return isset($task['user_id']) && (string) $task['user_id'] === (string) $userId;
        });

        return array_values($filtered);
    }

    public static function getById(int $id, $userId = null): ?array
    {
        $json = file_get_contents(self::$file);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return null;
        }

        foreach ($data as $task) {
            if ($task['id'] === $id && ($userId === null || (isset($task['user_id']) && (string) $task['user_id'] === (string) $userId))) {
                return $task;
            }
        }

        return null;
    }

    public static function create(array $task): void {
        if (!isset($task['user_id'])) {
            throw new Exception('Task must belong to a user.');
        }

        $json = file_get_contents(self::$file);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            $data = [];
        }
        
        $maxId = empty($data) ? 0 : max(array_column($data, 'id'));
        $newId = $maxId + 1;
        $task['id'] = $newId;
        $task['start_time'] = date('Y-m-d H:i:s');
        $data[] = $task;
        $newJson = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(self::$file, $newJson);
    }

    public static function destroy(int $id, $userId = null): void {
        $json = file_get_contents(self::$file);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            $data = [];
        }

        $data = array_values(array_filter($data, function($task) use ($id, $userId) {
            if ($task['id'] !== $id) {
                return true;
            }
            if ($userId !== null && (!isset($task['user_id']) || (string) $task['user_id'] !== (string) $userId)) {
                return true;
            }
            return false;
        }));

        $newJson = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(self::$file, $newJson);
    }

    public static function update(array $task, $userId = null): void {
        $json = file_get_contents(self::$file);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            $data = [];
        }

        $id = $task['id'];
        foreach ($data as $index => $CurrentTask) {
            if ($CurrentTask['id'] === $id && ($userId === null || (isset($CurrentTask['user_id']) && (string) $CurrentTask['user_id'] === (string) $userId))) {
                $mergedTask = array_merge($CurrentTask, $task);
                if ($mergedTask['status'] === 'completed' && empty($mergedTask['end_time'])) {
                    $mergedTask['end_time'] = date('Y-m-d H:i:s');
                }
                $data[$index] = $mergedTask;
                break;
            }
        }

        $newJson = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(self::$file, $newJson);
    }

    public static function updateStatus(int $id, string $status, $userId = null): void {
        $json = file_get_contents(self::$file);
        $data = json_decode($json, true);
        if (!is_array($data)) {
            $data = [];
        }

        foreach ($data as $index => $CurrentTask) {
            if ($CurrentTask['id'] === $id && ($userId === null || (isset($CurrentTask['user_id']) && (string) $CurrentTask['user_id'] === (string) $userId))) {
                $CurrentTask['status'] = $status;
                if ($status === 'completed') {
                    $CurrentTask['end_time'] = date('Y-m-d H:i:s');
                }
                $data[$index] = $CurrentTask;
                break;
            }
        }

        $newJson = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents(self::$file, $newJson);
    }
}
