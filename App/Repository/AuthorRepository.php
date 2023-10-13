<?php

namespace App\Repository;

use App\Entity\Author;
use App\Db\Mysql;

class AuthorRepository extends Repository
{

    public function findOneById(int $id): Author|bool
    {

        $query = $this->pdo->prepare('SELECT * FROM author WHERE id = :id');
        $query->bindValue(':id', $id, $this->pdo::PARAM_INT);
        $query->execute();
        $author = $query->fetch($this->pdo::FETCH_ASSOC);
        if ($author) {
            return Author::createAndHydrate($author);
        } else {
            return false;
        }
    }

    public function findAll(int $limit = null, int $page = null): array
    {
        //@todo_done coder cette partie
        $authorsArray = [];

        if (!empty($page)){
            $start_row = ($page-1) * $limit;
        }
        
        $sql = "SELECT * FROM author" 
                . " ORDER BY id ASC"
                . (!empty($limit) ? " LIMIT {$limit}" : "")
                . (isset($start_row) ? " OFFSET {$start_row}" : "");
        $query = $this->pdo->prepare($sql);
        $query->execute();
        $authors = $query->fetchAll($this->pdo::FETCH_ASSOC);

        if (!empty($authors)) {
            foreach($authors as $author) {
                $authorsArray[] = Author::createAndHydrate($author);
            }
        }

        return $authorsArray;
    }

    public function count(): int
    {

        $query = $this->pdo->prepare("SELECT COUNT(*) as total_authors FROM author");
        $query->execute();
        $total = $query->fetch($this->pdo::FETCH_ASSOC);
        if ($total && !empty($total['total_authors'])) {
            $total = $total['total_authors'];
        } else {
            $total = 0;
        }
        return $total;
    }

    public function persist(Author $author)
    {
        if ($author->getId() !== null) {
                $query = $this->pdo->prepare('UPDATE author SET last_name = :last_name, first_name = :first_name,  
                                                    nickname = :nickname  WHERE id = :id'
                );
                $query->bindValue(':id', $author->getId(), $this->pdo::PARAM_INT);
           

        } else {
            $query = $this->pdo->prepare('INSERT INTO author (last_name, first_name, nickname) 
                                                    VALUES (:last_name, :first_name, :nickname)'
            );
        }

        $query->bindValue(':last_name', $author->getLastName(), $this->pdo::PARAM_STR);
        $query->bindValue(':first_name', $author->getFirstName(), $this->pdo::PARAM_STR);
        $query->bindValue(':nickname', $author->getNickname(), $this->pdo::PARAM_STR);

        return $query->execute();
    }

    public function removeById(int $id)
    {
        $query = $this->pdo->prepare('DELETE FROM author WHERE id = :id');
        $query->bindValue(':id', $id, $this->pdo::PARAM_INT);
        $query->execute();

        if ($query->rowCount() > 0) {
            return true;
        } else {
            return false;
        }
    }
}
