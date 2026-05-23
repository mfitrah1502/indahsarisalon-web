<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Config;

class HashingFallbackTest extends TestCase
{
    /**
     * Test that checking a password against a non-bcrypt hash does not throw a RuntimeException
     * when the hashing verification is configured to false.
     */
    public function test_non_bcrypt_hash_does_not_throw_exception(): void
    {
        // Explicitly set verify to false for bcrypt
        Config::set('hashing.bcrypt.verify', false);

        // This is not a bcrypt hash (standard bcrypt hash starts with $2y$, $2a$, etc.)
        $invalidHash = 'some_invalid_plain_text_hash';

        // Check password. It should return false instead of throwing a RuntimeException.
        $result = Hash::check('password', $invalidHash);

        $this->assertFalse($result);
    }
}
