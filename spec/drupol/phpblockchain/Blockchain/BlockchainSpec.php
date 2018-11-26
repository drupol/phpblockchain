<?php

declare(strict_types=1);

namespace spec\drupol\phpblockchain\Blockchain;

use drupol\phpblockchain\Block\Block;
use drupol\phpblockchain\Blockchain\Blockchain;
use drupol\phpblockchain\Blockchain\BlockchainInterface;
use drupol\phpblockchain\Miner\HashValidationStrategy\StartWithZeros;
use drupol\phpblockchain\Miner\Miner;
use Exception;
use Iterator;
use PhpSpec\ObjectBehavior;
use tests\drupol\phpblockchain\Hasher\Md5;

class BlockchainSpec extends ObjectBehavior
{
    public function it_can_add_blocks()
    {
        $this->isValid()->shouldReturn(true);

        $blockchain = [
            [
                'previous_hash' => '7f85b1740a0476d351d889c90e704194',
                'nonce' => 3162,
                'difficulty' => 4,
                'timestamp' => '1543335203.468',
                'data' => 'Hello world 1',
                'hash' => '66136d549f22ad2b4adb5844509639d6',
            ],
            [
                'previous_hash' => '66136d549f22ad2b4adb5844509639d6',
                'nonce' => 24554,
                'difficulty' => 4,
                'timestamp' => '1543335203.5249',
                'data' => 'Hello world 2',
                'hash' => '4a615853b496eb4cc83002082c24ff5b',
            ],
            [
                'previous_hash' => '4a615853b496eb4cc83002082c24ff5b',
                'nonce' => 112621,
                'difficulty' => 4,
                'timestamp' => '1543335203.9476',
                'data' => 'Hello world 3',
                'hash' => 'e03d6689b1bb77b905608467091ee336',
            ],
            [
                'previous_hash' => 'e03d6689b1bb77b905608467091ee336',
                'nonce' => 40873,
                'difficulty' => 4,
                'timestamp' => '1543335205.9081',
                'data' => 'Hello world 4',
                'hash' => 'bfcb7b7a8a5ecc2d2f30f9146f83bcf7',
            ],
        ];

        // Add Genesis block.
        $block = Block::fromArray($blockchain[0]);
        $this->addBlock($block)->shouldReturnAnInstanceOf(BlockchainInterface::class);
        $this->isValid()->shouldReturn(true);

        // Add a first valid block.
        $block = Block::fromArray($blockchain[1]);
        $this->addBlock($block)->shouldReturnAnInstanceOf(BlockchainInterface::class);
        $this->isValid()->shouldReturn(true);

        // Add an invalid block.
        $invalidBlock = $blockchain[2];
        $invalidBlock['data'] = 'foo';
        $block = Block::fromArray($invalidBlock);
        $this->shouldThrow(Exception::class)->during('addBlock', [$block]);
        $this->isValid()->shouldReturn(true);

        // Add a not mined block.
        $notMinedBlock = $blockchain[3];
        $notMinedBlock['nonce'] = null;
        $notMinedBlock['hash'] = null;
        $block = Block::fromArray($notMinedBlock);
        $this->shouldThrow(Exception::class)->during('addBlock', [$block]);
        $this->isValid()->shouldReturn(true);
    }

