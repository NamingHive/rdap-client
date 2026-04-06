<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapPublicId extends RdapObject
{
    protected ?array $ids = null;

    public function __construct(string $key, mixed $content)
    {
        $this->objectClassName = 'PublicId';
        parent::__construct($key, null);

        if (is_array($content)) {
            foreach ($content as $id) {
                $this->ids[$id['type']] = $id['identifier'];
            }
        }
    }

    public function dumpContents(): void
    {
        if (is_array($this->ids)) {
            foreach ($this->ids as $type => $identifier) {
                echo "- {$type}: {$identifier}\n";
            }
        }
    }

    public function getIds(): ?array
    {
        return $this->ids;
    }
}
