<?php

declare(strict_types=1);

namespace PlainCash\Lib\KeyPair;

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\Point\UncompressedPointSerializer;

/**
 * @param $privateKey
 * @return string
 */
function publicKey($privateKey) {
    $private = EccFactory::getSecgCurves()->generator256k1()->getPrivateKeyFrom(gmp_init($privateKey, 16));
    $point = $private->getPublicKey()->getPoint();

    return (new UncompressedPointSerializer())->serialize($point);
}

