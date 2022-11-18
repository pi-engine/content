<?php

namespace Content\Model;

class Meta
{
    private mixed  $id;
    private int    $item_id;
    private int    $time_create;
    private int    $status;
    private string $key;
    private string $value_string;
    private int    $value_number;

    public function __construct(
        $item_id,
        $time_create,
        $status,
        $key,
        $value_string,
        $value_number,
        $id = null
    ) {
        $this->item_id      = $item_id;
        $this->time_create  = $time_create;
        $this->status       = $status;
        $this->key          = $key;
        $this->value_string = $value_string;
        $this->value_number = $value_number;
        $this->id           = $id;
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return $this->item_id;
    }

    /**
     * @return int
     */
    public function getTimeCreate(): int
    {
        return $this->time_create;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
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
    public function getValueString(): string
    {
        return $this->value_string;
    }

    /**
     * @return int
     */
    public function getValueNumber(): int
    {
        return $this->value_number;
    }
}