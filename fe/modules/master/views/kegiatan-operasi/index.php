<?php

/**
 * @author Mokh Nurhuda
 * @todo Kegiatan Operasi
 * @copyright 04 Juli 2018 aweutist
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use yii\helpers\Url;

use kartik\widgets\Select2;
use yii\web\JsExpression;


// Some variables
$this->title = Yii::t('fe', 'Kegiatan Operasi');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

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
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset'=> [
                        'attributes'=>[
                            'data-parent' => '.filter-form'
                        ]
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/kegiatan-operasi/create',
                        ]
                    ],
                    'delete' => [
                        'attributes' => [
                            'id' => 'btn-delete',
                            'disabled' => 'disabled',
                            'data-additional'=>'data-rm'
                        ]
                    ],
                    // 'pdf',
                ], '#tb-kegiatan-operasi') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>
                <table id="tb-kegiatan-operasi" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1"><?=\Yii::t("fe", "No");?></th>
                            <th><?= Yii::t('fe', 'Nama Kegiatan Operasi') ?></th>
                            <th><?= Yii::t("fe", "Kode Kegiatan Operasi") ?></th>
                        </tr>
                    </thead>
                    <tbody>

                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // Global vars

    const kode = "'.(\Yii::t("fe", "Kode Kegiatan Operasi")). '";
    const nama = "'.(\Yii::t("fe", "Nama Kegiatan Operasi")). '";
	// var kelompokPemeriksaan = "' . (\Yii::t("fe", "Kelompok Pemeriksaan")) . '";


    const updateUrl = "/master/kegiatan-operasi/update?id=";

    // Datatable language
    const emptyTable = "'.(\Yii::t("fe", "Tidak ada data yang tersedia")).'";
    const info = "'.(\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")).'";
    const infoEmpty = "'.(\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")).'";
    const infoFiltered = "'.(\Yii::t("fe", "(disaring dari _MAX_ total data)")).'";
    const lengthMenu = "'.(\Yii::t("fe", "Menampilkan _MENU_ data")).'";
    const loadingRecords = "'.(\Yii::t("fe", "Memuat...")).'";
    const processing = "'.(\Yii::t("fe", "Memproses...")).'";
    const search = "'.(\Yii::t("fe", "Cari:")).'";
    const zeroRecords = "'.(\Yii::t("fe", "Tidak ada data yang ditemukan")).'";
    const first = "'.(\Yii::t("fe", "Pertama")).'";
    const last = "'.(\Yii::t("fe", "Terakhir")).'";
    const next = "'.(\Yii::t("fe", "Selanjutnya")).'";
    const previous = "'.(\Yii::t("fe", "Sebelumnya")).'";
    const sortAscending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")).'";
    const sortDescending = "'.(\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")). '";

    // custom dropdown
        var dropdownKegiatanOperasi = \'' . (preg_replace("/[\n\t\r]/i", '', Html::dropDownList('kegiatanoperasi_id', '', $kegiatanOperasi, ['class' => 'form-control select2 jenis', 'prompt' => \Yii::t('fe', 'Pilih')]))) . '\';
        
        var autocompleteKegiatanOperasi = \'<div class="input-group">' . (preg_replace(
    "/[\n\t\r]/i",
    '',
    Select2::widget([
        'name' => 'kegiatanoperasi_nama',
        'options' => ['placeholder' => \Yii::t('fe', 'Nama Kegiatan Operasi'), 'autocomplete' => 'off'],
        'pluginOptions' => [
            'allowClear' => true,
            'minimumInputLength' => 3,
            'language' => [
                'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
            ],
            'ajax' => [
                'url' => Url::home() . (Yii::$app->controller->module->id) . '/kegiatan-operasi/get-kegiatan-operasi',
                'dataType' => 'json',
                'data' => new JsExpression('function(params) { return {q:params.term}; }')
            ],
        ],
    ])
)) . '\'
', View::POS_END, 'b-index');

// Register js file
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
