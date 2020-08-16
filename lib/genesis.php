<?php

declare(strict_types=1);

namespace PlainCash\Lib\Genesis;

use BitWasp\Bitcoin\Block\Block;
use BitWasp\Bitcoin\Block\BlockHeader;
use BitWasp\Bitcoin\Block\MerkleRoot;
use BitWasp\Bitcoin\Exceptions\InvalidHashLengthException;
use BitWasp\Bitcoin\Exceptions\MerkleTreeEmpty;
use BitWasp\Bitcoin\Math\Math;
use BitWasp\Bitcoin\Script\Opcodes;
use BitWasp\Bitcoin\Script\ScriptFactory;
use BitWasp\Bitcoin\Transaction\Factory\TxBuilder;
use BitWasp\Buffertools\Buffer;
use Exception;

/**
 * @param $version
 * @param $prevBlockHash
 * @param $txCollection
 * @param $time
 * @param $bits
 * @param $nonce
 * @return Block
 * @throws InvalidHashLengthException
 * @throws MerkleTreeEmpty
 * @throws Exception
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
 * @throws InvalidHashLengthException
 * @throws MerkleTreeEmpty
 * @throws Exception
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
 * @param string $pszTimestamp
 * @param int $time
 * @param int $bits
 * @param int $version
 * @param int $amount
 * @param string $publicKey
 * @return Block|null
 * @throws InvalidHashLengthException
 * @throws MerkleTreeEmpty
 */
function generate(string $pszTimestamp, int $time, int $bits, int $version, int $amount, string $publicKey)
{
    $nonce = 0 ;
    while (true){

        $genesis = createGenesisBlock(
            $pszTimestamp,
            $time,
            ++$nonce,
            $bits,
            $version,
            $amount,
            $publicKey
        );

        // TODO: do it right, according with difficulty (bits).
        if (substr($genesis->getHeader()->getHash()->getHex(), 0, 3) !== '000')
            continue;

        break;
    }

    return $genesis ?? null;
}
