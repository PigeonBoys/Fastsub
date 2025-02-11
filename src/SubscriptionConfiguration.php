<?php

namespace PigeonBoys\Fastsub;

class SubscriptionConfiguration
{
    private static ?string $host = null;
    private static ?int $port = null;
    private static ?int $database = null;
    private static string $prefix;
    private static bool $isWriter;

    public static function initialize(
        string $host,
        int $port,
        int $database,
        string $prefix = 'subscriptions:',
        bool $isWriter = false
    ): void {
        self::$host = $host;
        self::$port = $port;
        self::$database = $database;
        self::$prefix = $prefix;
        self::$isWriter = $isWriter;
    }

    private static function checkConfig(): void
    {
        if (self::$host === null) {
            throw new \RuntimeException("Host is not set in SubscriptionConfiguration.");
        }

        if (self::$port === null) {
            throw new \RuntimeException("Port is not set in SubscriptionConfiguration.");
        }

        if (self::$database === null) {
            throw new \RuntimeException("Database is not set in SubscriptionConfiguration.");
        }
    }

    public static function checkPermission(): void
    {
        if (self::$isWriter === false) {
            throw new \RuntimeException("Write permission is not enabled in SubscriptionConfiguration.");
        }
    }

    public static function getHost(): string
    {
        self::checkConfig();
        return self::$host;
    }

    public static function getPort(): int
    {
        self::checkConfig();
        return self::$port;
    }

    public static function getDatabase(): int
    {
        self::checkConfig();
        return self::$database;
    }

    public static function getPrefix(): string
    {
        return self::$prefix;
    }

    public static function isWriter(): bool
    {
        return self::$isWriter;
    }
}
