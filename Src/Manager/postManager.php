<?php

namespace Manager;

use Core\Database\Manager;
use Model\Post;

class PostManager extends Manager
{
    public function FindPaginated($page)
    {
        $start = ($page - 1) * 10;
        $statement = $this->pdo->prepare(sprintf("SELECT p.id,p.title,p.slug,c.title category FROM %s p LEFT JOIN category c ON p.category = c.id ORDER BY created_at DESC LIMIT %d,10","post", $start));
        $statement->execute();
        $results = $statement->fetchAll(\PDO::FETCH_ASSOC);
        array_walk($results, function (&$post) {
            $post = (new Post())->hydrate($post);
        });
        return $results;
    }
}

