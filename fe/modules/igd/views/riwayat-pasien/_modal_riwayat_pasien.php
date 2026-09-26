<?php


use yii\web\View;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;

$this->title = \Yii::t('fe', 'Riwayat Pasien');
$this->params['breadcrumbs'][] = ['label' => $instalasi, 'url' => [$url]];
$this->params['breadcrumbs'][] = $title;
?>

<style lang="">
    .info-pasien {
        width: 70px;
        height: 30px;
        border-radius: 5px;
        border: 1px solid black;
        float: left;
        margin: 3px;
    }

    .btn-riwayat {
        margin-bottom: 5px;
        margin-right: 5px;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php if($aksesRiwayatPasien) : ?>
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-white">
                <div class="panel-body">
                    <table id="table-riwayat" class="table table-striped table-condensed table-hover" style="width:100%">
                        <thead class="text-center">
                            <tr class="bg-inverse">
                                <th><?= Yii::t('fe', 'Tanggal kunjungan / No pendaftaran') ?></th>
                                <th><?= Yii::t('fe', 'Ruangan / Kamar') ?></th>
                                <th><?= Yii::t('fe', 'Dokter pemeriksa') ?></th>
                                <th><?= Yii::t('fe', 'Pelayanan pasien') ?></th>
                                <th><?= Yii::t('fe', 'Penunjang') ?></th>
                                <th><?= Yii::t('fe', 'Cara keluar') ?></th>
                            </tr>
                        </thead>
                        <tbody> 
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php else : ?>
        <br>
        <div class="alert alert-danger" role="alert">
        Mohon maaf, akses anda dibatasi.
        </div>
    <?php endif; ?>
</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>

<?php
$this->registerJs($this->render('js/index.js'), View::POS_END);
$this->registerJs("
    var table = $('#table-riwayat').docoTabel({
        filter: false,
        sorting: [[0, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: '/igd/riwayat-pasien/get-data-riwayat-pasien?norm=$norm&is_modal=is_modal',
        columns: [
            {title: '".(\Yii::t('fe', 'Tanggal kunjungan / No pendaftaran'))."',  data: 'tgl_pendaftaran', name: 'tgl_pendaftaran'},
            {title: '".(\Yii::t('fe', 'Ruangan / Kamar'))."',  data: 'ruangan_pend'},
            {title: '".(\Yii::t('fe', 'Dokter pemeriksa'))."',  data: 'dok_rjrd'},
            {title: '".(\Yii::t('fe', 'Pelayanan pasien'))."',  data: 'aksi_pelayanan'},
            {title: '".(\Yii::t('fe', 'Penunjang'))."',  data: 'aksi_penunjang'},
            {title: '".(\Yii::t('fe', 'Cara keluar'))."',  data: 'cara_keluar'},
        ],
    });
    table
        .clear()
        .draw();
");

?>