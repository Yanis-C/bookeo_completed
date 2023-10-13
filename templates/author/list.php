<?php require_once _ROOTPATH_ . '\templates\header.php'; ?>

<h1>Auteurs</h1>

<div class="d-flex gap-2 justify-content-left py-5">
    <a class="btn btn-primary d-inline-flex align-items-left" href="index.php?controller=author&action=add">
        Ajouter un auteur
    </a>
</div>
<table class="table">
    <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Titre</th>
            <th scope="col">Actions</th>
        </tr>
  </thead>
  <tbody>
    <?php foreach ($authors as $author): ?>
      <tr>
        <th scope="row"><?= $author->getId() ?></th>
        <td><?= $author->getDisplayName() ?></td>
        <td><a href="index.php?controller=author&action=edit&id=<?= $author->getId() ?>">Modifier</a>
          | <a href="index.php?controller=author&action=delete&id=<?= $author->getId() ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet article ?')">Supprimer</a></td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>


<div class="row">
    <nav aria-label="Page navigation example">
        <ul class="pagination justify-content-center">
            <?php for ($i = 1; $i <= $totalPages; $i++) { ?>
                <li class="page-item">
                    <a class="page-link <?= $page == $i ? "active" : "" ?>" href="index.php?controller=author&action=list&page=<?= $i ?>"><?php echo $i; ?></a>
                </li>
            <?php } ?>
        </ul>
    </nav>
</div>




<?php require_once _ROOTPATH_ . '\templates\footer.php'; ?>