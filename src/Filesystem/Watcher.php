<?php

namespace Hugo\Psr7\Filesystem;

use Hugo\Psr7\Exceptions\InvalidPathException;
use Swoole\Coroutine as Co;

class Watcher
{
    private array $filesMap = [];

    public function __construct(
        private readonly string $path,
        private int $watchInterval = 1,
        private \Closure|string|null $callback = null
    )
    {
        if (!file_exists($path)) {
            throw new InvalidPathException("O caminho '{$path}' é inválido");
        }
    }

    public function watch(): void
    {
        $this->filesMap = $this->readPath($this->path);
        while (true) {
            $this->clearStats();
            $this->checkPath($this->path);
            Co::sleep($this->watchInterval);
        }
    }

    // Lê de forma recursiva os arquivos e subpastas de uma pasta teste
    protected function readPath($path): array
    {
        $filesMap = [];
        $filesMap[$path] = filemtime($path);
        if (is_dir($path)) {
            $files = scandir($path);
            foreach ($files as $file) {
                if ($file === '.' || $file === '..' || $file === 'vendor' || $file === '.git') {
                    continue;
                }
                $actualPath = "{$path}{$file}";
                $filesMap[$actualPath] = filemtime($actualPath);
                if (is_dir($actualPath)) {
                    $tmp = $this->readPath($actualPath."/");
                    $filesMap = $filesMap + $tmp;
                }
            }
        }
        return $filesMap;
    }

    // limpa o cache dos nomes dos arquivos mudei
    protected function clearStats(): void
    {
        foreach ($this->filesMap as $file => $time) {
            clearstatcache(filename: $file);
        }
    }

    protected function checkPath($path)
    {
        /** @var Event[] */
        $eventFiles = [];
        $currentStatus = $this->readPath($path);
        // Detecta arquivos deletados e modificações
        foreach ($this->filesMap as $file => $time) {
            if (!isset($currentStatus[$file])) {
                $eventFiles[] = new Event(EventTypes::FILE_DELETED, $file);
            } else if ($currentStatus[$file] !== $time) {
                $eventFiles[] = new Event(EventTypes::FILE_CHANGED, $file);
            }
        }

        // Detecta novos arquivos
        foreach ($currentStatus as $file => $time) {
            if (!isset($this->filesMap[$file])) {
                $eventFiles[] = new Event(EventTypes::FILE_ADDED, $file);
            }
        }

        $this->filesMap = $currentStatus;

        foreach ($eventFiles as $event) {
            $this->triggerEvent($event);
        }
    }

    protected function triggerEvent(Event $event): void
    {
        if (is_callable($this->callback)) {
            call_user_func($this->callback, $event);
        }
    }
}
