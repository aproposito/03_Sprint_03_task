<?php
declare(strict_types =1);

class Task extends Model {

private static string $file = __DIR__ . '/../../data/tasks.json';

public static function getAll(): array {}

public static function fetchOne(int $id): ?array {}

public static function save(array $task): void {}

public static function delete(int $id): void {}

public static function update(array $task): void {}

public static function updateStatus(int $id, string $status): void {}

}