<?php

require_once BASE_PATH . '/template/admin/layouts/header.php';

?>
<section class="pt-3 pb-1 mb-2 border-bottom">
    <h1 class="h5">Show Comment</h1>
</section>
<section class="row my-3">
    <section class="col-12">
        <h1 class="h4 border-bottom"><?= $comment['id'] ?></h1>
        <p class="text-secondary border-bottom"> <?=
                                                    $comment['email'] ?></p>
        <p class="text-secondary border-bottom"> <?=
                                                    $comment['post_title'] ?></p>
        <p class="text-secondary border-bottom"> <?= $comment['comment'] ?></p>
        <p class="text-secondary border-bottom"> <?= $comment['status'] ?></p>

        <?php if ($comment['status'] == 'seen') { ?>
            <a role="button" class="btn btn-sm btn-success text-white" href="<?= url('admin/comment/change-status/' . $comment['id']) ?>">click to approved</a>
        <?php } else { ?>
            <a role="button" class="btn btn-sm btn-warning text-white" href="<?= url('admin/comment/change-status/' . $comment['id']) ?>">click not to approved</a>
        <?php } ?>

    </section>
</section>
<?php

require_once BASE_PATH . '/template/admin/layouts/footer.php';

?>