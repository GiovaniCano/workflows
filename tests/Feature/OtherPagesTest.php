<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class OtherPagesTest extends TestCase
{
    public function test_privacy_policy_is_accessible()
    {
        $res = $this->get(route('privacy'));
        $res->assertStatus(200);
        $res->assertSee('<h1>', false);
    }

    public function test_terms_and_conditions_is_accessible()
    {
        $res = $this->get(route('terms'));
        $res->assertStatus(200);
        $res->assertSee('<h1>', false);
    }
}
