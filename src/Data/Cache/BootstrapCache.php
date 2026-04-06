<?php declare(strict_types=1);

namespace NamingHive\RDAP\Data\Cache;

use NamingHive\RDAP\Protocol;

/**
 * File-based cache for IANA RDAP bootstrap data.
 *
 * Stores each protocol's bootstrap JSON as a separate cache file to avoid
 * redundant HTTP requests to IANA. The data rarely changes (typically updated
 * monthly), so a 24-hour TTL is a sensible default.
 */
final class BootstrapCache
{
    private readonly string $cacheDir;

    /**
     * @param int $ttl Time-to-live in seconds (default: 86400 = 24 hours)
     * @param string|null $cacheDir Custom cache directory (default: src/Data/Cache/store)
     */
    public function __construct(
        private readonly int $ttl = 86400,
        ?string $cacheDir = null,
    ) {
        $this->cacheDir = $cacheDir ?? __DIR__ . '/store';

        if (!is_dir($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }

    /**
     * Retrieve cached bootstrap data for a protocol.
     *
     * @return array{services: array, description: string, publication: string, version: string}|null
     */
    public function get(Protocol $protocol): ?array
    {
        $file = $this->getCacheFilePath($protocol);

        if (!file_exists($file)) {
            return null;
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            return null;
        }

        $cached = unserialize($raw);
        if (!is_array($cached) || !isset($cached['timestamp'], $cached['data'])) {
            return null;
        }

        // Check if cache has expired
        if ((time() - $cached['timestamp']) > $this->ttl) {
            unlink($file);
            return null;
        }

        return $cached['data'];
    }

    /**
     * Store bootstrap data for a protocol.
     *
     * @param array{services: array, description: string, publication: string, version: string} $data
     */
    public function set(Protocol $protocol, array $data): void
    {
        $file = $this->getCacheFilePath($protocol);

        $cached = [
            'timestamp' => time(),
            'data'      => $data,
        ];

        file_put_contents($file, serialize($cached), LOCK_EX);
    }

    /**
     * Check if a valid (non-expired) cache entry exists for a protocol.
     */
    public function isValid(Protocol $protocol): bool
    {
        return $this->get($protocol) !== null;
    }

    /**
     * Clear all cached bootstrap data.
     */
    public function clear(): void
    {
        $files = glob($this->cacheDir . '/rdap_bootstrap_*.cache');
        if ($files) {
            foreach ($files as $file) {
                unlink($file);
            }
        }
    }

    /**
     * Clear cached data for a specific protocol.
     */
    public function clearProtocol(Protocol $protocol): void
    {
        $file = $this->getCacheFilePath($protocol);
        if (file_exists($file)) {
            unlink($file);
        }
    }

    /**
     * Get the cache file path for a given protocol.
     */
    private function getCacheFilePath(Protocol $protocol): string
    {
        return $this->cacheDir . '/rdap_bootstrap_' . $protocol->value . '.cache';
    }
}
