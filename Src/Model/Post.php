<?php
namespace Model;

use Manager\PostManager;
use Core\Database\Model;

class Post extends Model
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
     * @var string
     */
    private $content;
    /**
     * @var \DateTime
     */
    private $createdAt;
    /**
     * @var \DateTime
     */
    private $updatedAt;
    /**
     * @var Category
     */
    private $category;
    /**
     * @inheritdoc
     */
    
    public static function metadata()
    {
        return [
            "table" => "post",
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
                ],
                "content" => [
                    "type" => "string",
                    "property" => "content"
                ],
                "created_at" => [
                    "type" => "datetime",
                    "property" => "createdAt"
                ],
                "updated_at" => [
                    "type" => "datetime",
                    "property" => "updatedAt"
                ],
                "category" => [
                    "type" => "object",
                    "property" => "category"
                ],
            ]
        ];
    }
    /**
     * @inheritdoc
     */
    public static function getManager()
    {
        return postManager::class;
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
    /**
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }
    /**
     * @param string $content
     */
    public function setContent($content)
    {
        $this->content = $content;
    }
    /**
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    }
    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
    /**
     * @param \DateTime $updatedAt
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
    /**
     * @return int
     */
    public function getCategory()
    {
        return $this->category;
    }
    /**
     * @param mixed $category_id
     */
    public function setCategory($category)
    {
        $this->category = $category;
    }
}