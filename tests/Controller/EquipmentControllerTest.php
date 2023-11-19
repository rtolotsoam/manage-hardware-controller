<?php

namespace App\Tests\Controller;

use App\Entity\Equipment;
use App\Repository\EquipmentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class EquipmentControllerTest extends WebTestCase
{

    public function testGetAllEquipments()
    {
        $client = static::createClient();

        $client->request('GET', '/api/equipments');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testGetDetailEquipment()
    {
        $client = static::createClient();

        $client->request('GET', '/api/equipment/3');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertJson($client->getResponse()->getContent());
    }

    public function testDeleteEquipment()
    {
        $client = static::createClient();

        $container = self::$container ?? self::$kernel->getContainer();
        $equipmentRepository = $container->get(EquipmentRepository::class);
        $existingEquipment = $equipmentRepository->find(1);
        $client->request('DELETE', '/api/equipment/delete/13');

        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        $updatedEquipment = $equipmentRepository->find(13);
        $this->assertNull($updatedEquipment);

        if ($existingEquipment) {
            $entityManager = $container->get('doctrine.orm.entity_manager');
            $entityManager->persist($existingEquipment);
            $entityManager->flush();
        }
    }

    public function testCreateEquipment(): void
    {
        $client = static::createClient();
        $name = 'Test name Equipment';
        $category = 'Test category Equipment';
        $number = 'Test number Equipment';
        $description = 'Test description Equipment';

        $requestData = [
            'name' => $name,
            'category' => $category,
            'number' => $number,
            'description' => $description
        ];
        $client->request(
            'POST',
            '/api/equipment/create',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_CREATED, $response->getStatusCode());

        $responseData = json_decode($response->getContent(), true);
        $this->assertArrayHasKey('id', $responseData);
        $this->assertEquals($name, $responseData['name']);
        $this->assertEquals($category, $responseData['category']);
        $this->assertEquals($number, $responseData['number']);
        $this->assertEquals($description, $responseData['description']);

        $entityManager = $this->getEntityManager($client);
        $equipment = $entityManager->getRepository(\App\Entity\Equipment::class)->find($responseData['id']);

        $this->assertInstanceOf(\App\Entity\Equipment::class, $equipment);
        $this->assertEquals($name, $equipment->getName());
        $this->assertEquals($category, $equipment->getCategory());
        $this->assertEquals($number, $equipment->getNumber());
        $this->assertEquals($description, $equipment->getDescription());
    }

    public function testUpdateEquipment(): void
    {
        $client = static::createClient();

        $entityManager = $this->getEntityManager($client);
        $equipment = new Equipment();
        $equipment->setName('Old Name');
        $equipment->setNumber('Old Number');
        $equipment->setDescription('Old Description');
        $entityManager->persist($equipment);
        $entityManager->flush();

        $requestData = ['name' => 'New Name', 'number' => 'New Number', 'description' => 'New Description'];
        $client->request(
            'PUT',
            '/api/equipment/update/' . $equipment->getId(),
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($requestData)
        );

        $response = $client->getResponse();

        $this->assertEquals(Response::HTTP_NO_CONTENT, $response->getStatusCode());

        $entityManager->refresh($equipment);
        $this->assertEquals('New Name', $equipment->getName());
        $this->assertEquals('New Number', $equipment->getNumber());
        $this->assertEquals('New Description', $equipment->getDescription());
    }

    private function getEntityManager(KernelBrowser $client)
    {
        return $client->getContainer()->get('doctrine.orm.entity_manager');
    }

}
