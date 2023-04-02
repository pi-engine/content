<?php

namespace Content\Model;

class Log
{
    private mixed  $id;
    private int    $user_id;
    private string $action;
    private string $event;
    private string $type;
    private string $date;
    private int    $time_create;

    /**
     * @param mixed $id
     * @param int $user_id
     * @param string $action
     * @param string $event
     * @param string $type
     * @param string $date
     * @param int $time_create
     */
    public function __construct(mixed $id, int $user_id, string $action, string $event, string $type, string $date, int $time_create)
    {
        $this->id = $id;
        $this->user_id = $user_id;
        $this->action = $action;
        $this->event = $event;
        $this->type = $type;
        $this->date = $date;
        $this->time_create = $time_create;
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
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action): void
    {
        $this->action = $action;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @param string $event
     */
    public function setEvent(string $event): void
    {
        $this->event = $event;
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
    public function getDate(): string
    {
        return $this->date;
    }

    /**
     * @param string $date
     */
    public function setDate(string $date): void
    {
        $this->date = $date;
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

}