    public function it_can_be_used_like_an_array()
    {
        $blockchain = [
            [
                'previous_hash' => null,
                'nonce' => 45671,
                'difficulty' => 4,
                'timestamp' => '1543316301.3649',
                'data' => 'Hello world 0',
                'hash' => '00006d15f4b03bd340c577cb242f909a',
            ],
            [
                'previous_hash' => '00006d15f4b03bd340c577cb242f909a',
                'nonce' => 198249,
                'difficulty' => 4,
                'timestamp' => '1543316301.8911',
                'data' => 'Hello world 1',
                'hash' => '00009d19e07cbea3b9befeb04ccbdcfc',
            ],
            [
                'previous_hash' => '00009d19e07cbea3b9befeb04ccbdcfc',
                'nonce' => 71208,
                'difficulty' => 4,
                'timestamp' => '1543316304.7817',
                'data' => '****** INVALID DATA HAS BEEN ADDED HERE ON PURPOSE ******',
                'hash' => '00002f0d6b2828dc30669328953ab016',
            ],
            [
                'previous_hash' => '00002f0d6b2828dc30669328953ab016',
                'nonce' => 20436,
                'difficulty' => 4,
                'timestamp' => '1543316305.8773',
                'data' => 'Hello world 3',
                'hash' => '000019a3a0093b1ee5b88af9f0a7004e',
            ],
        ];

        $blockchain = array_map(
            static function (array $state) {
                return Block::fromArray($state);
            },
            $blockchain
        );

        $miner = new Miner(new Md5(), new StartWithZeros());

        $this->beConstructedWith($blockchain, $miner);
        $this->isValid()->shouldReturn(false);

        $this->count()->shouldReturn(4);

        $this->offsetExists(0)->shouldReturn(true);
        $this->offsetExists(4)->shouldReturn(false);
        $this[0]->shouldReturn($blockchain[0]);

        $this->shouldThrow(Exception::class)->during('offsetSet', [0, 10]);
        $block = [
            'previous_hash' => 'b2a8d855d51ab5cad5d56c67ac9a58ed',
            'nonce' => 70741,
            'difficulty' => 4,
            'timestamp' => '1543305112.1475',
            'data' => 'Hello world 1',
            'hash' => '5767452e32cd78e1b083ae93b5b67fc6',
        ];

        $block = Block::fromArray($block);
        $this->offsetSet(4, $block);

        $this[4]->shouldReturn($block);

        unset($this[4]);
        $this->offsetExists(4)->shouldReturn(false);
    }

    public function it_can_get_a_block_from_a_hash()
    {
        $hasher = new Md5();
        $miner = new Miner($hasher, new StartWithZeros());

        $this->beConstructedWith([], $miner);

        $this->isValid()->shouldReturn(true);

        $this->getLastBlock()->shouldBeNull();

        $block = [
            'previous_hash' => 'b2a8d855d51ab5cad5d56c67ac9a58ed',
            'nonce' => 70741,
            'difficulty' => 4,
            'timestamp' => '1543305112.1475',
            'data' => 'Hello world 1',
            'hash' => '5767452e32cd78e1b083ae93b5b67fc6',
        ];

        $block = Block::fromArray($block);
        $this->addBlock($block);

        $this->getBlock('5767452e32cd78e1b083ae93b5b67fc6')->shouldBe($block);

        $this->getBlock('foo')->shouldBeNull();
    }

    public function it_can_get_the_last_block()
    {
        $miner = new Miner(new Md5(), new StartWithZeros());

        $this->beConstructedWith([], $miner);

        $this->isValid()->shouldReturn(true);

        $this->getLastBlock()->shouldBeNull();

        $block = [
            'previous_hash' => 'b2a8d855d51ab5cad5d56c67ac9a58ed',
            'nonce' => 70741,
            'difficulty' => 4,
            'timestamp' => '1543305112.1475',
            'data' => 'Hello world 1',
            'hash' => '5767452e32cd78e1b083ae93b5b67fc6',
        ];

        $block = Block::fromArray($block);

        $this->addBlock($block);

        $this->getLastBlock()->shouldReturn($block);
    }

