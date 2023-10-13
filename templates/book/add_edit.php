<?php require_once _TEMPLATEPATH_ . '\header.php'; ?>

<?php foreach ($errors as $error) { ?>
    <div class="alert alert-danger" role="alert">
        <?= $error; ?>
    </div>
<?php } ?>

<h1><?= $pageTitle; ?></h1>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="title" class="form-label">Titre</label>
        <input type="text" class="form-control " id="title" name="title" value="<?= $book->getTitle() ?>">

    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea class="form-control" id="description" name="description" rows="3"><?= $book->getDescription() ?></textarea>
    </div>

    <!-- Attention, cette liste doit être récupéreée avec une requête-->
    <div class="mb-3">
        <label for="type" class="form-label">Type</label>
        <select name="type_id" id="type" class="form-select">
            <?php foreach ($types as $type) { ?>
                <option value="<?= $type->getId() ?>"><?= $type->getName() ?></option>
            <?php } ?>
        </select>
    </div>

    <!-- Attention, cette liste doit être récupérée avec une requête-->
    <div class="mb-3">
        <label for="author" class="form-label">Auteur</label>
        <select name="author_id" id="author" class="form-select">
            <?php foreach ($authors as $author) { ?>
                <option value="<?= $author->getId() ?>"><?= $author->getDisplayName() ?></option>
            <?php } ?>
        </select>
    </div>

    <input type="hidden" name="image" value="<?= $book->getImage() ?>">
    <div class="mb-3">
        <label for="file" class="form-label">Image</label>
        <input type="file" name="file" id="file" class="form-control ">
    </div>

    <input type="submit" name="saveBook" class="btn btn-primary" value="Enregistrer">

</form>


<?php require_once _TEMPLATEPATH_ . '\footer.php'; ?>