<?php
/**
 * @author : iqbal.rukmana@sirs.co.id
 * Powered by Sirs
 */
use app\components\DocoConstants;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\web\View;
use yii\web\JsExpression;
use kartik\widgets\DateTimePicker;
use kartik\widgets\DatePicker;
?>
<style>
    .m-10 {
        margin: 10px 0px !important;
    }
    .ml-10{
        margin-left: -10px !important;
    }
    .datepicker>div {
        display: block;
    }
    .panel .panel-toolbar-skrining {
        padding: 6px 10px 6px 14px;
        border-bottom: 1px solid #cccccc;
        background-color: #f5f5f5;
    }
</style>
<div class="row">
    <div class="col-sm-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h5 class="panel-title"><?= Yii::t('fe', 'Form Skrining Covid') ?></h5>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapsed" href="#skrining-covids"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel panel-toolbar-skrining clearfix">
                <?= DocoHelpers::generateToolbar([
                    'custom-save' => [
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'id' => 'submit-skrining-covid',
                            'data-options' => 'click'
                        ]
                    ],
                    'custom-print' => [
                        'title' => Yii::t('fe', 'Cetak'),
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'data-options' => 'click',
                            'id' => 'btn-print-skrining-covid',
                            'data-target' => '/rajal/informasi/print-skrining-covid?pendaftaran_id=' . $pendaftaranId . '&pasien_id='.$pasienId ,
                            'disabled' => isset($pendaftaranId) && isset($pasienId) ? false : true 
                        ],
                    ],
                ], ''); ?>
            </div>
            <div class="panel-body" id="skrining-covids">
                <?php $form = ActiveForm::begin([
                    'id' => 'form-skrining-covid',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'action' => '/rajal/informasi/save-skrining-covid?pendaftaran_id=' ,
                    'formConfig' => [
                        'labelSpan' => 8, 
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'skip-confirm' => "true"
                    ]
                ]) ?>
                <!-- Hidden inputs -->
                <?= Html::activeHiddenInput($model, 'pendaftaran_id', ['value' => $pendaftaran_id]) ?>
                <?= Html::activeHiddenInput($model, 'is_riwayat', ['value' => $is_riwayat]) ?>
                <!-- form skrining covid start-->
                <div class="row">
                    <div class="col-lg-12">

                        <!-- GEJALA -->                        
                        <div class="panel panel-default panel-gejala">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?= Yii::t('fe', 'GEJALA') ?></h5>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <?= $form->field($model, 'demam')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'demam form-demam'])->label($model->getAttributeLabel('demam'), ['class' => 'col-sm-8 m-10']); ?>
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'batuk_pilek_nyeri_tenggorokan')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'batuk_pilek_nyeri_tenggorokan form-batuk_pilek_nyeri_tenggorokan'])->label($model->getAttributeLabel('batuk_pilek_nyeri_tenggorokan'), ['class' => 'col-sm-8 m-10']); ?>
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'sesak_napas')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'sesak_napas form-sesak_napas'])->label($model->getAttributeLabel('sesak_napas'), ['class' => 'col-sm-8 m-10']); ?>
                                </div>

                            </div>
                        </div>

                        <!-- FAKTOR RESIKO -->
                        <div class="panel panel-default panel-faktor-resiko">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?= Yii::t('fe', 'FAKTOR RESIKO') ?></h5>
                            </div>
                            <div class="panel-body">
                                <div class="col-md-12 ml-10">
                                    <label class="control-label text-black l-label" style="padding-left:0px;"><?= Yii::t('fe', 'Pada hari terakhir sebelum timbul gejala, memenuhi salah satu kriteria berikut :'); ?></label>
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'riwayat_luar_negeri')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'riwayat_luar_negeri faktor-resiko'])->label($model->getAttributeLabel('riwayat_luar_negeri'), ['class' => 'col-sm-8 m-10']); ?>
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'riwayat_dalam_negeri')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'riwayat_dalam_negeri faktor-resiko'])->label($model->getAttributeLabel('riwayat_dalam_negeri'), ['class' => 'col-sm-8 m-10']); ?>
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'resiko_kontak_pasien_covid')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'resiko_kontak_pasien_covid faktor-resiko'])->label($model->getAttributeLabel('resiko_kontak_pasien_covid'), ['class' => 'col-sm-8 m-10']); ?>
                                </div>

                            </div>
                        </div>

                        <!-- KONTRAK ERAT -->
                        <div class="panel panel-default panel-faktor-kontrak-erat">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?= Yii::t('fe', 'KONTRAK ERAT') ?></h5>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <?= $form->field($model, 'kontak_tatap_muka')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'kontak_tatap_muka'])->label($model->getAttributeLabel('kontak_tatap_muka'), ['class' => 'col-sm-8 m-10']); ?>
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'kontak_fisik')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'kontak_fisik'])->label($model->getAttributeLabel('kontak_fisik'), ['class' => 'col-sm-8 m-10']); ?>
                                </div>
                                <div class="form-group">
                                    <?= $form->field($model, 'kontak_perawatan_tanpa_apd')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'kontak_perawatan_tanpa_apd'])->label($model->getAttributeLabel('kontak_perawatan_tanpa_apd'), ['class' => 'col-sm-8 m-10']); ?>
                                </div>

                            </div>
                        </div>

                        <!-- HASIL SWAB -->
                        <div class="panel panel-default panel-faktor-hasil-swab">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?= Yii::t('fe', 'HASIL SWAB') ?></h5>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'swab_positif')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'swab-positif swab-terkonfirmasi'])->label($model->getAttributeLabel('swab_positif'), ['class' => 'col-sm-8 m-10']); ?>
                                    </div>
                                    <div class="col-md-6 form-tgl-swab-positif hidden">
                                        <?= $form->field($model, 'tgl_swab_positif', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-6',
                                                'class' => "asaas "
                                            ]
                                        ])->widget(DatePicker::classname(), [
                                            'name' => 'tgl_anamnesis',
                                            'readonly' => true,
                                            'language' => 'en',
                                            'pluginOptions' => [
                                                'autoclose' => true,
                                                'format' => 'dd-M-yyyy',
                                                // 'endDate' => "0d",
                                            ],
                                            'options' => ['class' => 'tgl-swab-positif form-control'],
                                        ])->label($model->getAttributeLabel('tgl_swab_positif')); ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <?= $form->field($model, 'swab_negatif')->radioList([1 => 'Ya', 0 => 'Tidak'], ['inline' => true, 'class' => 'swab-negatif swab-terkonfirmasi'])->label($model->getAttributeLabel('swab_negatif'), ['class' => 'col-sm-8 m-10']); ?>
                                    </div>
                                    <div class="col-md-6 form-tgl-swab-negatif hidden">
                                        <?= $form->field($model, 'tgl_swab_negatif', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-6',
                                                'class' => "asaas "
                                            ]
                                        ])->widget(DatePicker::classname(), [
                                            'name' => 'tgl_anamnesis',
                                            'readonly' => true,
                                            'language' => 'en',
                                            'pluginOptions' => [
                                                'autoclose' => true,
                                                'format' => 'dd-M-yyyy',
                                                // 'endDate' => "0d",
                                            ],
                                            'options' => ['class' => 'tgl-swab-positif form-control'],
                                        ])->label($model->getAttributeLabel('tgl_swab_negatif')); ?>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- KESIMPULAN -->
                        <div class="panel panel-default panel-faktor-hasil-swab">
                            <div class="panel-heading">
                                <h5 class="panel-title"><?= Yii::t('fe', 'KESIMPULAN') ?></h5>
                            </div>
                            <div class="panel-body">
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-bold"><?=$model->getAttributeLabel('kesimpulan_suspek')?></label>
                                    <div class="col-sm-3">
                                            <ul>
                                                <li>Gejala 1, 2, 3 dan salah satu faktor resiko</li>
                                                <li>Gejala 1, 2 dan salah satu faktor resiko</li>
                                                <li>Gejala 1, 3 dan salah satu faktor resiko</li>
                                            </ul>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-bold"><?=$model->getAttributeLabel('kesimpulan_kontak_erat')?></label>
                                    <div class="col-sm-3">
                                        <span>Salah satu kriteria Kontak Erat</span>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2 text-bold"><?=$model->getAttributeLabel('kesimpulan_terkonfirmasi')?></label>
                                    <div class="col-sm-3">
                                        <span>Jika Hasil Swab No.1 "Ya"</span>
                                    </div>
                                </div>
                            </div>
                            <div class="panel-body">
                                <br>
                                <div class="form-group">
                                    <label class="control-label col-sm-2"><?=$model->getAttributeLabel('suspek')?></label>
                                    <div class="col-sm-1">:</div>
                                    <div class="col-sm-3">
                                        <label class="container-label suspek-values"></label>
                                        <span class="suspek-check hidden" style="font-size:20px; margin-left:-30px"><i class="cr-icon fa fa-check"></i></span>
                                        <?= Html::activeHiddenInput($model, 'suspek', ['class' => 'data-suspek']) ?>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="control-label col-sm-2"><?=$model->getAttributeLabel('terkonfirmasi')?></label>
                                    <div class="col-sm-1">:</div>
                                    <div class="col-sm-3">
                                        <label class="container-label terkonfirmasi-values"></label>
                                        <span class="terkonfirmasi-check hidden" style="font-size:20px; margin-left:-30px"><i class="cr-icon fa fa-check"></i></span>
                                        <?= Html::activeHiddenInput($model, 'terkonfirmasi', ['class' => 'data-terkonfirmasi']) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- form skrining covid end -->
                <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
   
</div>
<?php
    $this->registerJs($this->render('js/_skrining_covid.js'));
?>

<script>
    var is_riwayat = $('#is_riwayat').val()
    if(is_riwayat == 'true'){
        $(':input[type="radio"]').attr("disabled", 'disabled');
        $(':input[type="text"]').attr("disabled", 'disabled');
        $(':input[type="number"]').attr("disabled", 'disabled');
        $(':input[type="checkbox"]').attr("disabled", 'disabled');
        $('#submit-skrining-covid').attr("disabled", 'disabled');
    }
</script>