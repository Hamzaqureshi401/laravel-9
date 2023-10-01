<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginApiTest extends TestCase{
    //use RefreshDatabase;

    public function testLoginApi(){
        $userData = [
            'email' => 'admin@admin.com',
            'password' => '12345'
        ];
        $response = $this->json('POST', 'api/login', $userData, ['Accept' => 'application/json']);
        $response->assertStatus(200);
        $response->assertSeeText('5');
        $responseData = json_decode($response->getContent(), true);
         if ($responseData && array_key_exists('data', $responseData)) {
            $response->assertJson([
                'message' => 'User Login Successfully!',
                'data' => $responseData['data']
            ]);
         }else{
            $response->assertJson([
                'message' => 'Credentials Do no match'
            ]);
        }
        $prettyResponse = json_encode(json_decode($response->getContent()), JSON_PRETTY_PRINT);
        dump($prettyResponse);
    }



}
