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

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                        <?= Yii::$app->controller->renderPartial('_search_rd', array(
                            'dokter' => $dokter,
                            'ruangan' => $ruangan,
                            'status_periksa' => $status_periksa
                        )) ?>
                    </div>
                </div>
                <table id="example" class="table datatable-basic table-striped table-hover dataTable no-footer" 
                data-source="<?=Url::home();?>informasi/kunjungan/get-data?instalasi=rd"
                data-filter=".form-filter"
                data-test="true">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="1"><?= Yii::t('fe', 'Rownum') ?></th>
                            <th><?= Yii::t('fe', 'Tanggal pendaftaran') ?></th>
                            <th><?= Yii::t('fe', 'No pendaftaran') ?></th>
                            <th><?= Yii::t('fe', 'Ruangan igd') ?></th>
                            <th><?= Yii::t('fe', 'No rekam medik') ?></th>
                            <th><?= Yii::t('fe', 'Nama pasien') ?></th>
                            <th><?= Yii::t('fe', 'Alamat pasien') ?></th>
                            <th><?= Yii::t('fe', 'Cara bayar') ?></th>
                            <th><?= Yii::t('fe', 'Dokter') ?></th>
                            <th><?= Yii::t('fe', 'Status rawat') ?></th>
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
        dom: 'Bfltip',
            buttons: [
                {
                    text: '<i class=\"fa fa-search\"></i>',
                    className: 'btn btn-primary btn-xs ',
                    action: function ( e, dt, node, config ) {
                        $('.form-filter').submit();
                    }
                },
                {
                    text: '<i class=\"fa fa-refresh\"></i>',
                    className: 'btn btn-lime-green btn-xs ',
                    action: function ( e, dt, node, config ) {
                        tabel.reset();
                    }
                },
                {
                    text: '<i class=\" fa fa-print\"></i>',
                    className: 'btn btn-dodger-blue btn-xs ',
                    action: function ( e, dt, node, config ) {
                        tabel.reset();
                    }
                },
                {
                    text: '<i class=\"fa fa-file-excel-o\"></i>',
                    className: 'btn btn-green btn-xs ',
                    action: function ( e, dt, node, config ) {
                        tabel.reset();
                    }
                },
                {
                    text: '<i class=\"fa fa-file-pdf-o\"></i>',
                    className: 'btn btn-crimson btn-xs ',
                    action: function ( e, dt, node, config ) {
                        tabel.reset();
                    }
                },
            ],
        columns : [
            {data: 'rowNum', name : 'rowNum'},
            {data: \"tgl_pendaftaran\", name: 'tgl_pendaftaran'},
            {data: \"no_pendaftaran\", name: 'no_pendaftaran'},
            {data: \"ruangan_nama\", name: 'ruangan_nama'},
            {data: \"no_rekam_medik\", name: 'no_rekam_medik'},
            {data: \"nama_pasien\", name: 'nama_pasien'},
            {data: \"alamat_pasien\", name: 'alamat_pasien'},
            {data: \"carabayar_nama\", name: 'carabayar_nama'},
            {data: \"nama_pegawai\", name: 'nama_pegawai'},
            {data: \"jeniskasuspenyakit_nama\", name: 'jeniskasuspenyakit_nama'},
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
",View::POS_END, 'Kunjungan');

?>