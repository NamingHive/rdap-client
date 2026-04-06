<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapDescription extends RdapObject
{
    protected ?string $description = null;

    public function __construct(string $key, mixed $content)
    {
        parent::__construct($key, null);

        $this->description = is_array($content) ? ($content[0] ?? null) : $content;
    }

    public function dumpContents(): void
    {
        echo '  - Description: ' . $this->getDescription() . PHP_EOL;
    }

    public function getDescription(): string
    {
        return $this->description ?? '';
    }
}
