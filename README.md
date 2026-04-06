# RDAP Client

A modern, robust PHP 8.1+ client to query RDAP (Registration Data Access Protocol) services.

It seamlessly abstracts IANA bootstrap discovery, automatically fetching the correct registry base URLs and performing searches. The library caches bootstrap documents intelligently to prevent redundantly querying IANA limits.

## Requirements

- PHP >= 8.1
- [Composer](https://getcomposer.org/)

## Features
- **Backed by Protocol Enums**: Strictly typed `Protocol` configurations.
- **Built-in File Caching**: Automatically caches IANA bootstrap mappings for 24-hours using `BootstrapCache`.
- **Guzzle HTTP Client Integration**: Uses robust standard `guzzlehttp/guzzle` wrappers under the hood, gracefully resolving timeout and protocol issues.

## Installation

Install the library via Composer:

```bash
composer require naminghive/rdap-client
```

## Usage

### Basic Search (Domain)

Use the `NamingHive\RDAP\Rdap` client instance and the `NamingHive\RDAP\Protocol` enum to search for domains, IPs, or ASN numbers.

```php
use NamingHive\RDAP\Rdap;
use NamingHive\RDAP\Protocol;

// Initialize an RDAP Client targeting Domain registries
$rdap = new Rdap(Protocol::Domain);

try {
    $response = $rdap->search('google.com');

    if ($response !== null) {
        // Output basic entity information
        echo 'Handle: ' . $response->getHandle() . PHP_EOL;
        echo 'LDH Name: ' . $response->getLDHName() . PHP_EOL;
        
        // Output Name Servers
        foreach ($response->getNameservers() as $nameserver) {
            echo 'Nameserver: ' . $nameserver->getLdhName() . PHP_EOL;
        }
    } else {
        echo "Domain could not be found on any RDAP service.\n";
    }
} catch (NamingHive\RDAP\RdapException $e) {
    echo "Query failed: " . $e->getMessage() . "\n";
}
```

### Searching by IP Address

The flow is identical for IP queries configurations; directly supply the `IPv4` or `IPv6` protocols.

```php
use NamingHive\RDAP\Rdap;
use NamingHive\RDAP\Protocol;

$rdap = new Rdap(Protocol::Ipv4);
$response = $rdap->search('8.8.4.4');

if ($response !== null) {
    echo 'Notice: ' . $response->getNotices()[0]->getTitle() . "\n";
}
```

### Supporting Standards
This client adheres closely to modern RFC standards. Note that RDAP adoption differs by regional registry.

- **RFC 7480** - HTTP Usage in the Registration Data Access Protocol (RDAP)
- **RFC 7481** - Security Services for the Registration Data Access Protocol (RDAP)
- **RFC 7482** - Registration Data Access Protocol (RDAP) Query Format
- **RFC 7483** - JSON Responses for the Registration Data Access Protocol (RDAP)
- **RFC 7484** - Finding the Authoritative Registration Data (RDAP) Service
- **RFC 9083** - JSON Responses for the Registration Data Access Protocol (RDAP)

## Customization

The library allows overrides to both caching methods and Guzzle HTTP wrappers natively.

```php
// Modifying cache durations
use NamingHive\RDAP\Data\Cache\BootstrapCache;

// Changes cache directory & reduces timeout
$customCache = new BootstrapCache(ttl: 3600, cacheDir: __DIR__ . '/var/cache');
$rdap = new Rdap(Protocol::Domain, cache: $customCache);
```