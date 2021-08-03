<?php


namespace App\Helpers;


class SessionStack
{
    private string $sessionKey;

    public function __construct(string $sessionKey)
    {
        $this->sessionKey = $sessionKey;
    }

    public function set(array $values): void
    {
        session([$this->sessionKey => $values]);
    }

    public function get(): array
    {
        return session($this->sessionKey, []);
    }

    public function push($value): void
    {
        $values = $this->get();
        array_push($values, $value);
        $this->set($values);
    }

    public function reset(): void
    {
        $this->set([]);
    }



}