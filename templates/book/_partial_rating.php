<div class="card">
    <div class="card-body p-4">

        <div class="row mb-3">
            <h2>Note des utilisateurs</h2>
            <div class="row align-items-center justify-content-center">
                <div class="rate col-6">
                    <?php for ($i=5; $i>0; $i--) { ?>
                        <input disabled="disabled" type="radio" id="avgstar<?= $i ?>" name="avgrate" value="<?= $i ?>" <?= $i == round($averageRate) ? "checked" : "" ?>>
                        <label for="avgstar<?= $i ?>" title="<?= $i ?> étoiles"><?= $i ?> étoiles</label>
                    <?php } ?>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <h3>Noter ce livre</h3>

            <form method="POST">
                <div class="mb-3">
                    <div class="row">
                        <div class="col-4 py-2">
                            <label for="rate" class="form-label">Votre note :</label>

                        </div>
                        <div class="col-8">
                            <div class="rate enabled">
                                <?php for ($i=5; $i>0; $i--) { ?>
                                    <input type="radio" id="star<?= $i ?>" name="rate" value="<?= $i ?>"  <?= $i == $rating->getRate() ? "checked" : "" ?>>
                                    <label for="star<?= $i ?>" title="<?= $i ?> étoiles"><?= $i ?> étoiles</label>
                                <?php } ?>
                                
                                <!--<input type="radio" id="star4" name="rate" value="4">
                                <label for="star4" title="4 étoiles">4 étoiles</label>
                                <input type="radio" id="star3" name="rate" value="3" checked="checked">
                                <label for="star3" title="3 étoiles">3 étoiles</label>
                                <input type="radio" id="star2" name="rate" value="2">
                                <label for="star2" title="2 étoiles">2 étoiles</label>
                                <input type="radio" id="star1" name="rate" value="1">
                                <label for="star1" title="1 étoiles">1 étoiles</label>-->
                            </div>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="book_id" value="<?= $rating->getBookId() ?>">
                <input type="hidden" name="user_id" value="<?= $rating->getUserId() ?>">

                <input type="hidden" name="id" value="<?= $rating->getId() ?>">



                <div class="mb-3">
                    <input type="submit" name="saveRating" class="btn btn-primary form-control " value="Noter">
                </div>

            </form>
        </div>



    </div>
</div>