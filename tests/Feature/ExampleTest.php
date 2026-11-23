<?php

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function test_la_raiz_redirige_a_login_para_invitados(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('login'));
    }
}
