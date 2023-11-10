<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Ramsey\Uuid\Uuid;
use Tests\TestCase;

class HashServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testHashStore()
    {
        $response = $this->json('POST', 'api/hash', ['data' => [
            'externalId' => 'foo',
            'context' => 'bar'
            ]
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure(['hash']);
    }

    public function testHashReadNotFound()
    {
        $response = $this->json('GET', 'api/hash/nonexistenthash');

        $response->assertStatus(404)
            ->assertJson(['error' => 'Not found']);
    }

    public function testHashRead()
    {
        $responseItem = $this->json('POST', 'api/hash', ['data' => [
            'externalId' => Uuid::uuid4()->toString(),
            'context' => 'bar'
        ]
        ]);

        $response = $this->json('GET', 'api/hash/' . $responseItem['hash']);


        $response->assertStatus(200)
            ->assertJsonStructure(['item']);
    }

    public function testHashReadWithCollisions()
    {
        $this->json('POST', 'api/hash', ['data' => [
            'externalId' => 'foo',
            'context' => 'bar'
            ]
        ]);
        $collisionsItem = $this->json('POST', 'api/hash', ['data' => [
            'externalId' => 'foo',
            'context' => 'bar'
            ]
        ]);

        $response = $this->json('GET', 'api/hash/' . $collisionsItem['hash']);


        $response->assertStatus(200)
            ->assertJsonStructure(['item', 'collisions']);
    }
}
