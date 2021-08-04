<?php


namespace App\Helpers;


class SessionStack
{
    /**
     * @var string session key name
     */
    private string $sessionKey;

    public function __construct(string $sessionKey)
    {
        $this->sessionKey = $sessionKey;
    }

    /**
     * Set values array into session key
     * @param array $values
     */
    public function set(array $values): void
    {
        session([$this->sessionKey => $values]);
    }

    /**
     * Return the values of session key
     * @return array
     */
    public function get(): array
    {
        return session($this->sessionKey, []);
    }

    /**
     * Push the value into session key array
     * @param $value
     */
    public function push($value): void
    {
        $values = $this->get();
        array_push($values, $value);
        $this->set($values);
    }

    /**
     * Remove all session key values
     */
    public function reset(): void
    {
        $this->set([]);
    }

}