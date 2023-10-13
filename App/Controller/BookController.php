<?php

namespace App\Controller;

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


class BookController extends Controller
{
    public function route(): void
    {
        try {
            if (isset($_GET['action'])) {
                switch ($_GET['action']) {
                    case 'show':
                        $this->show();
                        break;
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
    /*
    Exemple d'appel depuis l'url
        ?controller=book&action=show&id=1
    */
    protected function show()
    {
        $errors = [];

        try {
            if (isset($_GET['id'])) {

                $id = (int)$_GET['id'];
                // Charger le livre par un appel au repository findOneById
                $bookRepository = new BookRepository();
                $book = $bookRepository->findOneById($id);

                if ($book) {
                    //@todo_done créer une nouvelle instance de CommentRepository
                    $commentRepository = new CommentRepository();

                    //@todo_done Créer une nouvelle instance de commentaire en settant le book id et l'id de l'utilisateur connecté (User::getCurrentUserId())
                    $comment = new Comment();
                    $comment->setBookId($book->getId());
                    $comment->setUserId(User::getCurrentUserId());


                    if (isset($_POST['saveComment'])) {
                        if (!User::isLogged()) {
                            throw new \Exception("Accès refusé");
                        }
                        //@todo_done appeler la méthode hydrate du l'objet comment en passant le tableau $_POST
                        $comment->setComment($_POST['comment'] ?? "");

                        //@todo_done verifier que le commentaire est valide en appelant la commande validate
                        $errors = $comment->validate();
                        
                        if (empty($errors)) {
                            // @todo_done si il n'y a pas d'erreur, alors appeler la méthode persist de l'objet commentRepository en passant $comment

                            $commentRepository->persist($comment);

                            header("Location: index.php?controller=book&action=show&id=". 90);
                        }
                    }

                    $ratingRepository = new RatingRepository();

                    $rating = $ratingRepository->findOneByBookIdAndUserId($book->getId(), User::getCurrentUserId());
                    if (!$rating) {
                        $rating = new Rating();
                        $rating->setBookId($book->getId());
                        $rating->setUserId(User::getCurrentUserId());
                    }
                    
                    $averageRating = $ratingRepository->findAverageByBookId($book->getId());

                    if (isset($_POST['saveRating'])) {
                        if (!User::isLogged()) {
                            throw new \Exception("Accès refusé");
                        }
                        $rating->setRate($_POST['rate'] ?? 1);
                        $errors = $rating->validate();
                        
                        if (empty($errors)) {
                            $ratingRepository->persist($rating);

                            header("Location: index.php?controller=book&action=show&id=". $rating->getBookId());
                        }
                    }

                    // @todo_done récupérer les commentaires existants
                    $comments = $commentRepository->findAllByBookId($book->getId());                    

                    //@todo remplacer petit à petit les valeurs 
                    $this->render('book/show', [
                        'book' => $book,
                        'comments' => $comments,
                        'newComment' => $comment,
                        'rating' => $rating,
                        'averageRate' => $averageRating,
                        'errors' => $errors,
                    ]);
                } else {
                    $this->render('errors/default', [
                        'error' => 'Livre introuvable'
                    ]);
                }
            } else {
                throw new \Exception("L'id est manquant en paramètre");
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
            $bookRepository = new BookRepository();
            $errors = [];
            // Si on a pas d'id on est dans le cas d'une création
            if (is_null($id)) {
                $book = new Book();
            } else {
                // Si on a un id, il faut récupérer le livre
                $book = $bookRepository->findOneById($id);
                if (!$book) {
                    throw new \Exception("Le livre n'existe pas");
                }
            }

            // @todo_done Récupération des types
            $typeRepository = new TypeRepository();
            $types = $typeRepository->findAll();

            // @todo_done Récupération des auteurs
            $authorRepository = new AuthorRepository();
            $authors = $authorRepository->findAll();

            if (isset($_POST['saveBook'])) {
                //@todo_done envoyer les données post à la méthode hydrate de l'objet $book
                $book = Book::createAndHydrate($_REQUEST);

                //@todo_done appeler la méthode validate de l'objet book pour récupérer les erreurs (titre vide)
                $errors = $book->validate();

                // Si pas d'erreur on peut traiter l'upload de fichier
                if (empty($errors)) {
                    $fileErrors = [];
                    // On lance l'upload de fichier
                    if (isset($_FILES['file']['tmp_name']) && $_FILES['file']['tmp_name'] !== '') {
                        //@todo_done appeler la méthode static uploadImage de la classe FileTools et stocker le résultat dans $res
                        $res = FileTools::uploadImage("/uploads/books/", $_FILES['file']);
                        if (empty($res['errors'])) {
                            $book->setImage($res['fileName']);
                        } else {
                            $fileErrors = $res['errors'];
                        }
                    }
                    if (empty($fileErrors)) {
                        // @todo_done si pas d'erreur alors on appelle persit de bookRepository en passant $book
                        $newBook = $bookRepository->persist($book);

                        // @todo_done On redirige vers la page du livre (avec header location)
                        header("Location: index.php?controller=book&action=show&id=" . $newBook->getId());
                        
                    } else {
                        $errors = array_merge($errors, $fileErrors);
                    }
                }
            }

            $this->render('book/add_edit', [
                'book' => $book,
                'types' => $types,
                'authors' => $authors,
                'pageTitle' => 'Ajouter un livre',
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

        $bookRepository = new BookRepository;

        // On récupère la page courante, si page de page on met à 1
        if (isset($_GET['page'])) {
            $page = (int)$_GET['page'];
        } else {
            $page = 1;
        }

        //@todo_done récupérer les tous les livres (avec pagination plus tard)
        $books = $bookRepository->findAll(_ITEM_PER_PAGE_, $page);

        //@todo_done pour la pagination, on a besoin de connaitre le nombre total de livres
        $total_books = $bookRepository->count();

        //@todo_done pour la pagination on a besoin de connaitre le nombre de pages
        $nb_pages = ceil($total_books / _ITEM_PER_PAGE_);

        $this->render('book/list', [
            'books' => $books,
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
            $bookRepository = new BookRepository();

            $id = (int)$_GET['id'];

            $book = $bookRepository->findOneById($id);

            if (!$book) {
                throw new \Exception("Le livre n'existe pas");
            }
            if ($bookRepository->removeById($id)) {
                // On redirige vers la liste de livre
                header('location: index.php?controller=book&action=list&alert=delete_confirm');
            } else {
                throw new \Exception("Une erreur est survenue l'ors de la suppression");
            }

        } catch (\Exception $e) {
            $this->render('errors/default', [
                'error' => $e->getMessage()
            ]);
        }
    }
}
