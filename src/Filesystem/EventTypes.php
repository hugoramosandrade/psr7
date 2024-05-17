<?php

namespace Hugo\Psr7\Filesystem;

class EventTypes
{
    const FILE_ADDED = 'FILE_ADDED';
    const FILE_CHANGED = 'FILE_CHANGED';
    const FILE_DELETED = 'FILE_DELETED';
    const START_SERVER = 'START_SERVER';

    public static function getTypes(): array
    {
        $reflection = new \ReflectionClass(self::class);
        return $reflection->getConstants();
    }
}
