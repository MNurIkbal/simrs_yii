<?php

/**
 * @author Randy Vianda Putra
 * @todo Batal Pasien Radiologi
 * @copyright 26 Juli 2018 aweutist
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use kartik\widgets\Select2;
use yii\web\JsExpression;

use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;


// Some variables
$this->title = Yii::t('fe', 'Pembatalan Pemeriksaan Radiologi');
$this->params['breadcrumbs'][] = ['label' => 'Radiologi', 'url' => ['/radiologi']];
$this->params['breadcrumbs'][] = $this->title;


?>

<style>
    .datepicker>div{
        display:block;
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
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                                <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                    // 'search',
                    'back' => [
                        'title' => \Yii::t('fe', 'Kembali'),
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-kembali',
                            // 'data-options' => 'link',
                            'id' => 'btn-kembali',
                            'onClick' => null,
                            // 'data-content' => 'content-perda',
                            // 'data-target' => '/laboratorium/inf-pasien-rujukan-lab/form-batal?id=',
                        ]
                    ],
                    'save' => [
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'class' => 'btn btn-info btn-labeled btn-xs btn-simpan',
                            // 'data-options' => 'link',
                            'id' => 'btn-simpan',
                            'onClick' => null,
                            // 'data-content' => 'content-perda',
                            // 'data-target' => '/laboratorium/inf-pasien-rujukan-lab/form-batal?id=',
                        ]
                    ],
                ], '#tb-inf-pasien-rujukan-rad') ?>
            </div>
            <div class="panel-body">
               <!-- pannel detail pasien -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Informasi Pasien'); ?></b></h6>
                        </div>

                        <div class="panel-body">
     
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Pendaftaran") ?></b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= isset($detail['no_pendaftaran']) ? $detail['no_pendaftaran'] : '-' ?> </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Nama Pasien") ?></b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= isset($detail['nama_pasien']) ? $detail['nama_pasien'] : '-' ?> </p>
                                    </div>
                                </div>
                                
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "No Rujukan") ?></b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= isset($detail['no_rujukan']) ? $detail['no_rujukan'] : '-' ?> </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Asal Rujukan") ?></b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= isset($detail['asalrujukan_nama']) ? $detail['asalrujukan_nama'] : '-' ?> </p>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Tanggal Rujukan") ?></b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= isset($detail['tgl_rujukan']) ? DocoHelpers::convDateTime($detail['tgl_rujukan'],true,false) : '-' ?> </p>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter") ?></b></label>
                                    <div class="col-sm-7">
                                        <p><b>:</b>&nbsp;<?= isset($detail['dokter_penunjang']) ? $detail['dokter_penunjang'] : '-' ?> </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
               <!-- pannel detail pasien -->

               <!-- pannel data pemeriksaan -->
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Rencana Pemeriksaan Radiologi'); ?></b></h6>
                        </div>

                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-12 filter-form"></div>
                            </div>
                            <!-- table -->
                            <table id="tb-rencana-pemeriksaan-rad" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th><?= Yii::t('fe', 'No') ?></th>
                                        <th><?= Yii::t("fe", "Jenis Pemeriksaan") ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    
                                </tbody>
                            </table>
                            <!-- table -->
                        </div>
                    </div>
                </div>
               <!-- pannel data pemeriksaan -->

               <!-- panel form pembatalan -->
                
                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Form Pembatalan'); ?></b></h6>
                        </div>

                        <div class="panel-body">
                            
                            <?php $form = ActiveForm::begin(
                                [
                                    'action' => 'batal-order',
                                    'method' => 'post',
                                    'id' => 'form',
                                    'type' => ActiveForm::TYPE_HORIZONTAL,
                                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL],
                                    'fieldConfig' => ['enableLabel' => false],
                                    'enableAjaxValidation' => false,
                                    'enableClientValidation' => false,
                                ]
                            ) ?>

                                <?= Html::hiddenInput('pasienmasukpenunjang_id', DocoHelpers::encrypt($id), ['id' => 'pasien-kirim-unit-lain-id', 'readonly' => 'readonly']) ?>
                                <div class="form-group">
                                    
                                    <div class="col-md-6">
                                        <label class="text-left control-label col-sm-4"><b><?= Yii::t("fe", "Tanggal Batal") ?></b> <span class="text-danger">*</span> </label>
                                        <div class="col-sm-7">
                                            <?php $model->tgl_batalperiksa = date('d-M-Y'); ?>
                                            <?= $form->field($model, 'tgl_batalperiksa')->widget(DatePicker::classname(), [
                                                'name' => 'date_12',
                                                'value' => date('Y-m-d'),
                                                'readonly' => true,
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-M-yyyy',
                                                    'endDate' => "0d",
                                                    'startDate' => "0d",
                                                ]
                                            ]); ?>
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Disetujui Oleh") ?></b> <span class="text-danger">*</span> </label>
                                        <div class="col-sm-7">
                                            <?=
                                            $form->field($model, 'peg_menyetujui_id')->widget(Select2::classname(), [
                                                'name' => 'disetujui_oleh',
                                                'options' => ['placeholder' => \Yii::t('fe', 'Disetujui Oleh'), 'autocomplete' => 'off'],
                                                'pluginOptions' => [
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 3,
                                                    'language' => [
                                                        'errorLoading' => new JsExpression("function () { return 'Waiting for results...'; }"),
                                                    ],
                                                    'ajax' => [
                                                        'url' => Url::home() . (Yii::$app->controller->module->id) . '/informasi-pasien-rad/get-pegawai-ruangan',
                                                        'dataType' => 'json',
                                                        'data' => new JsExpression('function(params) { return {q:params.term}; }')
                                                    ],
                                                ],
                                            ]);
                                            ?>
                                        </div>
                                    </div>
                                
                                </div>
                                <div class="form-group">

                                    <div class="col-md-12">
                                        <label class="text-left control-label col-sm-2"><b><?= Yii::t("fe", "Alasan Pembatalan") ?></b> <span class="text-danger">*</span></label>
                                        <div class="col-sm-7">
                                            <?= $form->field($model, 'alasan')->textarea(['rows' => '4']) ?>
                                        </div>
                                    </div>
                                
                                </div>
                                <div class="form-group">

                                    <div class="col-md-12">
                                        <!-- <?= Html::submitButton(Yii::t('fe', 'Simpan'), ['class' => 'btn bg-teal btn-sm']) ?> -->
                                                    
                                    </div>
                                
                                </div>
                            <?php ActiveForm::end(); ?>
                        </div>
                    </div>
                </div>

               <!-- panel form pembatalan -->
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    // save into localStorage
    localStorage.clear();
    // Global vars
    const no = "' . (\Yii::t("fe", "Nomor")) . '";
    const jenis_pemeriksaan = "' . (\Yii::t("fe", "Jenis Pemeriksaan")) . '";
    const nama_pemeriksaan = "' . (\Yii::t("fe", "Nama Pemeriksaan")) . '";
    const qty = "' . (\Yii::t("fe", "Qty")) . '";
    const dokter_perujuk = "' . (\Yii::t("fe", "Dokter Perujuk")) . '";
    const cyto = "' . (\Yii::t("fe", "Cyto")) . '";
    const id = "' . DocoHelpers::encrypt($id) . '";

    const updateUrl = "/radiologi/inf-pasien-rad/update?id=";
    const redirectUrl = "/radiologi/informasi-pasien-rad/index";

    // Datatable language
    const emptyTable = "' . (\Yii::t("fe", "Tidak ada data yang tersedia")) . '";
    const info = "' . (\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")) . '";
    const infoEmpty = "' . (\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")) . '";
    const infoFiltered = "' . (\Yii::t("fe", "(disaring dari _MAX_ total data)")) . '";
    const lengthMenu = "' . (\Yii::t("fe", "Menampilkan _MENU_ data")) . '";
    const loadingRecords = "' . (\Yii::t("fe", "Memuat...")) . '";
    const processing = "' . (\Yii::t("fe", "Memproses...")) . '";
    const search = "' . (\Yii::t("fe", "Cari:")) . '";
    const zeroRecords = "' . (\Yii::t("fe", "Tidak ada data yang ditemukan")) . '";
    const first = "' . (\Yii::t("fe", "Pertama")) . '";
    const last = "' . (\Yii::t("fe", "Terakhir")) . '";
    const next = "' . (\Yii::t("fe", "Selanjutnya")) . '";
    const previous = "' . (\Yii::t("fe", "Sebelumnya")) . '";
    const sortAscending = "' . (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")) . '";
    const sortDescending = "' . (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")) . '";

    // Custom dropdown
    

   
', View::POS_END, 'b-index');

// Register js file
$this->registerJs($this->render('js/form_batal.js'), View::POS_END);
?>
