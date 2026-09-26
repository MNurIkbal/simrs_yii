<?php

/**
 * @Author: Sigit
 * @Date:   2018-04-16 11:29:40
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-04-18 09:49:34
 */

// Using
use app\components\DocoHelpers;
use kartik\select2\Select2;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;

// Title
$this->title = Yii::t('fe', 'Pemesanan Dokumen Rekam Medik');
$this->params['breadcrumbs'][] = ['label' => 'Rm', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <!-- Panel heading -->
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <!-- Panel toolbar -->
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'save' => [
                        'attributes' => [
                            'data-target' => 'pemesanan-dok-rekam-medik-form',
                            'disabled' => 'disabled',
                            'id' => 'btn-save'
                        ]
                    ],
                    'custom-print' => [
                        'type'=>'button',
                        'title' => Yii::t('fe', 'Cetak'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'method' => 'json',
                            'data-options' => 'click',
                            'disabled' => 'disabled'
                        ],
                    ],
                    'reset' => [
                        'attributes' => [
                            'data-options' => 'click',
                            'id' => 'btn-ulang'
                        ]
                    ],
                ], '#table-pemesanan-dok-rekam-medik');?>
            </div>
            <?php $form = ActiveForm::begin(['id' => 'pemesanan-dok-rekam-medik-form', 'action' => '/rm/tra-pemesanan-dok-rekam-medik/create']) ?>
            <!-- Panel body -->
            <div class="panel-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?= $form->field($model, 'no_pesandokrm')->textInput([
                                'class' => 'form-control input-sm',
                                'disabled' => 'disabled',
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?= $form->field($model, 'tgl_pesandokrm')->textInput([
                                'class' => 'form-control input-sm',
                                'readonly' => 'readonly',
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?= $form->field($model, 'ruanganpemesan_id')->dropDownList(
                                ArrayHelper::map($resMaster['instalasi'], 'instalasi_id', 'instalasi_nama'),
                                [
                                    'id' => 'filter_instalasi',
                                    'class' => 'form-control select2 dep-to-child',
                                    'prompt' => \Yii::t('fe', '--Pilih Instalasi akhir--'),
                                    'data-url' =>  '/rm/tra-pemesanan-dok-rekam-medik/get-ruangan',
                                    'data-depend_id' => 'filter_ruangan',
                                    'data-depend_prompt' => \Yii::t('fe', '--Pilih Ruangan--'),
                                    'data-storage' => 'ruangan',
                                    'data-key' => 'ruangan_id',
                                    'data-val' => 'ruangan_nama',
                                ]
                            ) ?>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?= $form->field($model, 'tgl_mintakirim')->textInput([
                                'class' => 'form-control input-sm date',
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <?= $form->field($model, 'ruangantujuan_id')->dropDownList(
                                ArrayHelper::map($resMaster['ruangan'], 'ruangan_id', 'ruangan_nama'),
                                [
                                    'id' => 'filter_ruangan',
                                    'class' => 'form-control select2 dep-to-parent',
                                    'data-url' =>  '/rm/tra-pemesanan-dok-rekam-medik/get-instalasi',
                                    'data-depend_id' => 'filter_instalasi',
                                    'prompt' => \Yii::t('fe', '--Pilih Ruangan--')
                                ]
                            ) ?>
                        </div>
                    </div>
                    <div class="col-sm-4">
                        <div class="form-group">
                            <?= Html::label(Yii::t('fe', 'Nomor Rekam Medis'), '', ['class' => 'control-label']) ?>
                            <?= Select2::widget([
                                'model' => '',
                                'name' => '',
                                'attribute' => '',
                                'options' => [
                                    'id' => 'dd-no-rekam-medik',
                                ],
                                'pluginOptions' => [
                                    'minimumInputLength' => 3,
                                    'language' => [
                                        'errorLoading' => new JsExpression("function () {return 'Loading...';}"),
                                    ],
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['tra-pemesanan-dok-rekam-medik/get-posisi-dok-rekam-medik']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(params) {
                                            const ruangan_id = $("#data-ruangan_id").val();
                                            return {params: params.term, ruangan_id: ruangan_id, type: "dd"}
                                        }')
                                    ],
                                    'escapeMarkup' => new JsExpression('function (markup) {return markup}'),
                                    'templateResult' => new JsExpression(
                                        'function(result) { 
                                            return result.text; 
                                        }'
                                    ),
                                    'templateSelection' => new JsExpression(
                                        'function (result) {
                                            return result.text; 
                                        }'
                                    ),
                                ],
                                'pluginEvents' => [
                                    'change' => 'function(data) {
                                        // Get id
                                        var id = $(this).val();
                                        console.log(id)
                                        // Ajax to get the data
                                        $.ajax({
                                            url: "'.\yii\helpers\Url::to(['tra-pemesanan-dok-rekam-medik/get-posisi-dok-rekam-medik']).'",
                                            type: "GET",
                                            data: {params: id, type: "assign"},
                                            dataType: "json",
                                            success: function(data) {
                                                // Assign to hidden input
                                                $("#data-posisi-dok-rekam-medik-id").val(data.posisidokrm_id);
                                                $("#data-dok-rekam-medis-id").val(data.dokrekammedis_id);
                                                $("#data-tanggal-rekam-medis").val(data.tglrekammedis);
                                                $("#data-lokasi-rak-id").val(data.lokasirak_id);
                                                $("#data-lokasi-rak-nama").val(data.lokasirak_nama);
                                                $("#data-subrak-id").val(data.subrak_id);
                                                $("#data-subrak-nama").val(data.subrak_nama);
                                                $("#data-no-rekam-medik").val(data.no_rekam_medik);
                                                $("#data-nama-pasien").val(data.nama_pasien);
                                                $("#data-warna-dok-id").val(data.warnadokrm_id);
                                                $("#data-warna-dok-nama").val(data.warnadokrm_namawarna);

                                                // Enabled btn add
                                                $("#btn-add").prop("disabled", false);
                                            }
                                        });
                                    }'
                                ],
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <br>
                            <?= Html::button('<i class="fa fa-plus"></i>', ['class' => 'btn btn-xs btn-primary', 'id' => 'btn-add', 'disabled' => 'disabled']) ?>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <?= Html::hiddenInput('pesandokrm_id', '', ['id' => 'data-pesan-dok-rekam-medik-id', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('posisidokrm_id', '', ['id' => 'data-posisi-dok-rekam-medik-id', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('dokrekammedis_id', '', ['id' => 'data-dok-rekam-medis-id', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('tglrekammedis', '', ['id' => 'data-tanggal-rekam-medis', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('lokasirak_id', '', ['id' => 'data-lokasi-rak-id', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('lokasirak_nama', '', ['id' => 'data-lokasi-rak-nama', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('subrak_id', '', ['id' => 'data-subrak-id', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('subrak_nama', '', ['id' => 'data-subrak-nama', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('no_rekam_medik', '', ['id' => 'data-no-rekam-medik', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('nama_pasien', '', ['id' => 'data-nama-pasien', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('warnadokrm_id', '', ['id' => 'data-warna-dok-id', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('warnadokrm_namawarna', '', ['id' => 'data-warna-dok-nama', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('instalasitujuan_id', '', ['id' => 'data-instalasi_id', 'readonly' => 'readonly']) ?>
                    <?= Html::hiddenInput('ruangantujuan_id', '', ['id' => 'data-ruangan_id', 'readonly' => 'readonly']) ?>
                </div>
                <!-- TABLE -->
                <div class="row">
                    <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tb-detail-pemesanan-dok-rekam-medik" style="width:100%">
                        <thead>
                            <tr class="bg-inverse">
                                <th width="20"><?= Yii::t('fe', 'Nomor') ?></th>
                                <th><?= Yii::t('fe', 'Tanggal Rekam Medik') ?></th>
                                <th><?= Yii::t('fe', 'Instalasi Tujuan') ?></th>
                                <th><?= Yii::t('fe', 'Ruangan Tujuan') ?></th>
                                <th><?= Yii::t('fe', 'Nomor Rekam Medik') ?></th>
                                <th><?= Yii::t('fe', 'Nama Pasien') ?></th>
                                <th><?= Yii::t('fe', 'Warna Dokumen') ?></th>
                                <th class="th-hapus"><?= Yii::t('fe', 'Hapus') ?></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            <?php ActiveForm::end() ?>
        </div>
    </div>
</div>

<?php
// Register file js
$this->registerJs('
    // localStorage.clear();
    localStorage.setItem("ruangan", \''.json_encode($resMaster['ruangan']).'\');
', View::POS_END);
$this->registerJs($this->render('js/form.js'), View::POS_END);
?>