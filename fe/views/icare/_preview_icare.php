<?php

use app\components\DocoHelpers;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <?php if ($statusCode == 200): ?>
                <iframe src='<?= $path ?>' width='100%' style="height: 700px; overflow-y: auto;"></iframe>
            <?php else : ?>
                <div class="alert alert-warning" role="alert">
                    <i class="fa fa-exclamation-triangle" aria-hidden="true"></i>&nbsp; 
                    Halaman tidak dapat dibuka. <strong><?= ucfirst($errorMessage) ?>.</strong>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
