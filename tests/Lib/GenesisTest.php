<?php

use PHPUnit\Framework\TestCase;
use function PlainCash\Lib\Genesis\createGenesisBlock;

/**
 * Class GenesisTest
 * @covers PlainCash\Lib\Genesis
 */
class GenesisTest extends TestCase
{
    /**
     * @throws \BitWasp\Bitcoin\Exceptions\InvalidHashLengthException
     * @throws \BitWasp\Bitcoin\Exceptions\MerkleTreeEmpty
     */
    public function test_createGenesisBlock()
    {
        $genesis = createGenesisBlock(
            "DummyNet 15/Aug/2020",
            1597504370,
            2966,
            0x1e0ffff0,
            1,
            5000000000, // 50 * COIN
            "04f14ffe1017db891b93c601c2d3bf97dd8ae836e8eb209eb198f81fb74da493f0c2e6d5d3e2bc36ae6e68790e781f6c32397a15d28b1af58096a6513ad0cf3031"
        );

        $this->assertEquals(
            "0fa70a19407ea995ae94b8022fcd5a69bfbce153b3079d336e9a43d927ff7b19",
            $genesis->getMerkleRoot()->getHex()
        );
        $this->assertEquals(
            "010000000000000000000000000000000000000000000000000000000000000000000000197bff27d9439a6e339d07b353e1bcbf695acd2f02b894ae95a97e40190aa70f72fb375ff0ff0f1e960b0000",
            $genesis->getHeader()->getHex()
        );
        $this->assertEquals(
            "00050c26a9ff60f6e532786d443bf8c3b412d41406cc8c04f9f11625ee263514",
            $genesis->getHeader()->getHash()->getHex()
        );
        $this->assertEquals(
            "010000000000000000000000000000000000000000000000000000000000000000000000197bff27d9439a6e339d07b353e1bcbf695acd2f02b894ae95a97e40190aa70f72fb375ff0ff0f1e960b00000101000000010000000000000000000000000000000000000000000000000000000000000000ffffffff1c04ffff001d01041444756d6d794e65742031352f4175672f32303230ffffffff0100f2052a01000000434104f14ffe1017db891b93c601c2d3bf97dd8ae836e8eb209eb198f81fb74da493f0c2e6d5d3e2bc36ae6e68790e781f6c32397a15d28b1af58096a6513ad0cf3031ac00000000",
            $genesis->getHex()
        );
    }
}
