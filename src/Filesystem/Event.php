<?php

namespace Hugo\Psr7\Filesystem;

use Hugo\Psr7\Exceptions\InvalidEventTypeException;

class Event
{
    public function __construct(
        public string $type,
        public ?string $file = null,
    )
    {
        if (!in_array($type, EventTypes::getTypes())) {
            throw new InvalidEventTypeException("O tipo '{$type}' é inválido");
        }
    }

    public function isDeletion():bool
    {
        return $this->type === EventTypes::FILE_DELETED;
    }

    public function isAddition(): bool
    {
        return $this->type === EventTypes::FILE_ADDED;
    }

    public function isModification(): bool
    {
        return $this->type === EventTypes::FILE_CHANGED;
    }
}
