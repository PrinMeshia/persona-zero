<?php
namespace Model;

use Manager\CategoryManager;
use Core\Database\Model;

class Category extends Model
{
    /**
     * @var integer
     */
    private $id;
    /**
     * @var string
     */
    private $title;
    /**
     * @var string
     */
    private $slug;
    /**
     * @inheritdoc
     */
    public static function metadata()
    {
        return [
            "table" => "category",
            "primaryKey" => "id",
            "columns" => [
                "id" => [
                    "type" => "integer",
                    "property" => "id"
                ],
                "title" => [
                    "type" => "string",
                    "property" => "title"
                ],
                "slug" => [
                    "type" => "string",
                    "property" => "slug"
                ]
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public static function getManager()
    {
        return CategoryManager::class;
    }
    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }
    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
    /**
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }
    /**
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }
    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
    /**
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }
}