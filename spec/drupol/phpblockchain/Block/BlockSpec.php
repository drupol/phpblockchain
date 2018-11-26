<?php

declare(strict_types=1);

namespace spec\drupol\phpblockchain\Block;

use drupol\phpblockchain\Block\Block;
use drupol\phpblockchain\Block\BlockInterface;
use Exception;
use PhpSpec\ObjectBehavior;

class BlockSpec extends ObjectBehavior
{
    public function it_can_be_jsonserialized()
    {
        $this->shouldImplement('\JsonSerializable');

        $this->jsonSerialize()->shouldBeArray();
        $this->jsonSerialize()->shouldHaveKeyWithValue('difficulty', 2);
        $this->jsonSerialize()->shouldHaveKeyWithValue('nonce', null);
        $this->jsonSerialize()->shouldHaveKeyWithValue('timestamp', '123');
        $this->jsonSerialize()->shouldHaveKeyWithValue('data', 'hello world');
        $this->jsonSerialize()->shouldHaveKeyWithValue('hash', null);
        $this->jsonSerialize()->shouldHaveKeyWithValue('previous_hash', null);
    }

    public function it_can_be_serialized()
    {
        $this->serialize()->shouldReturn('{"previous_hash":null,"nonce":null,"difficulty":2,"timestamp":"123","data":"hello world","hash":null}');

        $text = "\xB1\x31";
        $this
            ->withData($text)
            ->shouldThrow(Exception::class)
            ->during('serialize');
    }

    public function it_can_be_unserialized()
    {
        $this
            ->unserialize('{"previous_hash":null,"nonce":null,"difficulty":2,"timestamp":"123","data":"hello world","hash":null}')
            ->shouldBeAnInstanceOf(BlockInterface::class);

        $this
            ->getDifficulty()
            ->shouldReturn(2);
    }

    public function it_can_create_a_new_block_with_hash()
    {
        $this->withHash('999')->serialize()->shouldReturn('{"previous_hash":null,"nonce":null,"difficulty":2,"timestamp":"123","data":"hello world","hash":"999"}');
    }

    public function it_can_create_a_new_block_with_nonce()
    {
        $this->withNonce(321)->serialize()->shouldReturn('{"previous_hash":null,"nonce":321,"difficulty":2,"timestamp":"123","data":"hello world","hash":null}');
    }

    public function it_can_set_data()
    {
        $this
            ->withData('foo')
            ->serialize()
            ->shouldReturn('{"previous_hash":null,"nonce":null,"difficulty":2,"timestamp":"123","data":"foo","hash":null}');
    }

    public function it_has_withers()
    {
        $this
            ->withData('foo')
            ->shouldNotReturn($this);

        $this
            ->withHash('foo')
            ->shouldNotReturn($this);

        $this
            ->withNonce(123)
            ->shouldNotReturn($this);

        $this
            ->withPreviousHash('foo')
            ->shouldNotReturn($this);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Block::class);
    }

    public function let()
    {
        $state = [
            'data' => 'hello world',
            'difficulty' => 2,
            'timestamp' => '123',
        ];

        $this->beConstructedWith(null, null, $state['difficulty'], $state['timestamp'], $state['data'], null);
    }
}
