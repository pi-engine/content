<?php

namespace Content\Model;

class Key
{
    private mixed  $id;
    private string $key;
    private string $type;
    private int    $status;

    public function __construct(
        $key,
        $type,
        $status,
        $id = null
    ) {
        $this->status = $status;
        $this->key    = $key;
        $this->type   = $type;
        $this->id     = $id;
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }
}