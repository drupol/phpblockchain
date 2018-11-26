<?php

declare(strict_types=1);

namespace spec\drupol\phpblockchain\Miner;

use drupol\phpblockchain\Block\Block;
use drupol\phpblockchain\Miner\HashValidationStrategy\StartWithZeros;
use drupol\phpblockchain\Miner\Miner;
use PhpSpec\ObjectBehavior;
use tests\drupol\phpblockchain\Hasher\Md5;

class MinerSpec extends ObjectBehavior
{
    public function it_can_mine_a_block()
    {
        $block = Block::fromArray([
            'timestamp' => '123',
            'data' => 'foo',
        ]);

        $this
            ->mine($block)
            ->serialize()
            ->shouldReturn('{"previous_hash":null,"nonce":41,"difficulty":1,"timestamp":"123","data":"foo","hash":"a49759c402b87e712f6dd52576008b44"}');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Miner::class);
    }

    public function let()
    {
        $this->beConstructedWith(new Md5(), new StartWithZeros());
    }
}
