<?php

declare(strict_types=1);

namespace drupol\phpblockchain\Block;

use Serializable;

/**
 * Interface BlockInterface.
 */
interface BlockInterface extends Serializable
{
    /**
     * @param array $data
     *
     * @return \drupol\phpblockchain\Block\BlockInterface
     */
    public static function fromArray(array $data): BlockInterface;

    /**
     * Get the data for the block.
     *
     * @return string|null
     */
    public function getData(): ?string;

    /**
     * Get the difficulty for the mining.
     *
     * @return int
     */
    public function getDifficulty(): int;

    /**
     * Get the hash for the block.
     *
     * @return string|null
     */
    public function getHash(): ?string;

    /**
     * Get the nonce result from mining.
     *
     * @return int|null
     */
    public function getNonce(): ?int;

    /**
     * Get the previous hash, if available.
     *
     * @return string|null
     */
    public function getPreviousHash(): ?string;

    /**
     * Get the timestamp of the block creation.
     *
     * @return string
     */
    public function getTimestamp(): string;

    /**
     * @param string $data
     *
     * @return \drupol\phpblockchain\Block\BlockInterface
     */
    public function withData(string $data): BlockInterface;

    /**
     * @param string $hash
     *
     * @return \drupol\phpblockchain\Block\BlockInterface
     */
    public function withHash(?string $hash = null): BlockInterface;

    /**
     * @param int $nonce
     *
     * @return \drupol\phpblockchain\Block\BlockInterface
     */
    public function withNonce(int $nonce): BlockInterface;

    /**
     * @param string $hash
     *
     * @return \drupol\phpblockchain\Block\BlockInterface
     */
    public function withPreviousHash(?string $hash = null): BlockInterface;
}
