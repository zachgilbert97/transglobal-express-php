<?php

namespace ZachGilbert\TransglobalExpress\Tests\Support;

use Psr\Log\AbstractLogger;
use Stringable;

final class InMemoryLogger extends AbstractLogger
{
    /**
     * @var array<int, array{level: mixed, message: string, context: array}>
     */
    public array $records = [];

    /**
     * @param mixed             $level
     * @param string|Stringable $message
     * @param array             $context
     *
     * @return void
     */
    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->records[] = [
            'level' => $level,
            'message' => (string) $message,
            'context' => $context,
        ];
    }

    /**
     * @param string $level
     *
     * @return array<int, array{level: mixed, message: string, context: array}>
     */
    public function recordsAt(string $level): array
    {
        return array_values(array_filter(
            $this->records,
            static fn (array $record): bool => $record['level'] === $level,
        ));
    }
}
