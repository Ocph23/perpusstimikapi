<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserTest extends TestCase
{
    
    public function test_user_unauthorized()
    {
        $this->token="Bearer 2|RYFJHOZhA0jxNi3IZ4ZsIwexdbAWOftlRWeFqoo5";
       $response= $this->withHeaders([
           'Authorization'=>$this->token,
           ])->get('/api/buku');
        $response->assertStatus(200);

    }


}
