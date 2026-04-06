<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapVcard
{
    protected ?string $name = null;
    protected ?string $fieldtype = null;
    protected mixed $content = null;
    protected ?int $preference = null;
    protected ?array $contenttypes = null;

    public function __construct(
        mixed $name,
        mixed $extras,
        mixed $type,
        mixed $contents,
    ) {
        $this->name = is_string($name) ? $name : null;

        if (is_array($extras)) {
            if (isset($extras['type'])) {
                $this->contenttypes = is_array($extras['type'])
                    ? $extras['type']
                    : [$extras['type']];
            }

            if (isset($extras['pref'])) {
                $this->preference = (int) $extras['pref'];
            }
        }

        $this->fieldtype = is_string($type) ? $type : null;
        $this->content = $contents;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getFieldtype(): ?string
    {
        return $this->fieldtype;
    }

    public function getContentTypes(): ?array
    {
        return $this->contenttypes;
    }

    public function dumpContents(): void
    {
        echo '  - ' . $this->getContent() . PHP_EOL;
    }

    public function getContent(): ?string
    {
        return match ($this->name) {
            'version' => 'Version: ' . $this->content,
            'tel'     => 'Type: ' . $this->fieldtype . ', Preference: ' . $this->preference . ', Content: ' . $this->content . ' (' . $this->dumpContentTypes() . ')',
            'email'   => 'Type: ' . $this->name . ', Content: ' . $this->content,
            'fn'      => 'Type: ' . $this->name . ', Content: ' . $this->content,
            'kind'    => 'Kind: ' . $this->content,
            'ISO-3166-1-alpha-2' => 'Language: ' . $this->content . ' (' . $this->name . ')',
            'adr'     => $this->formatAddress(),
            default   => null,
        };
    }

    public function dumpContentTypes(): string
    {
        if (!is_array($this->contenttypes)) {
            return '';
        }

        return implode(', ', $this->contenttypes);
    }

    private function formatAddress(): string
    {
        $return = 'Type: ' . $this->name . ', Content: ';

        if (!is_array($this->content)) {
            return $return . (string) $this->content;
        }

        foreach ($this->content as $content) {
            if (is_array($content)) {
                $return .= implode(' ', $content) . ' ';
            } elseif (trim((string) $content) !== '') {
                $return .= $content . ' ';
            }
        }

        return $return;
    }
}
