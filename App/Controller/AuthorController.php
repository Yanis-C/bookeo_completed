<?php

namespace App\Controller;

use App\Entity\Author;
use App\Repository\BookRepository;
use App\Repository\CommentRepository;
use App\Entity\Book;
use App\Entity\User;
use App\Entity\Comment;
use App\Entity\Rating;
use App\Tools\FileTools;
use App\Repository\TypeRepository;
use App\Repository\AuthorRepository;
use App\Repository\RatingRepository;


class AuthorController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'add':
                        $this->add();
                        break;
                    case 'edit':
                        $this->edit();
                        break;
                    case 'delete':
                        $this->delete();
                        break;
                    case 'list':
                        $this->list();
                        break;
                    default:
                        throw new \Exception("Cette action n'existe pas : " . $_GET['action']);
                        break;
                }
            } else {
                throw new \Exception("Aucune action détectée");
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function add()
    {
        $this->add_edit();
    }

    protected function edit()
    {
        try {
            if (isset($_GET['id'])) {
                $this->add_edit((int)$_GET['id']);
            } else {
                throw new \Exception("L'id est manquant en paramètre");
            }
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function add_edit($id = null)
    {

        try {
            // Cette action est réservé aux admin
            if (!User::isLogged() || !User::isAdmin()) {
                throw new \Exception("Accès refusé");
            }
            $authorRepository = new AuthorRepository();
            $errors = [];
            if (is_null($id)) {
                $author = new Author();
            } else {
                $author = $authorRepository->findOneById($id);
                if (!$author) {
                    throw new \Exception("L'auteur n'existe pas");
                }
            }

            if (isset($_POST['saveAuthor'])) {
                $author = Author::createAndHydrate($_REQUEST);
                $errors = $author->validate();

                if (empty($errors)) {    
                    $authorRepository->persist($author);

                    header("Location: index.php?controller=author&action=list");
                }
            }

            $this->render('author/add_edit', [
                'author' => $author,
                'pageTitle' => $author->getId() ? "Éditer un auteur" : "Créer un auteur",
                'errors' => $errors ?? ''
            ]);
        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }

    protected function list()
    {

        $authorRepository = new AuthorRepository;

        // On récupère la page courante, si page de page on met à 1
        if (isset($_GET['page'])) {
            $page = (int)$_GET['page'];
        } else {
            $page = 1;
        }

        $authors = $authorRepository->findAll(ADMIN_AUTHOR_PER_PAGE_, $page);
        $total_authors = $authorRepository->count();

        $nb_pages = ceil($total_authors / ADMIN_AUTHOR_PER_PAGE_);

        $this->render('author/list', [
            'authors' => $authors,
            'totalPages' => $nb_pages,
            'page' => $page,
        ]);
    }


    protected function delete()
    {
        try {
            // Cette action est réservé aux admin
            if (!User::isLogged() || !User::isAdmin()) {
                throw new \Exception("Accès refusé");
            }

            if (!isset($_GET['id'])) {
                throw new \Exception("L'id est manquant en paramètre");
            }
            $authorRepository = new AuthorRepository();

            $id = (int)$_GET['id'];

            $author = $authorRepository->findOneById($id);

            if (!$author) {
                throw new \Exception("Le livre n'existe pas");
            }
            if ($authorRepository->removeById($id)) {
                // On redirige vers la liste de livre
                header('location: index.php?controller=author&action=list&alert=delete_confirm');
            } else {
                throw new \Exception("Une erreur est survenue lors de la suppression");
            }

        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
