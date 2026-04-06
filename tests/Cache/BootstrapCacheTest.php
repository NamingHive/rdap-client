<?php declare(strict_types=1);

namespace NamingHive\RDAP\Tests\Cache;

use NamingHive\RDAP\Data\Cache\BootstrapCache;
use NamingHive\RDAP\Protocol;
use PHPUnit\Framework\TestCase;

final class BootstrapCacheTest extends TestCase
{
    private string $testCacheDir;
    private BootstrapCache $cache;

    protected function setUp(): void
    {
        $this->testCacheDir = sys_get_temp_dir() . '/rdap_test_cache_' . uniqid();
        $this->cache = new BootstrapCache(ttl: 3600, cacheDir: $this->testCacheDir);
    }

    protected function tearDown(): void
    {
        // Clean up test cache directory
        $this->cache->clear();
        if (is_dir($this->testCacheDir)) {
            rmdir($this->testCacheDir);
        }
    }

    public function testCacheDirectoryIsCreated(): void
    {
        $this->assertDirectoryExists($this->testCacheDir);
    }

    public function testGetReturnsNullWhenEmpty(): void
    {
        $this->assertNull($this->cache->get(Protocol::Domain));
    }

    public function testIsValidReturnsFalseWhenEmpty(): void
    {
        $this->assertFalse($this->cache->isValid(Protocol::Domain));
    }

    public function testSetAndGet(): void
    {
        $data = [
            'description' => 'Test bootstrap',
            'publication' => '2026-01-01',
            'version'     => '1.0',
            'services'    => [
                [['com', 'net'], ['https://rdap.example.com/']],
            ],
        ];

        $this->cache->set(Protocol::Domain, $data);
        $cached = $this->cache->get(Protocol::Domain);

        $this->assertNotNull($cached);
        $this->assertSame($data['description'], $cached['description']);
        $this->assertSame($data['services'], $cached['services']);
    }

    public function testIsValidReturnsTrueAfterSet(): void
    {
        $this->cache->set(Protocol::Domain, ['services' => []]);
        $this->assertTrue($this->cache->isValid(Protocol::Domain));
    }

    public function testExpiredCacheReturnsNull(): void
    {
        // Create cache with 1-second TTL
        $cache = new BootstrapCache(ttl: 1, cacheDir: $this->testCacheDir);
        $cache->set(Protocol::Ipv4, ['services' => []]);

        // Wait for expiration
        sleep(2);

        $this->assertNull($cache->get(Protocol::Ipv4));
    }

    public function testClearRemovesAllCacheFiles(): void
    {
        $this->cache->set(Protocol::Domain, ['services' => []]);
        $this->cache->set(Protocol::Ipv4, ['services' => []]);

        $this->cache->clear();

        $this->assertNull($this->cache->get(Protocol::Domain));
        $this->assertNull($this->cache->get(Protocol::Ipv4));
    }

    public function testClearProtocolRemovesOnlySpecificCache(): void
    {
        $this->cache->set(Protocol::Domain, ['services' => [], 'description' => 'domain']);
        $this->cache->set(Protocol::Ipv4, ['services' => [], 'description' => 'ipv4']);

        $this->cache->clearProtocol(Protocol::Domain);

        $this->assertNull($this->cache->get(Protocol::Domain));
        $this->assertNotNull($this->cache->get(Protocol::Ipv4));
    }

    public function testDifferentProtocolsAreCachedSeparately(): void
    {
        $domainData = ['services' => [['com']], 'description' => 'DNS'];
        $ipv4Data = ['services' => [['8.0.0.0/8']], 'description' => 'IPv4'];

        $this->cache->set(Protocol::Domain, $domainData);
        $this->cache->set(Protocol::Ipv4, $ipv4Data);

        $this->assertSame('DNS', $this->cache->get(Protocol::Domain)['description']);
        $this->assertSame('IPv4', $this->cache->get(Protocol::Ipv4)['description']);
    }
}
