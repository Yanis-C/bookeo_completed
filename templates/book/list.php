<?php require_once _ROOTPATH_ . '\templates\header.php'; ?>

<h1>Liste complète</h1>

<div class="row text-center mb-3">
    <?php foreach ($books as $book) { ?>
        <div class="col-md-4 my-2 d-flex">
            <div class="card">
                <img src="<?= !empty($book->getImagePath()) ? $book->getImagePath() : "/assets/images/default-book.jpg" ?>" class="card-img-top" alt="<?= $book->getTitle() ?>">
                <div class="card-body">
                    <h5 class="card-title"><?= $book->getTitle() ?></h5>
                    <p class="card-text"><?= strlen($book->getDescription()) > 100 ? substr($book->getDescription(), 0, 100) . "..." : $book->getDescription() ?></p>
                    <a href="index.php?controller=book&amp;action=show&amp;id=<?= $book->getId() ?>" class="btn btn-primary">Lire la suite</a>
                </div>
            </div>
        </div>
    <?php } ?>
</div>


<div class="row">
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <li class="page-item">
                    <a class="page-link <?= $page == $i ? "active" : "" ?>" href="index.php?controller=book&action=list&page=<?= $i ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
        </ul>
    </nav>
</div>




<?php require_once _ROOTPATH_ . '\templates\footer.php'; ?>