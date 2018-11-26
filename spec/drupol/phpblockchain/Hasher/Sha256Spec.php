<?php

declare(strict_types=1);

namespace spec\drupol\phpblockchain\Hasher;

use drupol\phpblockchain\Hasher\Sha256;
use PhpSpec\ObjectBehavior;

class Sha256Spec extends ObjectBehavior
{
    public function it_can_get_a_hash()
    {
        $hash = $this
            ->hash('foo');

        $this
            ->unpack($hash->getWrappedObject())
            ->shouldReturn('2c26b46b68ffc68ff99b453c1d30413413422d706483bfa0f98a5e886266e7ae');

        $this
            ->hash('foo', false)
            ->shouldReturn('2c26b46b68ffc68ff99b453c1d30413413422d706483bfa0f98a5e886266e7ae');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(Sha256::class);
    }
}
