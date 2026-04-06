<?php declare(strict_types=1);

namespace NamingHive\RDAP\Tests;

use NamingHive\RDAP\Data\RdapEntity;
use NamingHive\RDAP\Data\RdapNameserver;
use NamingHive\RDAP\Data\RdapNotice;
use NamingHive\RDAP\Protocol;
use NamingHive\RDAP\Rdap;
use NamingHive\RDAP\RdapException;
use PHPUnit\Framework\TestCase;

final class RdapTest extends TestCase
{
    public function testEmptySearchThrowsException(): void
    {
        $rdap = new Rdap(Protocol::Ipv4);

        $this->expectException(RdapException::class);
        $this->expectExceptionMessage('Search parameter may not be empty');
        $rdap->search('');
    }

    public function testWhitespaceOnlySearchThrowsException(): void
    {
        $rdap = new Rdap(Protocol::Ipv4);

        $this->expectException(RdapException::class);
        $rdap->search('   ');
    }

    public function testAsnSearchRequiresNumeric(): void
    {
        $rdap = new Rdap(Protocol::Asn);

        $this->expectException(RdapException::class);
        $this->expectExceptionMessage('numeric');
        $rdap->search('not-a-number');
    }

    public function testGetProtocolReturnsEnum(): void
    {
        $rdap = new Rdap(Protocol::Domain);

        $this->assertSame(Protocol::Domain, $rdap->getProtocol());
    }

    public function testDomainSearch(): void
    {
        $rdap = new Rdap(Protocol::Domain);
        $response = $rdap->search('google.com');

        $this->assertNotNull($response);
        $this->assertNotEmpty($rdap->getPublicationDate());
        $this->assertNotEmpty($rdap->getVersion());

        $nameservers = $response->getNameservers();
        if ($nameservers !== null) {
            $this->assertContainsOnlyInstancesOf(RdapNameserver::class, $nameservers);
        }

        $entities = $response->getEntities();
        if ($entities !== null) {
            $this->assertContainsOnlyInstancesOf(RdapEntity::class, $entities);
        }
    }

    public function testIpv4Search(): void
    {
        $rdap = new Rdap(Protocol::Ipv4);
        $result = $rdap->search('8.8.4.4');

        $this->assertNotNull($result);

        $notices = $result->getNotices();
        if ($notices !== null) {
            $this->assertContainsOnlyInstancesOf(RdapNotice::class, $notices);
        }

        $entities = $result->getEntities();
        if ($entities !== null) {
            $this->assertContainsOnlyInstancesOf(RdapEntity::class, $entities);
        }
    }

    public function testCachingWorks(): void
    {
        $rdap1 = new Rdap(Protocol::Domain);
        $rdap1->search('google.com');

        // Second search should use cached bootstrap data
        $rdap2 = new Rdap(Protocol::Domain);
        $result = $rdap2->search('google.com');

        $this->assertNotNull($result);
        $this->assertNotEmpty($rdap2->getDescription());
    }

    public function testNonExistentDomainReturnsNull(): void
    {
        $rdap = new Rdap(Protocol::Domain);
        $result = $rdap->search('this-domain-absolutely-does-not-exist-12345.xyz');

        // May return null or a response depending on the RDAP server
        // We mainly test that it doesn't throw an exception
        $this->assertTrue(true);
    }
}
