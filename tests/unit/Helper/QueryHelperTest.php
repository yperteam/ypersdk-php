<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;
use Yper\SDK\Helper\QueryHelper;

class QueryHelperTest extends TestCase
{
    public function testIsAssoc()
    {
        $this->assertFalse(QueryHelper::isAssoc([]));
        $this->assertFalse(QueryHelper::isAssoc(["Alraab", "Hagga", "Tabr"]));

        $this->assertTrue(QueryHelper::isAssoc(["ma" => "mie", "pas" => "pie"]));
    }

    public function testConstruct()
    {
        $helper = new QueryHelper([]);
        $this->assertEquals($helper->getEncodedUrl(), '?');

        $helper = new QueryHelper(["mentat" => "thufir hawat"]);
        $this->assertEquals($helper->getEncodedUrl(), '?mentat=thufir+hawat');

        $helper = new QueryHelper(["mentats" => ["thufir hawat", "duncan idaho", "peter de vries"]]);
        $this->assertEquals($helper->getEncodedUrl(), '?mentats=thufir+hawat&mentats=duncan+idaho&mentats=peter+de+vries');

        $helper = new QueryHelper([
            "date" => new \DateTime("1965-08-01 12:34:56+00:00"),
            "planet" => "Arrakis",
            "with_spice" => True,
            "pages" => 412,
            "sequels" => ["Dune Messiah", "Children of Dune"],
        ]);
        $this->assertEquals($helper->getEncodedUrl(), '?date=1965-08-01T12%3A34%3A56%2B0000&planet=Arrakis&with_spice=true&pages=412&sequels=Dune+Messiah&sequels=Children+of+Dune');
    }
}