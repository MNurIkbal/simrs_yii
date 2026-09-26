<?php
// Author : Ramdhan Nurrachman

use yii\web\View;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;

$this->title = \Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => 'Kasir', 'url' => ['index']];
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
                    </ul>
                </div>
            </div>

            <div class="panel-body">
                <?php $form = ActiveForm::begin([
                    'id' => 'ajax-form'
                ]) ?>
                <?=$form->field($model, 'pembayaranpelayanan_t[pembayaranpelayanan_id]', [
                    'options' => ['tag' => false],
                    'errorOptions' => ['tag' => null]
                ])->hiddenInput()->label(false);?>
                <?=$form->field($model, 'returbayarpelayanan_t[tandabuktibayar_id]', [
                    'options' => ['tag' => false],
                    'errorOptions' => ['tag' => null]
                ])->hiddenInput()->label(false);?>
                <?=$form->field($model, 'tandabuktibayar_t[tandabuktibayar_id]', [
                    'options' => ['tag' => false],
                    'errorOptions' => ['tag' => null]
                ])->hiddenInput()->label(false);?>
                <?=$form->field($model, 'tandabuktikeluar_t[tandabuktikeluar_id]', [
                    'options' => ['tag' => false],
                    'errorOptions' => ['tag' => null]
                ])->hiddenInput()->label(false);?>
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?=Yii::t('fe', 'Data Pasien');?></legend>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?=$form->field($model, 'tgl_pendaftaran', [
                                    'template' => '{label} <div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>{input}</div>',
                                ])->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ]);?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'no_pendaftaran')
                                ->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ]);?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'no_rekam_medik')
                                ->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ]);?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'nama_pasien')
                                ->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ]);?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?=$form->field($model, 'carabayar_nama')
                                ->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ]);?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'penjamin_nama')
                                ->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ]);?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'kelaspelayanan_nama')
                                ->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ]);?>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <br />
                <fieldset class="scheduler-border">
                    <legend class="scheduler-border"><?=Yii::t('fe', 'Pembayaran');?></legend>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <?=$form->field($model, 'pembayaranpelayanan_t[tgl_pembayaran]', [
                                    'template' => '{label} <div class="input-group"><span class="input-group-addon"><i class="fa fa-calendar"></i></span>{input}</div>',
                                ])->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ])->label(\Yii::t('fe', 'Tanggal pembayaran'));?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'total_bayartindakan', [
                                    'template' => '{label} <div class="input-group"><span class="input-group-addon"><i class="fa fa-money"></i></span>{input}</div>',
                                ])->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ]);?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'returbayarpelayanan_t[total_biayaretur]', [
                                    'template' => '{label} <div class="input-group"><span class="input-group-addon"><i class="fa fa-money"></i></span>{input}</div>',
                                ])->textInput([
                                    'class' => 'form-control',
                                ])->label(\Yii::t('fe', 'Nominal retur'));?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?=$form->field($model, 'returbayarpelayanan_t[biaya_administrasi]', [
                                    'template' => '{label} <div class="input-group"><span class="input-group-addon"><i class="fa fa-money"></i></span>{input}</div>',
                                ])->textInput([
                                    'class' => 'form-control',
                                ])->label(\Yii::t('fe', 'Biaya administrasi'));?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'tandabuktibayar_t[jmlpembulatan]', [
                                    'template' => '{label} <div class="input-group"><span class="input-group-addon"><i class="fa fa-money"></i></span>{input}</div>',
                                ])->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ])->label(\Yii::t('fe', 'Pembulatan'));?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'tandabuktibayar_t[jmlpembayaran]', [
                                    'template' => '{label} <div class="input-group"><span class="input-group-addon"><i class="fa fa-money"></i></span>{input}</div>',
                                ])->textInput([
                                    'readonly' => true,
                                    'class' => 'form-control',
                                ])->label(\Yii::t('fe', 'Uang diserahkan'));?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <?=Html::checkbox('carapembayaran', $model->tandabuktibayar_t['carapembayaran'], [
                                    'class' => 'ecollect',
                                    'style' => 'margin:25px 0 15px 0',
                                    'checked' => true
                                ]);?> 
                                &nbsp;&nbsp;
                                <?=\Yii::t('fe', 'E-collection') ?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'tandabuktibayar_t[namapemilik_rek]')
                                ->textInput([
                                    'class' => 'form-control',
                                ])->label(\Yii::t('fe', 'Nama pemilik rekening'));?>
                            </div>
                            <div class="form-group">
                                <?=$form->field($model, 'tandabuktibayar_t[no_rek]')
                                ->textInput([
                                    'class' => 'form-control',
                                ])->label(\Yii::t('fe', 'Nomor rekening'));?>
                            </div>
                        </div>
                    </div>
                </fieldset>
                <br />

                <?=Html::submitButton('<i class="fa fa-floppy-o"></i> '.\Yii::t('fe', 'Simpan').'', [
                    'class' => 'btn btn-teal btn-sm',
                ]);?>
                <?=Html::resetButton('<i class="fa fa-repeat"></i> '.\Yii::t('fe', 'Ulang').'', [
                    'class' => 'btn btn-aqua btn-sm',
                ]);?>
                <?=Html::button('<i class="fa fa-print"></i> '.\Yii::t('fe', 'Print Kwitansi').'', [
                    'class' => 'btn btn-crimson btn-sm',
                ]);?>
                <?=Html::button('<i class="fa fa-print"></i> '.\Yii::t('fe', 'Print BKM').'', [
                    'class' => 'btn btn-crimson btn-sm',
                ]);?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<!-- Untuk Kebutuhan Modal Global -->
<div id="modal_backdrop_search" class="modal fade" style="z-index:1065;" data-backdrop="static">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        </div>
    </div>
</div>
<!-- End -->

<?php 
$this->registerJs('
    $(document).on("click", ".ecollect", function(){
        if($(this).is(":checked")) {
            $("[name=\'InfoPasienSudahBayarForm[tandabuktibayar_t][namapemilik_rek]\']").prop("readonly", false);
            $("[name=\'InfoPasienSudahBayarForm[tandabuktibayar_t][no_rek]\']").prop("readonly", false);
        } else {
            $("[name=\'InfoPasienSudahBayarForm[tandabuktibayar_t][namapemilik_rek]\']").val("").prop("readonly", true);
            $("[name=\'InfoPasienSudahBayarForm[tandabuktibayar_t][no_rek]\']").val("").prop("readonly", true);
        }
    });
    
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            if (data.metadata.status == 201 || data.metadata.status == 200) {
                setTimeout(function () {
                    document.location = baseUrl+"'.(Yii::$app->controller->module->id).'/inf-pasien-sudah-bayar";
                }, 3000);
            }
        }
    });
', View::POS_END, 'b-index');
?>
