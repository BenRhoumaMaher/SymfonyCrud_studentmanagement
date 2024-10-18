<?php

namespace App\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class StudentTest extends WebTestCase
{
    private $client;
    private $entityManager;

    protected function setUp(): void
    {
        $this->client = static::createClient();
        $this->entityManager = $this->client->getContainer()->get(
            'doctrine.orm.entity_manager'
        );
    }

    /**
     * @dataProvider \App\Tests\DataProvider\StudentDataProvider::studentListDataProvider
     */
    public function testIndex(int $expectedCount): void
    {
        $crawler = $this->client->request('GET', '/student/');

        $this->assertResponseIsSuccessful();

        $responseContent = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );

        $this->assertIsArray($responseContent);

        $this->assertCount($expectedCount, $responseContent);

        foreach ($responseContent as $student) {
            $this->assertArrayHasKey('id', $student);
            $this->assertArrayHasKey('name', $student);
        }
    }

    /**
     * @dataProvider \App\Tests\DataProvider\StudentDataProvider::newStudentDataProvider
     */
    public function testNewStudent(array $studentData, int $expectedStatusCode): void
    {
        $this->client->request(
            'POST',
            '/student/new',
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($studentData)
        );

        $this->assertResponseStatusCodeSame($expectedStatusCode);

        if ($expectedStatusCode === 200) {
            $responseContent = json_decode(
                $this->client->getResponse()->getContent(),
                true
            );
            $this->assertArrayHasKey('id', $responseContent);
            $this->assertEquals($studentData['name'], $responseContent['name']);
        }
    }

    /**
     * @dataProvider \App\Tests\DataProvider\StudentDataProvider::studentIdDataProvider
     */
    public function testShowStudent(int $studentId, int $expectedStatusCode): void
    {
        $this->client->request('GET', '/student/' . $studentId);

        $this->assertResponseStatusCodeSame($expectedStatusCode);

        if ($expectedStatusCode === 200) {
            $responseContent = json_decode(
                $this->client->getResponse()->getContent(),
                true
            );
            $this->assertEquals($studentId, $responseContent['id']);
            $this->assertArrayHasKey('name', $responseContent);
        }
    }

    /**
     * @dataProvider \App\Tests\DataProvider\StudentDataProvider::editStudentDataProvider
     */
    public function testEditStudent(int $studentId, array $updatedData, int $expectedStatusCode): void
    {
        $this->client->request(
            'PUT',
            '/student/edit/' . $studentId,
            [],
            [],
            ['CONTENT_TYPE' => 'application/json'],
            json_encode($updatedData)
        );

        $this->assertResponseStatusCodeSame($expectedStatusCode);

        if ($expectedStatusCode === 200) {
            $responseContent = json_decode(
                $this->client->getResponse()->getContent(),
                true
            );
            $this->assertEquals(
                $updatedData['name'],
                $responseContent['name']
            );
        }
    }

    /**
     * @dataProvider \App\Tests\DataProvider\StudentDataProvider::deleteStudentDataProvider
     */
    public function testDeleteStudent(int $studentId, int $expectedStatusCode): void
    {
        $this->client->request('DELETE', '/student/delete/' . $studentId);

        $this->assertResponseStatusCodeSame($expectedStatusCode);

        if ($expectedStatusCode === 200) {
            $this->client->request('GET', '/student/' . $studentId);
            $this->assertResponseStatusCodeSame(404);
        }
    }

    /**
     * @dataProvider \App\Tests\DataProvider\StudentDataProvider::classListDataProvider
     */
    public function testGetClasses(int $expectedClassCount): void
    {
        $crawler = $this->client->request('GET', '/student/api/classes');

        $this->assertResponseIsSuccessful();

        $responseContent = json_decode(
            $this->client->getResponse()->getContent(),
            true
        );

        $this->assertIsArray($responseContent);
        $this->assertCount($expectedClassCount, $responseContent);

        foreach ($responseContent as $class) {
            $this->assertArrayHasKey('id', $class);
            $this->assertArrayHasKey('name', $class);
        }
    }

}
