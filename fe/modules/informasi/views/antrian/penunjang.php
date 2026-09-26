<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
// use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>

            <!-- <div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?//=Html::button('<i class="fa fa-refresh"></i> Muat Ulang', [
                        //'class' => 'btn btn-info btn-sm data-reload',
                    //]);?>
                    <?//=Html::a('<i class="fa fa-file-excel-o"></i> Ekspor', '#', [
                        //'class' => 'btn btn-success btn-sm data-export-all',
                        //'action' =>  Url::home().'master/kelompokpegawai/export-all'
                    //]);?>
                    <?//=Html::a('<i class="fa fa-print"></i> Cetak', '#', [
                        //'class' => 'btn btn-warning btn-sm data-export-all',
                        //'action' =>  Url::home().'master/kelompokpegawai/print-all'
                    //]);?>
                </div>
            </div> -->
            
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                        <?= Yii::$app->controller->renderPartial('_search_penunjang', array(
                            'dokter' => $dokter,
                            'ruangan' => $ruangan,
                            'status_periksa' => $status_periksa,
                        )) ?>
                    </div>
                </div>
                <table id="example" class="table datatable-basic table-striped table-hover dataTable no-footer" 
                data-source="<?=Url::home();?>informasi/antrian/get-data-penunjang"
                data-filter=".form-filter"
                data-test="true">
                    <thead>
                        <tr class="bg-inverse">
                            <!-- <th width="1">No</th> -->
                            <th>No Urut</th>
                            <th>No. Pendaftaran</th>
                            <th>Ruangan</th>
                            <th>Dokter</th>
                            <th>No. Rekam Medik</th>
                            <th>Nama Pasien</th>
                            <th>Status Periksa</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
                        </tr> -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs("
    var tabel = $('#example').docoTabel({
        columns : [
            // {data: 'rowNum', name : 'rowNum'},
            {data: \"no_urutantri\"},
            {data: \"no_pendaftaran\"},
            {data: \"ruangan_nama\"},
            {data: \"nama_pegawai\"},
            {data: \"no_rekam_medik\"},
            {data: \"nama_pasien\"},
            {data: \"status_periksa\"},
        ],
        colNoOrder : [0,5]
    });

    var _test = function (bool) {
        if (bool) {
            tabel.reload(false);
        } else {
            tabel.reload();
        }
    }

    var _afterSave = function (bool) {
        tabel.reload();
    }

    $('.reset-filter').on('click', function (e) {
        e.preventDefault();
        tabel.reset();
    });

    // $('.select2', $('form.form-filter')).change(function (event) {
    //     event.preventDefault();
    //     tabel.reload();
    // });

    $('form.form-filter').on('submit', function (e) {
        e.preventDefault();
        tabel.reload();
    });
",View::POS_END, 'AntrianPenunjang');

?>