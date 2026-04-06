<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapLink extends RdapObject
{
    protected ?string $rel = null;
    protected ?string $href = null;
    protected ?string $title = null;
    protected ?string $value = null;
    protected ?string $type = null;

    public function __construct(string $key, mixed $content)
    {
        parent::__construct($key, null);

        if (is_array($content)) {
            $data = isset($content[0]) ? $content[0] : $content;

            $this->rel   = $data['rel'] ?? null;
            $this->href  = $data['href'] ?? null;
            $this->type  = $data['type'] ?? null;
            $this->value = $data['value'] ?? null;
        }
    }

    public function dumpContents(): void
    {
        echo '  - Link: ' . $this->rel . ': ' . $this->href . ' (' . $this->title . ")\n";
    }

    public function getRel(): ?string
    {
        return $this->rel;
    }

    public function getHref(): ?string
    {
        return $this->href;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }
}