    public function it_can_have_an_iterator()
    {
        $blockchain = [
            [
                'previous_hash' => null,
                'nonce' => 45671,
                'difficulty' => 4,
                'timestamp' => '1543316301.3649',
                'data' => 'Hello world 0',
                'hash' => '00006d15f4b03bd340c577cb242f909a',
            ],
            [
                'previous_hash' => '00006d15f4b03bd340c577cb242f909a',
                'nonce' => 198249,
                'difficulty' => 4,
                'timestamp' => '1543316301.8911',
                'data' => 'Hello world 1',
                'hash' => '00009d19e07cbea3b9befeb04ccbdcfc',
            ],
            [
                'previous_hash' => '00009d19e07cbea3b9befeb04ccbdcfc',
                'nonce' => 71208,
                'difficulty' => 4,
                'timestamp' => '1543316304.7817',
                'data' => '****** INVALID DATA HAS BEEN ADDED HERE ON PURPOSE ******',
                'hash' => '00002f0d6b2828dc30669328953ab016',
            ],
            [
                'previous_hash' => '00002f0d6b2828dc30669328953ab016',
                'nonce' => 20436,
                'difficulty' => 4,
                'timestamp' => '1543316305.8773',
                'data' => 'Hello world 3',
                'hash' => '000019a3a0093b1ee5b88af9f0a7004e',
            ],
        ];

        $blockchain = array_map(
            static function (array $state) {
                return Block::fromArray($state);
            },
            $blockchain
        );

        $miner = new Miner(new Md5(), new StartWithZeros());

        $this->beConstructedWith($blockchain, $miner);

        $this
            ->getIterator()
            ->shouldYieldLike($blockchain);
    }

    public function it_can_return_an_iterator()
    {
        $this->getIterator()->shouldImplement(Iterator::class);
    }

    public function it_cannot_add_blocks_if_blockchain_is_invalid()
    {
        $blockchain = [
            [
                'previous_hash' => null,
                'nonce' => 45671,
                'difficulty' => 4,
                'timestamp' => '1543316301.3649',
                'data' => 'Hello world 0',
                'hash' => '00006d15f4b03bd340c577cb242f909a',
            ],
            [
                'previous_hash' => '00006d15f4b03bd340c577cb242f909a',
                'nonce' => 198249,
                'difficulty' => 4,
                'timestamp' => '1543316301.8911',
                'data' => 'Hello world 1',
                'hash' => '00009d19e07cbea3b9befeb04ccbdcfc',
            ],
            [
                'previous_hash' => '00009d19e07cbea3b9befeb04ccbdcfc',
                'nonce' => 71208,
                'difficulty' => 4,
                'timestamp' => '1543316304.7817',
                'data' => '****** INVALID DATA HAS BEEN ADDED HERE ON PURPOSE ******',
                'hash' => '00002f0d6b2828dc30669328953ab016',
            ],
            [
                'previous_hash' => '00002f0d6b2828dc30669328953ab016',
                'nonce' => 20436,
                'difficulty' => 4,
                'timestamp' => '1543316305.8773',
                'data' => 'Hello world 3',
                'hash' => '000019a3a0093b1ee5b88af9f0a7004e',
            ],
        ];

        $blockchain = array_map(
            static function (array $state) {
                return Block::fromArray($state);
            },
            $blockchain
        );

        $miner = new Miner(new Md5(), new StartWithZeros());

        $this->beConstructedWith($blockchain, $miner);
        $this->isValid()->shouldReturn(false);

        $newBlock = [
            'previous_hash' => '000019a3a0093b1ee5b88af9f0a7004e',
            'nonce' => 67744,
            'difficulty' => 4,
            'timestamp' => '1543316306.1999',
            'data' => 'Hello world 4',
            'hash' => '0000340e10adff7df211374fe02c373d',
        ];
        $newBlock = Block::fromArray($newBlock);

        $this
            ->shouldThrow(Exception::class)
            ->during('addBlock', [$newBlock]);

        $this
            ->hash()
            ->shouldReturn('f92ca315c4630f472088dfc26dd6b7f6');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Blockchain::class);
    }

    public function let()
    {
        $genesis_block = [
            'previous_hash' => null,
            'nonce' => 86215,
            'difficulty' => 4,
            'timestamp' => '1543335202.376',
            'data' => 'Hello world 0',
            'hash' => '7f85b1740a0476d351d889c90e704194',
        ];

        $genesis = Block::fromArray($genesis_block);

        $this->beConstructedWith([$genesis], new Miner(new Md5(), new StartWithZeros()));
    }
}
