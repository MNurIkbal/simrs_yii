<?php

use yii\web\View;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-bordered" width="100%" id="tbl-view-dokumen" data-href="<?=$url?>">
                <thead>
                    <tr class="bg-inverse">
                        <th width="5">No.</th>
                        <th>Jenis Dokumen</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="3" class="text-center">Data Belum Tersedia</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php

$this->registerJs($this->render('_view_dokumen.js'), View::POS_END, 'jskuning');

?>

