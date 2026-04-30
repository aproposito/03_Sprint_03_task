<?php

declare(strict_types=1);

class Tag
{

    private static string $file = __DIR__ . '/../../data/tags.json';
    private static string $taskTagsFile = __DIR__ . '/../../data/task_tags.json';


    private static function readTaskTags(): array
    {
        return json_decode(file_get_contents(self::$taskTagsFile), true) ?? [];
    }
    private static function writeTaskTags(array $taskTags): void
    {
        file_put_contents(self::$taskTagsFile, json_encode($taskTags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
    private static function writeTags(array $tags): void
    {
        file_put_contents(self::$file, json_encode($tags, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }


    public static function getAll(): array
    {
        $json = file_get_contents(self::$file);
        return json_decode($json, true) ?? [];
    }

    public static function fetchOne(int $id): ?array
    {
        foreach (self::getAll() as $tag) {
            if ($tag['id'] === $id) {
                return $tag;
            }
        }
        return null;
    }

    public static function save(array $tag): void
    {
        $tags = self::getAll();
        $maxId = empty($tags) ? 0 : max(array_column($tags, 'id'));
        $tag['id'] = $maxId + 1;
        $tags[] = $tag;
        self::writeTags($tags);
    }


    public static function update(array $tag): void
    {
        $tags = self::getAll();
        foreach ($tags as $index => $t) {
            if ($t['id'] === $tag['id']) {
                $tags[$index] = $tag;
                break;
            }
        }
        self::writeTags($tags);
    }


    public static function delete(int $id): void
    {
        $tags = self::getAll();
        $tags = array_values(array_filter($tags, fn($t) => $t['id'] !== $id));
        self::writeTags($tags);

        $taskTags = self::readTaskTags();
        $taskTags = array_values(array_filter($taskTags, fn($tt) => $tt['tag_id'] !== $id));
        self::writeTaskTags($taskTags);
    }


    public static function getByUser(int|string $userId): array
    {
        $tags = self::getAll();
        $filtered = array_filter($tags, fn($tag) => $tag['user_id'] === null || $tag['user_id'] == $userId);
        return array_values($filtered);
    }

    public static function getTagsByTaskId(int $taskId): array
    {
        $taskTags = self::readTaskTags();
        $tagIds = array_column(array_filter($taskTags, fn($tt) => $tt['task_id'] === $taskId), 'tag_id');
        $tags = self::getAll();
        return array_values(array_filter($tags, fn($t) => in_array($t['id'], $tagIds)));
    }

    public static function saveTaskTags(int $taskId, array $tagIds): void
    {
        $taskTags = self::readTaskTags();
        $taskTags = array_values(array_filter($taskTags, fn($tt) => $tt['task_id'] !== $taskId));
        foreach ($tagIds as $tagId) {
            $taskTags[] = ['task_id' => $taskId, 'tag_id' => $tagId];
        }
        self::writeTaskTags($taskTags);
    }

    public static function deleteTaskTags(int $taskId): void
    {
        $taskTags = self::readTaskTags();
        $taskTags = array_values(array_filter($taskTags, fn($tt) => $tt['task_id'] !== $taskId));
        self::writeTaskTags($taskTags);
    }

    public static function getTaskIdsByTagIds(array $tagIds): array
    {
        $taskTags = self::readTaskTags();
        $filtered = array_filter($taskTags, fn($tt) => in_array($tt['tag_id'], $tagIds));
        return array_values(array_unique(array_column($filtered, 'task_id')));
    }
}
