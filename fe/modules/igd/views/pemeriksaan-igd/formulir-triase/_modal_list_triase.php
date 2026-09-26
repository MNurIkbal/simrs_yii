<?php

use yii\web\View;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered" width="100%" id="list-triase-tb" data-href="igd/pemeriksaan-igd/get-list-triase">
                <thead>
                    <tr class="bg-inverse">
                        <th>No.</th>
                        <th>Waktu Input</th>
                        <th>Pegawai</th>
                        <th>No. Bed</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="5" class="text-center">Data Belum Tersedia</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('_modal_list_triase.js'), View::POS_END);
?>
