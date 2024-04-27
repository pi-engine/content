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
    private mixed $priority;

    /**
     * @param mixed $id
     * @param string $title
     * @param string $slug
     * @param string $type
     * @param int $status
     * @param int $user_id
     * @param int $time_create
     * @param int $time_update
     * @param int $time_delete
     * @param string $information
     * @param mixed $priority
     */
    public function __construct(mixed $id, string $title, string $slug, string $type, int $status, int $user_id, int $time_create, int $time_update, int $time_delete, string $information, mixed $priority)
    {
        $this->id = $id;
        $this->title = $title;
        $this->slug = $slug;
        $this->type = $type;
        $this->status = $status;
        $this->user_id = $user_id;
        $this->time_create = $time_create;
        $this->time_update = $time_update;
        $this->time_delete = $time_delete;
        $this->information = $information;
        $this->priority = $priority;
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
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getSlug(): string
    {
        return $this->slug;
    }

    /**
     * @param string $slug
     */
    public function setSlug(string $slug): void
    {
        $this->slug = $slug;
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

    /**
     * @return string
     */
    public function getInformation(): string
    {
        return $this->information;
    }

    /**
     * @param string $information
     */
    public function setInformation(string $information): void
    {
        $this->information = $information;
    }

    /**
     * @return mixed
     */
    public function getPriority(): mixed
    {
        return $this->priority;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority(mixed $priority): void
    {
        $this->priority = $priority;
    }


}
