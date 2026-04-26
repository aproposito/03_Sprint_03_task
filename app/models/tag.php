<?php

declare(strict_types=1);

class Tag extends Model
{

    private static string $file = __DIR__ . '/../../data/tags.json';
    private static string $taskTagsFile = __DIR__ . '/../../data/task_tags.json';

    public static function getAll(): array
    {
        $json = file_get_contents(self::$file);
        $tags = json_decode($json, true);
        return $tags;
    }

    public static function fetchOne(int $id): ?array
    {
        $json = file_get_contents(self::$file);
        $tags = json_decode($json, true);
        foreach ($tags as $tag) {
            if ($tag['id'] === $id) {
                return $tag;
            }
        }
        return null;
    }

    public static function save(array $tag): void
    {
        $tags = self::getAll();

        $maxId = max(array_column($tags, 'id'));
        $tag['id'] = $maxId + 1;

        $tags[] = $tag;
        file_put_contents(self::$file, json_encode($tags, JSON_PRETTY_PRINT));
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
        file_put_contents(self::$file, json_encode($tags, JSON_PRETTY_PRINT));
    }


    public static function delete(int $id): void
    {

        $tags = self::getAll();
        $tags = array_values(array_filter($tags, fn($t) => $t['id'] !== $id));
        file_put_contents(self::$file, json_encode($tags, JSON_PRETTY_PRINT));

        $json = file_get_contents(self::$taskTagsFile);
        $taskTags = json_decode($json, true);
        $taskTags = array_values(array_filter($taskTags, fn($tt) => $tt['tag_id'] !== $id));
        file_put_contents(self::$taskTagsFile, json_encode($taskTags, JSON_PRETTY_PRINT));
    }


    public static function getByUser(int $userId): array
    {
        $json = file_get_contents(self::$file);
        $tags = json_decode($json, true);

        $filtered = array_filter($tags, function ($tag) use ($userId) {
            return $tag["user_id"] === null || $tag["user_id"] === $userId;
        });

        return array_values($filtered);
    }
}
