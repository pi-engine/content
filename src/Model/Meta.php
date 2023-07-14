<?php

namespace Content\Model;

class Meta
{
    private mixed $id;
    private int $item_id;
    private string $item_slug;
    private string $item_type;
    private string $meta_key;
    private string $value_string;
    private string $value_id;
    private int $value_number;
    private string $value_slug;
    private int $status;
    private string $logo;
    private int $time_create;
    private int $time_update;
    private int $time_delete;

    /**
     * @param mixed $id
     * @param int $item_id
     * @param string $item_slug
     * @param string $item_type
     * @param string $meta_key
     * @param string $value_string
     * @param string $value_id
     * @param int $value_number
     * @param string $value_slug
     * @param int $status
     * @param string $logo
     * @param int $time_create
     * @param int $time_update
     * @param int $time_delete
     */
    public function __construct(mixed $id, int $item_id, string $item_slug, string $item_type, string $meta_key, string $value_string, string $value_id, int $value_number, string $value_slug, int $status, string $logo, int $time_create, int $time_update, int $time_delete)
    {
        $this->id = $id;
        $this->item_id = $item_id;
        $this->item_slug = $item_slug;
        $this->item_type = $item_type;
        $this->meta_key = $meta_key;
        $this->value_string = $value_string;
        $this->value_id = $value_id;
        $this->value_number = $value_number;
        $this->value_slug = $value_slug;
        $this->status = $status;
        $this->logo = $logo;
        $this->time_create = $time_create;
        $this->time_update = $time_update;
        $this->time_delete = $time_delete;
    }

    /**
     * @return string
     */
    public function getItemSlug(): string
    {
        return $this->item_slug;
    }

    /**
     * @param string $item_slug
     */
    public function setItemSlug(string $item_slug): void
    {
        $this->item_slug = $item_slug;
    }

    /**
     * @return string
     */
    public function getItemType(): string
    {
        return $this->item_type;
    }

    /**
     * @param string $item_type
     */
    public function setItemType(string $item_type): void
    {
        $this->item_type = $item_type;
    }


    /**
     * @return string
     */
    public function getValueSlug(): string
    {
        return $this->value_slug;
    }

    /**
     * @param string $value_slug
     */
    public function setValueSlug(string $value_slug): void
    {
        $this->value_slug = $value_slug;
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
     * @return int
     */
    public function getItemId(): int
    {
        return $this->item_id;
    }

    /**
     * @param int $item_id
     */
    public function setItemId(int $item_id): void
    {
        $this->item_id = $item_id;
    }

    /**
     * @return string
     */
    public function getMetaKey(): string
    {
        return $this->meta_key;
    }

    /**
     * @param string $meta_key
     */
    public function setMetaKey(string $meta_key): void
    {
        $this->meta_key = $meta_key;
    }

    /**
     * @return string
     */
    public function getValueString(): string
    {
        return $this->value_string;
    }

    /**
     * @param string $value_string
     */
    public function setValueString(string $value_string): void
    {
        $this->value_string = $value_string;
    }

    /**
     * @return string
     */
    public function getValueId(): string
    {
        return $this->value_id;
    }

    /**
     * @param string $value_id
     */
    public function setValueId(string $value_id): void
    {
        $this->value_id = $value_id;
    }

    /**
     * @return int
     */
    public function getValueNumber(): int
    {
        return $this->value_number;
    }

    /**
     * @param int $value_number
     */
    public function setValueNumber(int $value_number): void
    {
        $this->value_number = $value_number;
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
    public function getTimeCreate(): int
    {
        return $this->time_create;
    }

    /**
     * @param int $time_create
     */
    public function setTimeCreate(int $time_create): void
    {
        $this->time_create = $time_create;
    }

    /**
     * @return int
     */
    public function getTimeUpdate(): int
    {
        return $this->time_update;
    }

    /**
     * @param int $time_update
     */
    public function setTimeUpdate(int $time_update): void
    {
        $this->time_update = $time_update;
    }

    /**
     * @return int
     */
    public function getTimeDelete(): int
    {
        return $this->time_delete;
    }

    /**
     * @param int $time_delete
     */
    public function setTimeDelete(int $time_delete): void
    {
        $this->time_delete = $time_delete;
    }


}