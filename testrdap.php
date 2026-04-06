<?php

/**
 * Manual test script for the RDAP client library.
 *
 * Usage: php testrdap.php
 */

include './vendor/autoload.php';

use NamingHive\RDAP\Protocol;
use NamingHive\RDAP\Rdap;
use NamingHive\RDAP\RdapException;

// --- Configure the search ---
$search   = 'gammaddqwdwqdq.com';
$protocol = Protocol::Domain;

// Uncomment for other search types:
// $search   = '8.8.4.4';
// $protocol = Protocol::Ipv4;

// $search   = '59980';
// $protocol = Protocol::Asn;

try {
    $rdap = new Rdap($protocol);
    $test = $rdap->search($search);

    if ($test) {
        echo 'Class name: ' . $test->getClassname() . PHP_EOL;
        echo 'Handle: ' . $test->getHandle() . PHP_EOL;
        echo 'LDH (letters, digits, hyphens) name: ' . $test->getLDHName() . PHP_EOL;

        if (is_array($test->getNameservers())) {
            echo "\nNameservers:\n";
            foreach ($test->getNameservers() as $nameserver) {
                $nameserver->dumpContents();
            }
        }

        if (is_array($test->getSecureDNS())) {
            echo "DNSSEC:\n";
            foreach ($test->getSecureDNS() as $dnssec) {
                $dnssec->dumpContents();
            }
            echo PHP_EOL;
        }

        echo "RDAP conformance:\n";
        foreach ($test->getConformance() as $conformance) {
            $conformance->dumpContents();
        }
        echo PHP_EOL;

        if (is_array($test->getEntities())) {
            echo "Entities found:\n";
            foreach ($test->getEntities() as $entity) {
                $entity->dumpContents();
                echo PHP_EOL;
            }
        }

        if (is_array($test->getLinks())) {
            echo "Links:\n";
            foreach ($test->getLinks() as $link) {
                $link->dumpContents();
            }
            echo PHP_EOL;
        }

        if (is_array($test->getNotices())) {
            echo "Notices:\n";
            foreach ($test->getNotices() as $notice) {
                $notice->dumpContents();
            }
            echo PHP_EOL;
        }

        if (is_array($test->getRemarks())) {
            echo "Remarks:\n";
            foreach ($test->getRemarks() as $remark) {
                $remark->dumpContents();
            }
            echo PHP_EOL;
        }

        if (is_array($test->getStatus())) {
            echo "Statuses:\n";
            foreach ($test->getStatus() as $status) {
                $status->dumpContents();
            }
            echo PHP_EOL;
        }

        if (is_array($test->getEvents())) {
            echo "Events:\n";
            foreach ($test->getEvents() as $event) {
                $event->dumpContents();
            }
        }
    } else {
        echo "{$search} was not found on any RDAP service\n";
    }
} catch (RdapException $e) {
    echo 'ERROR: ' . $e->getMessage() . PHP_EOL;
}
