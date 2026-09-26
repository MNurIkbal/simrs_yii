<?php


use yii\web\View;
use app\components\DocoHelpers;
use app\widgets\DHInfiniteTableWidget;
use yii\widgets\Breadcrumbs;

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

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <div class="col-md-6">
                    <?=
                    DocoHelpers::generateToolbar([
                        'riwayat-pasien' => [
                            'type'  => 'button',
                            'title' => Yii::t('fe', 'Berkas Pasien'),
                            'icon'  => 'fa fa-file',
                            'attributes' => [
                                'id'          => 'btn-berkas-pasien',
                                'data-width'  => '50%',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_berkas_pasien',
                                'action'      => '/igd/riwayat-pasien/modal-berkas-pasien?no_rekam_medik='.$norm,
                            ]
                        ],
                    ]) ?>
                </div>
            </div>
            <div class="panel-body">
                <?php if($aksesRiwayatPasien) : ?>
                <?php
                    $columns = [
                        [
                            'title' => Yii::t('fe', 'Tanggal kunjungan / No pendaftaran'),
                            'data' => 'tgl_pendaftaran',
                            'name' => 'tgl_pendaftaran',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Ruangan / Kamar'),
                            'data' => 'ruangan_pend',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Dokter pemeriksa'),
                            'data' => 'dok_rjrd',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Pelayanan Pasien'),
                            'data' => 'aksi_pelayanan',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Penunjang'),
                            'data' => 'aksi_penunjang',
                            'searchable' => false,
                            'orderable' => false
                        ],
                        [
                            'title' => Yii::t('fe', 'Cara Keluar'),
                            'data' => 'cara_keluar',
                            'searchable' => false,
                            'orderable' => false
                        ]
                    ];
                ?>
                <div style="height: 100%; max-height: 550px; overflow-y: auto; overflow-x: hidden;">
                    <?=
                        DHInfiniteTableWidget::widget([
                            'id' => 'table-riwayat',
                            'ajax' => [
                                'url' => 'igd/riwayat-pasien/get-data-history-patient',
                                'data' => [
                                        'norm' => $norm,
                                        'is_modal' => true,
                                    ]
                            ],
                            'limit' => 5,
                            'columns' => $columns,
                            'formFilters' => [],
                            'functions' => [
                                'filterRendered' => "function (wrapper) {
                                    $('.filter-label-btn').remove();
                                }"
                            ]
                        ]);
                    ?>
                </div>
                <hr>
                <h5 class="panel-title"><b>Informasi Dokumen</b></h5>
                <div class="row">
                    <div class="col-md-12">
                        <table id="table-upload-dokumen" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead class="text-center">
                                <tr class="bg-inverse">
                                    <th style="width: 99px;"><?= Yii::t('fe', 'Tanggal') ?></th>
                                    <th style="width: 59px;"><?= Yii::t('fe', 'Ruangan') ?></th>
                                    <th style="width: 78px;"><?= Yii::t('fe', 'Dokter') ?></th>
                                    <th style="width: 484px;"><?= Yii::t('fe', 'Nama Dokumen') ?></th>
                                    <th style="width: 484px;"><?= Yii::t('fe', 'Upload Dokumen') ?></th>
                                </tr>
                            </thead>
                            <tbody> 
                            </tbody>
                        </table>
                    </div>
                </div>
                <?php else : ?>
                    <br>
                    <div class="alert alert-danger" role="alert">
                    Mohon maaf, akses anda dibatasi.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div id="modal-gambar-radiologi" class="modal">
    <div class="modal-dialog modal-xl" style="height: 100%;width: 99%;margin: 2px;">
        <div class="modal-content" style=" height: 100%;">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title">Gambar Radiologi</h5>
            </div>
            <div class="modal-body" style="height: 100%;">
                <iframe  style="width: 100%; height: 95%;" src=""></iframe>
            </div>
        </div>
    </div>
</div>
<div id="modal-preview" class="modal">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-header bg-inverse" style="z-index: 1050">
            <button type="button" id="dismiss-preview-btn" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Preview</h5>
        </div>
        <div class="modal-content">
            <div class="preview-wrapper" style="position: relative;" id="preview-wrapper">
                <!-- <div class="overlay-preview"></div> -->
                <iframe frameborder="0" id="preview-content" style="width:100%;height:85vh"></iframe>
            </div>
        </div>
    </div>
</div>

<div id="modal-show-konsul" class="modal">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-header bg-inverse" style="z-index: 1050">
            <button type="button" id="dismiss-preview-btn-konsul" class="close" data-dismiss="modal">&times;</button>
            <h5 class="modal-title">Konsul Poli</h5>
        </div>
        <div class="modal-content">
            <div class="preview-wrapper" style="position: relative;" id="preview-wrapper-konsul">
                <!-- <div class="overlay-preview"></div> -->
                <iframe frameborder="0" id="preview-content-konsul" style="width:100%;height:85vh"></iframe>
            </div>
        </div>
    </div>
</div>

<div id="modal_berkas_pasien" class="modal">
    <div class="modal-dialog modal-lg" style="width: 90%;">
        <div class="modal-content">
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/index.js'), View::POS_END);
$this->registerJs("
    var table_column_upload = [
        {title: '".(\Yii::t('fe', 'Tanggal kunjungan / No pendaftaran'))."',  data: 'doc_date', name: 'doc_date'},
        {title: '".(\Yii::t('fe', 'Ruangan / Kamar'))."',  data: 'ruangan_nama'},
        {title: '".(\Yii::t('fe', 'Dokter pemeriksa'))."',  data: 'nama_pegawai'},
        {title: '".(\Yii::t('fe', 'Nama Dokumen'))."',  data: 'nama_dokumen'},
        {title: '".(\Yii::t('fe', 'Dokumen'))."',  data: 'aksi_non_pendaftaran'},
    ];

    var table_riwayat_upload = $('#table-upload-dokumen').docoTabel({
        filter: false,
        cacheFilter: false,
        sorting: [[0, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        ajax: {
            url: `/api/upload-dokumen/get-dokumen-list?norm=$norm`,
        }, 
        columns: table_column_upload,
    });
");

?>
