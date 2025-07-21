<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testPasswordHashing(): void
    {
        $password = 'test-password-123';
        $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        
        $this->assertTrue(password_verify($password, $hash));
        $this->assertFalse(password_verify('wrong-password', $hash));
    }

    public function testPasswordNeedsRehash(): void
    {
        $password = 'test-password-123';
        $oldHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
        
        $this->assertTrue(password_needs_rehash($oldHash, PASSWORD_BCRYPT, ['cost' => 12]));
        $this->assertFalse(password_needs_rehash($oldHash, PASSWORD_BCRYPT, ['cost' => 10]));
    }
}