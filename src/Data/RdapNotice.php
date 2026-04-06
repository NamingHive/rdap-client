<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data;

final class RdapNotice extends RdapObject
{
    protected ?string $title = null;
    protected ?string $type = null;
    /** @var RdapDescription[]|null */
    protected ?array $description = null;
    /** @var RdapLink[]|null */
    protected ?array $links = null;

    public function __construct(string $key, mixed $content)
    {
        $this->objectClassName = 'Notice';
        parent::__construct($key, $content);
    }

    public function dumpContents(): void
    {
        echo '- ' . $this->getTitle() . ': ' . $this->getType() . PHP_EOL;

        if (is_array($this->description)) {
            foreach ($this->description as $descr) {
                $descr->dumpContents();
            }
        }

        if (is_array($this->links)) {
            foreach ($this->links as $link) {
                $link->dumpContents();
            }
        }
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    /**
     * @return RdapDescription[]|null
     */
    public function getDescription(): ?array
    {
        return $this->description;
    }
}
