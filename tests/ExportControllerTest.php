<?php

namespace App\Tests;

use App\Controller\ExportController;
use App\Exception\NotValidFileType;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Serializer\Encoder\JsonEncoder;

class ExportControllerTest extends WebTestCase
{
    public function testGetRandomAndSafeFileName(): void
    {
        $randomName = (new ExportController())->getRandomAndSafeFileName('json');
        $this->assertMatchesRegularExpression("/exported-data-([a-z0-9]+)\.json/", $randomName);
    }

    public function testGetBadTypeOfSerialization()
    {
        $this->expectException(NotValidFileType::class);
        (new ExportController())->getTypeOfSerialization('pdf');
    }

    public function testGetGoodTypeOfSerialization()
    {
        $type = (new ExportController())->getTypeOfSerialization('json');
        $this->assertEquals($type, JsonEncoder::FORMAT);
    }

    public function testFullExportInJSON()
    {
        $client = static::createClient();
        $client->request('GET', '/export/all/json');
        $binaryData = $client->getInternalResponse()->getContent();
        $decodedData = json_decode($binaryData, true);
        $this->assertCount(3, $decodedData);
    }

    public function testFilteredDataInJSON(){
        $client = static::createClient();
        $client->request('GET', '/export/filtered/json?city=Olsztyn');
        $binaryData = $client->getInternalResponse()->getContent();
        $decodedData = json_decode($binaryData, true);
        $this->assertCount(2, $decodedData);
    }
}
