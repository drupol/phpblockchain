<?php

declare(strict_types=1);

namespace spec\drupol\phpblockchain\Miner\HashValidationStrategy;

use drupol\phpblockchain\Miner\HashValidationStrategy\StartWithZeros;
use InvalidArgumentException;
use PhpSpec\ObjectBehavior;

class StartWithZerosSpec extends ObjectBehavior
{
    public function it_can_check_if_the_difficulty_is_greater_or_equal_to_one()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('hashMatch', ['0000123456789', 0]);
    }

    public function it_can_check_if_the_difficulty_is_smaller_than_the_hash()
    {
        $this->shouldThrow(InvalidArgumentException::class)->during('hashMatch', ['0000123456789', 13]);
    }

    public function it_can_validate_a_hash()
    {
        $this->hashMatch('0000123456789', 4)->shouldReturn(true);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(StartWithZeros::class);
    }
}
