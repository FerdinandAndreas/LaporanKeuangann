<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;

class CreateOwnerCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_owner_interactively(): void
    {
        $this->artisan('app:create-owner')
            ->expectsQuestion('Nama Owner:', 'Owner Toko')
            ->expectsQuestion('Email Owner:', 'owner@tokoberas.local')
            ->expectsQuestion('Password (minimal 8 karakter):', 'password123')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'name' => 'Owner Toko',
            'email' => 'owner@tokoberas.local',
            'role' => 'owner',
        ]);
    }
}
