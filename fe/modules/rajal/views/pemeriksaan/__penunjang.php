<?php

/**
 * @Author: afil
 * @Date:   2018-01-16 17:31:57
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-01 09:14:35
 * @Description: 
 */

use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
?>


<div class="panel panel-flat">
    <div class="panel-heading">
        <h5 class="panel-title"><?=$title?></h5>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar clearfix">
            <?=
            DocoHelpers::generateToolbar([
                'cppt' => [
                    'title' => 'CPPT',
                    'icon' => 'fa fa-arrow-left',
                    'attributes' => [
                        'data-options' => 'click',
                        'id' => 'btn-penunjang-back',
                    ]
                ],
            ]) ?>
        </div>
    <div class="panel-body table-responsive">
        <table 
            class="table datatable-basic table-striped table-hover dataTable no-footer table-framed" 
            id="tabel-r" style="width: 100% background-color:red">
            <thead>
                <tr class="bg-inverse">
                    <!-- <th></th> -->
                    <th>No</th>
                    <th><?=Yii::t('fe', 'Tanggal permintaan')?></th>
                    <th><?=Yii::t('fe', 'Unit penunjang')?></th>
                    <th><?=Yii::t('fe', 'Ruangan')?></th>
                    <th><?=Yii::t('fe', 'No rujukan')?></th>
                    <th><?=Yii::t('fe', 'Dokter perujuk')?></th>
                    <th><?=Yii::t('fe', 'Catatan rujukan')?></th>
                    <th><?=Yii::t('fe', 'Catatan persetujuan')?></th>
                    <th><?=Yii::t('fe', 'Status')?></th>
                    <th><?=Yii::t('fe', 'Aksi')?></th>
                </tr>
            </thead>
            <tbody> 
            </tbody>
        </table>
    </div>
</div>

<div class="row">
    <hr>
</div>

<div class='form-order'>
<?php $form = ActiveForm::begin([
    'id' => 'terapi-penunjang-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'action' => '/rajal/pemeriksaan/simpan-terapi-penunjang',
    'enableClientValidation' => false,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]) ?>

<div class="panel panel-default">
    <div class="panel-body">
        <div class="row">
            <div class="col-md-6">
                <?= $form->field($modelPenunjang, 'instalasi_id', [
                        // 'labelOptions' => ['class' => 'text-right']
                    ])->widget(Select2::classname(), [
                        'data' => $data_instalasi,
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
                            'url'=>Url::to(['/rajal/end-point/get-list-ruangan'])
                        ]
                    ]);
                ?>

                <?=
                    $form->field($modelPenunjang, 'catatan_dokterpengirim', [
                    ])->textArea([
                        'class' => 'form-control input-sm',
                    ])->label(Yii::t('fe', 'catatan_instruksi'));
                ?>
            </div>

            <div class='col-md-6'>
                <?= $form->field($modelPenunjang, 'tgl_kirimpasien', [
                        'addon' => [
                            'append' => [
                                'content' => Html::button('Buka Jadwal', [
                                    'class'=>'btn btn-primary btn-buka-jadwal',
                                    'action'=>"/rajal/pemeriksaan/modal-jadwal-operasi",
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
                        'class' => 'form-control input-sm pickadate',
                        'id' => 'penunjang_tgl_kirimpasien',
                    ]); 
                ?>

                <?= $form->field($modelPenunjang, 'pegawai_nama', [
                    ])->textInput([
                        'class' => 'form-control input-sm',
                        'readonly' => true,
                        'id' => 'penunjang_pegawai_nama',
                        'value' => $data_pegawai['nama_pegawai']
                    ]); 
                ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class='penunjang-bedah'>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Jadwal Operasi'); ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5">
                                        <b><?= Yii::t("fe", "Tanggal Permintaan") ?> </b>
                                    </label>
                                    <label class="text-left control-label col-sm-7">
                                        <b>:</b>&nbsp;
                                        <span id='tgl_permintaan_info'>-</span>
                                    </label>
                                    <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jam Mulai") ?></b></label>
                                    <label class="control-label col-sm-7">
                                        <b>:</b>&nbsp;
                                        <span id='jam_mulai_info'>-</span>
                                    </label>
                                    <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter Anestesi") ?></b></label>
                                    <label class="control-label col-sm-7">
                                        <b>:</b>&nbsp;
                                        <span id='dr_anestesi_info'>-</span>
                                    </label>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Dokter Operator") ?></b></label>
                                    <label class="control-label col-sm-7">
                                        <b>:</b>&nbsp;
                                        <span id='dr_operator_info'>-</span>
                                    </label>
                                    <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Jam Selesai") ?></b></label>
                                    <label class="control-label col-sm-7">
                                        <b>:</b>&nbsp;
                                        <span id='jam_selesai_info'>-</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?= $form->field($modelPenunjang, 'has_jadwal', [
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
            <?= Html::hiddenInput('pegawai_id', $data_pegawai['pegawai_id'], ['id' => 'penunjang_pegawai_id']) ?>
        </div>
    </div>

</div><!--  panel -->
<?php echo $this->render('__order_penunjang'); ?>


<div class="row">
    <div class="col-lg-12">
        <div class="pull-left">
            <button type="submit" id="save-terapi" class="btn bg-teal"><i class="fa fa-floppy-o"></i> Simpan</button>
            <button type="button" class="btn bg-warning btn-muat-ulang"><i class="fa fa-refresh"></i> Muat ulang</button>
        </div>
    </div>
</div>
<div class="col-md-12">
    <br>
</div>


<?php ActiveForm::end() ?>

</div>
<?php // File

$this->registerJs('
if(pasienpulang_id != ""){
    $("#penunjang_instalasi_id").prop("disabled", true);
}
var list_pemeriksaanlab = [];
var pend_id = ' . $data_pasien['pendaftaran_id'] . ';
var status_periksa = "'.$status_periksa.'";
status_periksa = (status_periksa == "") ? false : status_periksa;
', View::POS_END);
$this->registerJs($this->render('js/_penunjang.js'), View::POS_END);
?>