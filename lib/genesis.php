<?php

declare(strict_types=1);

namespace PlainCash\Lib\Genesis;

use BitWasp\Bitcoin\Block\Block;
use BitWasp\Bitcoin\Block\BlockHeader;
use BitWasp\Bitcoin\Block\MerkleRoot;
use BitWasp\Bitcoin\Math\Math;
use BitWasp\Bitcoin\Script\Opcodes;
use BitWasp\Bitcoin\Script\ScriptFactory;
use BitWasp\Bitcoin\Transaction\Factory\TxBuilder;
use BitWasp\Buffertools\Buffer;

/**
 * @param $version
 * @param $prevBlockHash
 * @param $txCollection
 * @param $time
 * @param $bits
 * @param $nonce
 * @return Block
 * @throws \BitWasp\Bitcoin\Exceptions\InvalidHashLengthException
 * @throws \BitWasp\Bitcoin\Exceptions\MerkleTreeEmpty
 * @throws \Exception
 */
function getBlock($version, $prevBlockHash, $txCollection, $time, $bits, $nonce)
{
    return new Block(
        new Math(),
        new BlockHeader(
            $version,
            Buffer::hex($prevBlockHash, 32),
            (new MerkleRoot(new Math(), $txCollection))->calculateHash(),
            $time,
            $bits,
            $nonce
        ),
        ...$txCollection
    );
}

/**
 * @param $timestamp
 * @param $time
 * @param $nonce
 * @param $bits
 * @param $version
 * @param $amount
 * @param $publicKey
 * @return Block
 * @throws \BitWasp\Bitcoin\Exceptions\InvalidHashLengthException
 * @throws \BitWasp\Bitcoin\Exceptions\MerkleTreeEmpty
 * @throws \Exception
 */
function createGenesisBlock($timestamp, $time, $nonce, $bits, $version, $amount, $publicKey)
{
    return getBlock(
        $version,
        "0000000000000000000000000000000000000000000000000000000000000000",
        [
            (new TxBuilder)
                ->version(1)
                ->input(
                    "0000000000000000000000000000000000000000000000000000000000000000",
                    0xffffffff,
                    ScriptFactory::create()
                        ->push(Buffer::int('486604799', 4)->flip())
                        ->push(Buffer::int('4', 1))
                        ->push(new Buffer($timestamp))
                        ->getScript()
                )
                ->output(
                    $amount,
                    ScriptFactory::sequence([
                        Buffer::hex($publicKey),
                        Opcodes::OP_CHECKSIG
                    ])
                )
                ->locktime(0)
                ->get()
        ],
        $time,
        $bits,
        $nonce
    );
}

/**
 * @throws \BitWasp\Bitcoin\Exceptions\InvalidHashLengthException
 * @throws \BitWasp\Bitcoin\Exceptions\MerkleTreeEmpty
 */
function generate()
{
    $nonce = 0 ;
    while (true){

        $genesis = createGenesisBlock(
            "DummyNet 15/Aug/2020",
            1597504370,
            ++$nonce,
            0x1e0ffff0,
            1,
            5000000000, // 50 * COIN
            "04f14ffe1017db891b93c601c2d3bf97dd8ae836e8eb209eb198f81fb74da493f0c2e6d5d3e2bc36ae6e68790e781f6c32397a15d28b1af58096a6513ad0cf3031"
        );

        if (substr($genesis->getHeader()->getHash()->getHex(), 0, 3) !== '000')
            continue;

        return vsprintf(
            <<<EOF
            
            FOUND:
            ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
            nonce: %s
            merkle root hex: %s
            header hex: %s
            header hash hex: %s
            hex: %s
            
            EOF,
            [
                $genesis->getHeader()->getNonce(),
                $genesis->getMerkleRoot()->getHex(),
                $genesis->getHeader()->getHex(),
                $genesis->getHeader()->getHash()->getHex(),
                $genesis->getHex(),
            ]
        );

    }
}
