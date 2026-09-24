<?php

namespace Tests\Unit;

use App\Enums\Role;
use PHPUnit\Framework\TestCase;

class RoleTest extends TestCase
{
    public function test_only_editors_and_admins_can_moderate(): void
    {
        $this->assertTrue(Role::Admin->canModerate());
        $this->assertTrue(Role::Editor->canModerate());
        $this->assertFalse(Role::Member->canModerate());
    }
}
