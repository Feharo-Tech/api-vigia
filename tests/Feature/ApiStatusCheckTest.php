<?php

public function test_user_can_create_api()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)
        ->post('/apis', [
            'name' => 'Test API',
            'url' => 'https://example.com/api',
            'method' => 'GET',
            'expected_status_code' => 200,
            'check_interval' => 5,
            'is_active' => true,
        ]);
        
    $response->assertRedirect('/');
    $this->assertDatabaseHas('apis', ['name' => 'Test API']);
}