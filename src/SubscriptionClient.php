<?php

namespace PigeonBoys\Fastsub;

use Exception;
use Predis\Client;

/**
 * @internal
 */
final class SubscriptionClient
{
    private static ?Client $client = null;

    private static function execute(callable $operation): mixed
    {
        try {
            return $operation(self::getClient());
        } catch (Exception $e) {
            throw new Exception('SubscriptionClient failed: ' . $e->getMessage(), 0, $e);
        }
    }

    private static function getClient(): Client
    {
        return self::$client ?? self::initialize();
    }

    private static function initialize(): Client
    {
        if (!self::$client) {
            self::$client = new Client([
                'scheme' => 'tcp',
                'host' => SubscriptionConfiguration::getHost(),
                'port' => SubscriptionConfiguration::getPort(),
                'database' => SubscriptionConfiguration::getDatabase(),
            ]);
        }

        return self::$client;
    }

    public static function deleteKey(string $key): mixed
    {
        SubscriptionConfiguration::checkPermission();
        return self::execute(fn($client) => $client->del(SubscriptionConfiguration::getPrefix() . $key));
    }

    public static function getLength(string $key): int
    {
        return self::execute(fn($client) => $client->hLen(SubscriptionConfiguration::getPrefix() . $key));
    }

    public static function getValue(string $key, int|string $field): ?string
    {
        return self::execute(fn($client) => $client->hget(SubscriptionConfiguration::getPrefix() . $key, $field));
    }

    public static function getValues(string $key, array $fields): array
    {
        return self::execute(fn($client) => $client->hmget(SubscriptionConfiguration::getPrefix() . $key, $fields));
    }

    public static function removeValue(string $key, string $field): int
    {
        SubscriptionConfiguration::checkPermission();
        return self::execute(fn($client) => $client->hdel(SubscriptionConfiguration::getPrefix() . $key, $field));
    }

    public static function removeValues(string $key, array $fields): int
    {
        SubscriptionConfiguration::checkPermission();
        return self::execute(fn($client) => $client->hdel(SubscriptionConfiguration::getPrefix() . $key, $fields));
    }

    public static function renameKey(string $oldKey, string $newKey): mixed
    {
        SubscriptionConfiguration::checkPermission();
        return self::execute(fn($client) => $client->rename(SubscriptionConfiguration::getPrefix() . $oldKey, SubscriptionConfiguration::getPrefix() . $newKey));
    }

    public static function setValue(string $key, string $field, string $value): int
    {
        SubscriptionConfiguration::checkPermission();
        return self::execute(fn($client) => $client->hset(SubscriptionConfiguration::getPrefix() . $key, $field, $value));
    }

    public static function setValues(string $key, array $dictionary): mixed
    {
        SubscriptionConfiguration::checkPermission();
        return self::execute(fn($client) => $client->hmset(SubscriptionConfiguration::getPrefix() . $key, $dictionary));
    }
}
