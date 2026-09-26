<?php

/**
 * @Author: Rizal
 * @Date:   2018-07-25 11:16:29
 */

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;
use yii\web\View;
?>


<div class="row">
    <hr>
</div>

<?php $form = ActiveForm::begin([
    'id' => 'terapi-penunjang-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => '/ranap/pemeriksaan-rawat-inap/simpan-terapi-penunjang',
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<!-- <div class="panel panel-default"> -->
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($modelPenunjang, 'instalasi_id', [
                        // 'labelOptions' => ['class' => 'text-right']
                    ])->widget(Select2::classname(), [
                        'data' => $listInstalasiPenunjang,
                        'options' => [
                            'id' => 'penunjang_instalasi_id',
                            'class' => 'form-control input-sm', 
                            'prompt' => Yii::t('fe', '--Pilih--'),
                        ],
                    ]
                ); ?>

                <?=
                    $form->field($modelPenunjang, 'ruangan_id', [
                        // 'labelOptions' => ['class' => 'text-right']
                    ])->widget(DepDrop::classname(), [
                        'options'=>['id'=>'penunjang_ruangan_id'],
                        'type'=>DepDrop::TYPE_SELECT2,
                        'pluginOptions'=>[
                            'depends'=>['penunjang_instalasi_id'],
                            'placeholder'=>Yii::t('fe', '--Pilih--'),
                            'url'=>Url::to(['/ranap/end-point/get-list-ruangan'])
                        ]
                    ]);
                ?>
            </div>

            <div class='col-md-6'>
                <?= $form->field($modelPenunjang, 'tgl_kirimpasien', [
                        // 'labelOptions' => ['class' => 'text-left required'],
                        // 'options' => ['id' => 'kirimpasien' 'class'=>'form-group'],
                        'addon' => [
                            'append' => [
                                'content' => Html::button('Buka Jadwal', [
                                    'class'=>'btn btn-primary btn-buka-jadwal',
                                    'action'=>"/ranap/pemeriksaan-rawat-inap/modal-jadwal-operasi",
                                    'data-width'=>"80%",
                                    'data-toggle'=>"modal",
                                    'data-target'=>"#modal_backdrop",
                                    'data-options'=>"click",
                                    'disabled' => true
                                ]), 
                                'asButton' => true,
                            ]
                        ]
                    ])->textInput([
                        'class' => ' form-control input-sm pickadate',
                        'id' => 'penunjang_tgl_kirimpasien',
                    ]); 
                ?>

                <?= $form->field($modelPenunjang, 'pegawai_nama', [
                        // 'labelOptions' => ['class' => 'text-right'],
                    ])->textInput([
                        'class' => 'form-control input-sm',
                        'readonly' => true,
                        'id' => 'penunjang_pegawai_nama',
                        'value' => $pegawai['nama_pegawai']
                    ]); 
                ?>

                <div class='penunjang-bedah'>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                    <h6 class="panel-title"><b><?= Yii::t('fe', 'Jadwal Operasi'); ?></b></h6>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-5">
                                                <b><?= Yii::t("fe", "Tgl Permintaan") ?></b>
                                            </label>
                                            <label class="control-label col-sm-7">
                                                <b>:</b>&nbsp;
                                                <span id='tgl_permintaan_info'>-</span>
                                            </label>
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jam Mulai") ?></b></label>
                                            <label class="control-label col-sm-7">
                                                <b>:</b>&nbsp;
                                                <span id='jam_mulai_info'>-</span>
                                            </label>
                                            <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jam Selesai") ?></b></label>
                                            <label class="control-label col-sm-7">
                                                <b>:</b>&nbsp;
                                                <span id='jam_selesai_info'>-</span>
                                            </label>
                                        </div>
                                        <div class="col-md-6">
                                            <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "Dokter Operator") ?></b></label>
                                            <label class="control-label col-sm-6">
                                                <b>:</b>&nbsp;
                                                <span id='dr_operator_info'>-</span>
                                            </label>
                                            <label class="text-left control-label col-sm-6"><b><?= Yii::t("fe", "Dokter Anestesi") ?></b></label>
                                            <label class="control-label col-sm-6">
                                                <b>:</b>&nbsp;
                                                <span id='dr_anestesi_info'>-</span>
                                            </label>
                                        </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?= $form->field($modelPenunjang, 'has_jadwal', [
                        // 'labelOptions' => ['class' => 'text-right'],
                    ])->hiddenInput([
                        'id' => 'penunjang_has_jadwal'
                    ])->label(false); 
                ?>
            </div>


            <?= Html::hiddenInput('instalasi_id', '', ['id' => 'penunjang_instalasi_id2']) ?>
            <?= Html::hiddenInput('ruangan_id', '', ['id' => 'penunjang_ruangan_id2']) ?>
            <?= Html::hiddenInput('penjamin_id', $data_pasien['penjamin_id'], ['id' => 'penunjang_penjamin_id']) ?>
            <?= Html::hiddenInput('kelaspelayanan_id', $data_pasien['kelaspelayanan_id'], ['id' => 'penunjang_kelaspelayanan_id']) ?>
            <?= Html::hiddenInput('pendaftaran_id', $data_pasien['pendaftaran_id'], ['id' => 'penunjang_pendaftaran_id']) ?>
            <?= Html::hiddenInput('pasienadmisi_id', $data_pasien['pasienadmisi_id'], ['id' => 'penunjang_pasienadmisi_id']) ?>
            <?= Html::hiddenInput('pegawai_id', $pegawai['pegawai_id'], ['id' => 'penunjang_pegawai_id']) ?>
            <?= Html::hiddenInput('cppt_id', $cppt_id, ['id' => 'penunjang_cppt_id']) ?>
            <?= Html::hiddenInput('instruksi_id', $modelInstruksi['instruksi_id'], ['id' => 'penunjang_instruksi_id']) ?>
        </div>

    </div><!--  panel -->
    <?php echo $this->render('_order_penunjang', []); ?>


    <div class="panel-footer">
        <div class="col-lg-12">
            <div class="pull-left">
                <button type="button" id="save-terapi-penunjang" class="btn bg-teal"><i class="fa fa-floppy-o"></i> Simpan</button>
                <button type="button" id="muat-ulang" class="btn bg-warning"><i class="fa fa-refresh"></i> Muat ulang</button>
            </div>
        </div>
    </div>
<!-- </div> -->


<?php ActiveForm::end() ?>


<?php // File

$this->registerJs('
var list_pemeriksaanlab = [];
var instalasi_id = "'. ($data_penunjang ? $data_penunjang['pasienkirimkeunitlain']['instalasi_id'] : "") .'";
var ruangan_id = "'. ($data_penunjang ? $data_penunjang['pasienkirimkeunitlain']['ruangan_id'] : "") .'";
var pasienadmisi_id = "'. ($data_pasien ? $data_pasien['pasienadmisi_id'] : "") .'";
var ruangan = "'.$model['ruangan_id'].'";
var pasienId = "'.$model['pasien_id'].'";
var kelaspelayananId = "'. $data_pasien['kelaspelayanan_id'] .'";
', View::POS_END);
$this->registerJs($this->render('js/terapi_penunjang.js'), View::POS_END);
?>