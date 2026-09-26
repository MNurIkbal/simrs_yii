<?php

use app\components\DocoHelpers;
use yii\web\View;

?>

<div class="row">
    <div class="col-md-12" style="width: 100%;">
        <table id="tabel-surat-keterangan" class="table datatable-basic table-striped table-hover dataTable no-footer" width="100%" style="overflow-x: scroll;">
            <thead>
                <tr class="bg-inverse">
                    <th><?=Yii::t('fe', 'No')?></th>
                    <th><?=Yii::t('fe', 'Judul Surat')?></th>
                    <th><?=Yii::t('fe', 'Aksi')?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>

<?php 
    $this->registerJs("
        var pendaftaran_id = '" . $pendaftaran_id . "';
        var dec_pendaftaran_id = '". DocoHelpers::decrypt($pendaftaran_id) ."';
        var url = '" . $url . "';
        var modul = '" . $modul . "/';
    ", View::POS_END);
    $this->registerJs($this->render('index.js'), View::POS_END);
?>