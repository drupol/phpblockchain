<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Block;

use Exception;
use JsonSerializable;

/**
 * Class Block.
 */
final class Block implements BlockInterface, JsonSerializable
{
    /**
     * @var string|null
     */
    private $data;

    /**
     * @var int
     */
    private $difficulty;

    /**
     * @var string|null
     */
    private $hash;

    /**
     * @var int|null
     */
    private $nonce;

    /**
     * @var string|null
     */
    private $previous_hash;

    /**
     * @var string
     */
    private $timestamp;

    /**
     * Block constructor.
     *
     * @param string|null $previous_hash
     * @param int|null $nonce
     * @param int $difficulty
     * @param string $timestamp
     * @param string|null $data
     * @param string|null $hash
     */
    public function __construct(
        ?string $previous_hash,
        ?int $nonce,
        int $difficulty,
        string $timestamp,
        ?string $data,
        ?string $hash
    ) {
        $this->previous_hash = $previous_hash;
        $this->nonce = $nonce;
        $this->difficulty = $difficulty;
        $this->timestamp = $timestamp;
        $this->data = $data;
        $this->hash = $hash;
    }

    /**
     * {@inheritdoc}
     */
    public static function fromArray(array $data): BlockInterface
    {
        $keys = [
            'previous_hash' => null,
            'nonce' => null,
            'difficulty' => 1,
            'timestamp' => (string) time(),
            'data' => null,
            'hash' => null,
        ];

        $ordered_array = array_merge(
            $keys,
            $data
        );

        return new self(...array_values($ordered_array));
    }

    /**
     * {@inheritdoc}
     */
    public function getData(): ?string
    {
        return $this->data;
    }

    /**
     * {@inheritdoc}
     */
    public function getDifficulty(): int
    {
        return $this->difficulty;
    }

    /**
     * {@inheritdoc}
     */
    public function getHash(): ?string
    {
        return $this->hash;
    }

    /**
     * {@inheritdoc}
     */
    public function getNonce(): ?int
    {
        return $this->nonce;
    }

    /**
     * {@inheritdoc}
     */
    public function getPreviousHash(): ?string
    {
        return $this->previous_hash;
    }

    /**
     * {@inheritdoc}
     */
    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    /**
     * {@inheritdoc}
     */
    public function jsonSerialize(): array
    {
        return [
            'previous_hash' => $this->getPreviousHash(),
            'nonce' => $this->getNonce(),
            'difficulty' => $this->getDifficulty(),
            'timestamp' => $this->getTimestamp(),
            'data' => $this->getData(),
            'hash' => $this->getHash(),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        $json = json_encode($this->jsonSerialize());

        if (false === $json) {
            throw new Exception('Unable to serialize the block.');
        }

        return $json;
    }

    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        return new self(...array_values(json_decode($serialized, true)));
    }

    /**
     * {@inheritdoc}
     */
    public function withData(string $data): BlockInterface
    {
        $clone = clone $this;
        $clone->data = $data;

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withHash(?string $hash = null): BlockInterface
    {
        $clone = clone $this;
        $clone->hash = $hash;

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withNonce(int $nonce): BlockInterface
    {
        $clone = clone $this;
        $clone->nonce = $nonce;

        return $clone;
    }

    /**
     * {@inheritdoc}
     */
    public function withPreviousHash(?string $hash = null): BlockInterface
    {
        $clone = clone $this;
        $clone->previous_hash = $hash;

        return $clone;
    }
}
