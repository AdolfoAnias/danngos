<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TaskRouteTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
    
    public function testVisitTheHomePage()
    {    
        //$response = $this->call('GET', '/api/task');
        $response = $this->get('/api/task');
        $this->assertEquals(200, $response->status());            
    }    
}
