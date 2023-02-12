<?php

namespace Content\Model;

class Item
{
    private mixed  $id;
    private string $title;
    private string $slug;
    private string $type;
    private int    $status;

    private int    $user_id;
    private int    $time_create;
    private int    $time_update;
    private int    $time_delete;
    private string $information;

    public function __construct(
        $title,
        $slug,
        $type,
        $status,
        $user_id,
        $time_create,
        $time_update,
        $time_delete,
        $information,
        $id = null
    ) {
        $this->title       = $title;
        $this->slug        = $slug;
        $this->type        = $type;
        $this->status      = $status;
        $this->user_id     = $user_id;
        $this->time_create = $time_create;
        $this->time_update = $time_update;
        $this->time_delete = $time_delete;
        $this->information = $information;
        $this->id          = $id;
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
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
    public function getTimeCreate(): int
    {
        return $this->time_create;
    }

    /**
     * @return int
     */
    public function getTimeUpdate(): int
    {
        return $this->time_update;
    }

    /**
     * @return int
     */
    public function getTimeDelete(): int
    {
        return $this->time_delete;
    }

    /**
     * @return string
     */
    public function getInformation(): string
    {
        return $this->information;
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
     * @return int
     */
    public function getUserId(): int
    {
        return $this->user_id;
    }

    /**
     * @param int $user_id
     */
    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }
}
