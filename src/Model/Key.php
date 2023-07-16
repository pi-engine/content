<?php

namespace Content\Model;

class Key
{
    private mixed $id;
    private string $key;
    private string $value;
    private string $type;
    private string $suffix;
    private string $option;
    private string $logo;
    private int $status;

    /**
     * @param mixed $id
     * @param string $key
     * @param string $value
     * @param string $type
     * @param string $suffix
     * @param string $option
     * @param string $logo
     * @param int $status
     */
    public function __construct(mixed $id, string $key, string $value, string $type, string $suffix,string $option, string $logo, int $status)
    {
        $this->id = $id;
        $this->key = $key;
        $this->value = $value;
        $this->type = $type;
        $this->suffix = $suffix;
        $this->option = $option;
        $this->logo = $logo;
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @param mixed $id
     */
    public function setId(mixed $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     */
    public function setKey(string $key): void
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value): void
    {
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @return string
     */
    public function getSuffix(): string
    {
        return $this->suffix;
    }

    /**
     * @param string $suffix
     */
    public function setSuffix(string $suffix): void
    {
        $this->suffix = $suffix;
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->logo;
    }

    /**
     * @param string $logo
     */
    public function setLogo(string $logo): void
    {
        $this->logo = $logo;
    }

    /**
     * @return int
     */
    public function getStatus(): int
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus(int $status): void
    {
        $this->status = $status;
    }

    /**
     * @return string
     */
    public function getOption(): string
    {
        return $this->option;
    }

    /**
     * @param string $option
     */
    public function setOption(string $option): void
    {
        $this->option = $option;
    }


}