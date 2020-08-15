<?php

use PHPUnit\Framework\TestCase;
use function PlainCash\Lib\KeyPair\publicKey;

/**
 * Class KeyPairTest
 * @covers PlainCash\Lib\KeyPair
 */
class KeyPairTest extends TestCase
{
    public function test_publicKey()
    {
        $this->assertEquals(
            '04f14ffe1017db891b93c601c2d3bf97dd8ae836e8eb209eb198f81fb74da493f0c2e6d5d3e2bc36ae6e68790e781f6c32397a15d28b1af58096a6513ad0cf3031',
            publicKey('57cd0f1fa13b60f20a8afbc4384a8df664e7c005ccf06463b3d96552c0fdb6e2')
        );
    }
}
