#!/usr/bin/env php
<?php

use Symfony\Component\OptionsResolver\Options;
use Symfony\Component\OptionsResolver\OptionsResolver;
use function PlainCash\Lib\Genesis\generate;

require __DIR__.'/../vendor/autoload.php';

$parser = new Console_CommandLine();
$parser->description = <<<EOF
Generation of genesis block by given params.

Example:
  ./bin/genesis.php \
  -p "DummyNet 15/Aug/2020" \
  -t 1597504370 \
  -k "04f14ffe1017db891b93c601c2d3bf97dd8ae836e8eb209eb198f81fb74da493f0c2e6d5d3e2bc36ae6e68790e781f6c32397a15d28b1af58096a6513ad0cf3031"
EOF;
$parser->version = '0.0.1';
$parser->addOption('pszTimestamp', array(
    'short_name'  => '-p',
    'long_name'   => '--psztimestamp',
    'description' => 'pszTimestamp. Doc 0001-genesis.md',
    'help_name'   => 'PSZTIMESTAMP',
    'action'      => 'StoreString'
));
$parser->addOption('time', array(
    'short_name'  => '-t',
    'long_name'   => '--time',
    'description' => 'Unix timestamp',
    'help_name'   => 'TIME',
    'action'      => 'StoreString'
));
$parser->addOption('bits', array(
    'short_name'  => '-b',
    'long_name'   => '--bits',
    'description' => 'Bits. Doc 0001-genesis.md',
    'help_name'   => 'BITS',
    'action'      => 'StoreString'
));
$parser->addOption('version', array(
    'short_name'  => '-v',
    'long_name'   => '--version',
    'description' => 'Block version number',
    'help_name'   => 'VERSION',
    'action'      => 'StoreString'
));
$parser->addOption('amount', array(
    'short_name'  => '-a',
    'long_name'   => '--amount',
    'description' => 'Amount',
    'help_name'   => 'AMOUNT',
    'action'      => 'StoreString'
));
$parser->addOption('publicKey', array(
    'short_name'  => '-k',
    'long_name'   => '--key',
    'description' => 'Public key',
    'help_name'   => 'KEY',
    'action'      => 'StoreString'
));

try {

    $resolver = (new OptionsResolver())
        ->setRequired([
            'pszTimestamp',
            'publicKey',
        ])
        ->setDefined([
            'pszTimestamp',
            'time',
            'bits',
            'version',
            'amount',
            'publicKey',
            'help',
        ])
        ->setDefaults([
            'time' => time(),
            'bits' => 0x1e0ffff0,
            'version' => 1,
            'amount' => 5000000000,
        ])
        ->setNormalizer('time', function (Options $options, $value) { return (int)$value; })
        ->setNormalizer('bits', function (Options $options, $value) { return (int)$value; })
        ->setNormalizer('version', function (Options $options, $value) { return (int)$value; })
        ->setNormalizer('amount', function (Options $options, $value) { return (int)$value; })
        ->setAllowedTypes('pszTimestamp', 'string')
        ->setAllowedTypes('time', ['integer', 'string', 'null'])
        ->setAllowedTypes('bits', ['integer', 'string', 'null'])
        ->setAllowedTypes('version', ['integer', 'string', 'null'])
        ->setAllowedTypes('amount', ['integer', 'string', 'null'])
        ->setAllowedTypes('publicKey', 'string');


    $options = $resolver->resolve(array_filter($parser->parse()->options));

    $genesis = generate(
        $options['pszTimestamp'],
        $options['time'],
        $options['bits'],
        $options['version'],
        $options['amount'],
        $options['publicKey'],
    );

    printf(
        <<<EOF
            
        INPUT:
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        %s
        
        FOUND:
        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        nonce: %s
        merkle root hex: %s
        header hex: %s
        header hash hex: %s
        hex: %s
        
        EOF,
        var_export($options, true),
        $genesis->getHeader()->getNonce(),
        $genesis->getMerkleRoot()->getHex(),
        $genesis->getHeader()->getHex(),
        $genesis->getHeader()->getHash()->getHex(),
        $genesis->getHex()
    );



} catch (Exception $exc) {
    $parser->displayError($exc->getMessage());
}
