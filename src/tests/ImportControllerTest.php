<?php

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ImportControllerTest extends WebTestCase
{
    public function testImport()
    {
        $client = static::createClient();
        $crawler = $client->request('GET', '/import');

        $buttonCrawlerNode = $crawler->selectButton('Upload data!');

        $form = $buttonCrawlerNode->form();

        $form['form[import_file]']->upload('tests/random-3-pharmacies.json');

        $client->submit($form);

        $pharmacyRepository = static::getContainer()->get(\App\Repository\PharmacyRepository::class);
        $allRecords = $pharmacyRepository->findAll();
        $this->assertCount(6, $allRecords);//6, because at the beginning we have always 3 records!
        $this->assertStringContainsString("Pomyślnie stworzono 3 aptek.", $client->getResponse()->getContent());
    }
}