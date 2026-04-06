<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapRole extends RdapObject
{
    protected ?string $role = null;

    public function __construct(string $key, mixed $content)
    {
        parent::__construct($key, null);

        // Roles come as simple string values
        if (is_string($content)) {
            $this->role = $content;
        }
    }

    public function dumpContents(): void
    {
        echo '- Role: ' . $this->getRole() . PHP_EOL;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }
}