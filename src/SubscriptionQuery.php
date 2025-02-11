<?php

namespace PigeonBoys\Fastsub;

class SubscriptionQuery
{
    private array $fields = [];
    private string $key;

    public function __construct(string $key)
    {
        $this->key = $key;
    }

    private function validateFields(): void
    {
        if (empty($this->fields)) {
            throw new \RuntimeException('Fields cannot be empty in SubscriptionQuery.');
        }
    }

    public function count(): int
    {
        return SubscriptionClient::getLength($this->key);
    }

    public function delete(): int
    {
        $this->validateFields();
        return SubscriptionClient::removeValues($this->key, $this->fields);
    }

    public function field(string $field): self
    {
        return $this->fields([$field]);
    }

    public function fields(array $fields): self
    {
        $this->fields = array_unique(array_merge($this->fields, $fields));
        return $this;
    }

    public function get(): array
    {
        $this->validateFields();
        $response = SubscriptionClient::getValues($this->key, $this->fields);
        $subscriptions = array_filter(array_combine($this->fields, $response));

        return $subscriptions;
    }

    public static function hash(string $key): self
    {
        return new self($key);
    }

    public function json(): array
    {
        return array_map(fn($value) => json_decode($value, true) ?? null, $this->get());
    }

    public function updateOrCreate(string $field, string $value): mixed
    {
        return $this->upsert([$field => $value]);
    }

    public function upsert(array $dictionary, bool $dumpHashBefore = false): mixed
    {
        if ($dumpHashBefore === false) {
            return SubscriptionClient::setValues($this->key, $dictionary);
        }

        $tempKey = $this->key . '_temp';
        SubscriptionClient::setValues($tempKey, $dictionary);
        sleep(1);

        return SubscriptionClient::renameKey($tempKey, $this->key);
    }
}
