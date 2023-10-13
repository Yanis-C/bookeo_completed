<?php require_once _TEMPLATEPATH_ . '\header.php'; ?>

<?php foreach ($errors as $error) { ?>
    <div class="alert alert-danger" role="alert">
        <?= $error; ?>
    </div>
<?php } ?>

<h1><?= $pageTitle; ?></h1>

<form method="POST" enctype="multipart/form-data">
    <div class="mb-3">
        <label for="first_name" class="form-label">Prénom</label>
        <input type="text" class="form-control " id="first_name" name="first_name" value="<?= $author->getFirstName() ?>">
    </div>

    <div class="mb-3">
        <label for="last_name" class="form-label">Nom de famille</label>
        <input type="text" class="form-control " id="last_name" name="last_name" value="<?= $author->getLastName() ?>">
    </div>

    <div class="mb-3">
        <label for="nickname" class="form-label">Surnom</label>
        <input type="text" class="form-control " id="nickname" name="nickname" value="<?= $author->getNickname() ?>">
    </div>


    <input type="submit" name="saveAuthor" class="btn btn-primary" value="Enregistrer">

</form>


<?php require_once _TEMPLATEPATH_ . '\footer.php'; ?>