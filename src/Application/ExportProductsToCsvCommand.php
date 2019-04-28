<?php

declare(strict_types=1);

namespace App\Application;

final class ExportProductsToCsvCommand
{
    /** @var string */
    private $client;

    /** @var string */
    private $secret;

    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $uri;

    public function __construct(string $client, string $secret, string $username, string $password, string $uri)
    {
        $this->client = $client;
        $this->secret = $secret;
        $this->username = $username;
        $this->password = $password;
        $this->uri = $uri;
    }

    public function client(): string
    {
        return $this->client;
    }

    public function secret(): string
    {
        return $this->secret;
    }

    public function username(): string
    {
        return $this->username;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function uri(): string
    {
        return $this->uri;
    }
}
