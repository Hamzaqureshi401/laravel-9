<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;


class EmailControllerTest extends TestCase
{
    //use RefreshDatabase;

    public function testSend(){
        $emailData = [
            ['subject' => 'test', 'body' => 'this is body1', 'to' => 'test@test.com'],
            ['subject' => 'this is subject2', 'body' => 'this is body2', 'to' => 'test@test.com']
        ];
        $jsonEmailData = json_encode($emailData);
        $requestData = [
            'emailData' => $jsonEmailData,
            'api_token' => 'a9c97cbb1eacc7e4f3686e5a7f9395fd.64e1b8d34f425d19e1ee2ea7236d3028'
        ];
        $user = 1;
        $response = $this->json( 'POST', 'api/{$user}/send', $requestData, ['Accept' => 'application/json']);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Email Sent Successfully!']);
    }

    public function testList(){

        $response = $this->json('GET', '/api/list');
        $response->assertStatus(200)
            ->assertJsonStructure([
                'sentEmailsElastic',
                'sentEmailsRedis'
            ]);
        $data = $response->json();
        $this->assertNotEmpty($data['sentEmailsElastic']);
        $this->assertNotEmpty($data['sentEmailsRedis']);
    }

}
