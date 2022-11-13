<?php

namespace Content\Model\Item;

class Item
{
    private mixed $id;
    private $slug;
    private $title;
    private $description;
    private $image;
    private $image_uri;
    private $type;
    private $status;
    private $author_id;

    /**
     * @param mixed $id
     * @param $slug
     * @param $title
     * @param $description
     * @param $image
     * @param $image_uri
     * @param $type
     * @param $status
     * @param $author_id
     */
    public function __construct(mixed $id, $slug, $title, $description, $image, $image_uri, $type, $status, $author_id)
    {
        $this->id = $id;
        $this->slug = $slug;
        $this->title = $title;
        $this->description = $description;
        $this->image = $image;
        $this->image_uri = $image_uri;
        $this->type = $type;
        $this->status = $status;
        $this->author_id = $author_id;
    }

    /**
     * @return mixed
     */
    public function getId(): mixed
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @return mixed
     */
    public function getImageUri()
    {
        return $this->image_uri;
    }

    /**
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @return mixed
     */
    public function getAuthorId()
    {
        return $this->author_id;
    }
    

}
