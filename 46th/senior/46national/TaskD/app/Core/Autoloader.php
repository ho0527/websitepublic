<?php

declare(strict_types=1);

namespace App\Core;

/**
 * PSR-4 風格的類別自動載入器，將命名空間 App\ 對應到 app/ 目錄。
 */
final class Autoloader
{
    private string $baseDirectory;

    private string $namespacePrefix;

    public function __construct(string $namespacePrefix, string $baseDirectory)
    {
        $this->namespacePrefix = rtrim($namespacePrefix, '\\') . '\\';
        $this->baseDirectory   = rtrim($baseDirectory, '/\\') . DIRECTORY_SEPARATOR;
    }

    /**
     * 向 PHP 註冊本載入器。
     */
    public function register(): void
    {
        spl_autoload_register([$this, 'load']);
    }

    /**
     * 依類別名稱載入對應的檔案。
     */
    public function load(string $className): void
    {
        if (!str_starts_with($className, $this->namespacePrefix)) {
            return;
        }

        $relativeClass = substr($className, strlen($this->namespacePrefix));
        $file          = $this->baseDirectory . str_replace('\\', DIRECTORY_SEPARATOR, $relativeClass) . '.php';

        if (is_file($file)) {
            require $file;
        }
    }
}
