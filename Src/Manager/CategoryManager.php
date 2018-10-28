<?php

namespace Manager;

use Core\Database\Manager;
use Model\Category;

class CategoryManager extends Manager
{
    public function FindPaginated($page)
    {
        $start = ($page - 1) * 10;
        $statement = $this->pdo->prepare(sprintf("SELECT * FROM %s LIMIT %d,10","category", $start));
        $statement->execute();
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        array_walk($results, function (&$category) {
            $category = (new Category())->hydrate($category);
        });
        return $results;
    }
}