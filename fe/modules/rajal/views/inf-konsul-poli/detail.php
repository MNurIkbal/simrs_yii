<?php

/**
 * @Author: Ayip
 * @Date:   2018-03-03 11:40:04
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-10 13:54:25
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><b><?=$title;?></b> - <?=$action?></h5>
</div>
<div class="modal-body">
    <div class="panel panel-white">
        <div class="panel-toolbar clearfix">
        <?php 
            if($data['status_periksa'] <= 1) {
                echo DocoHelpers::generateToolbar([
                    'save' => [
                        'attributes' => [
                            'form_id' => 'infKonsulPoli-form',
                            'id' => 'submit-konsulpoli'
                        ]
                    ],
                    'custom-reset' => [
                        'type'=>'button',
                        'title' => Yii::t('fe', 'Muat ulang'),
                        'icon' => 'fa fa-refresh',
                        'attributes' => [
                           'class'=>'reset-diagnosa',
                           'data-options'=>'click',
                        ],
                    ],
                ]);
            }
        ?>

        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-12">  
                    <?php 
                        $form = ActiveForm::begin([
                            'id'=>'infKonsulPoli-form',                             
                            'options'=>[
                                'class'=>'form-horizontal',                                 
                            ],
                            // 'enableClientValidation'=>false
                        ]);                         
                    ?>

                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=Yii::t('fe','No. Rekam Medik')?></label>
                        <div class="col-sm-9">
                            <?php
                            echo Html::textInput('no_rekam_medik',$data['no_rekam_medik'],['class'=>'form-control','readonly'=>'true']);
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=Yii::t('fe','No. Pendaftaran')?></label>
                        <div class="col-sm-9">
                            <?php
                            echo Html::textInput('no_pendaftaran',$data['no_pendaftaran'],['class'=>'form-control','readonly'=>'true']);
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=Yii::t('fe','Nama Pasien')?></label>
                        <div class="col-sm-9">
                            <?php
                            echo Html::textInput('nama_pasien',$data['nama_pasien'],['class'=>'form-control','readonly'=>'true']);
                            ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=Yii::t('fe','Ruangan Tujuan')?></label>
                        <div class="col-sm-9">
                        <?php
                        if ($data['status_periksa'] <= 1){
                            echo $form->field($model, 'ruangan_id')->dropDownList(
                                    $list_ruangan,
                                    [
                                        'class' => 'select2 autoListRuangan konsulpoli-reset',
                                        'id' => 'select2_list_ruangan_id',
                                        'prompt' => Yii::t('fe', '--Pilih ruangan--')
                                    ]
                                )->label(false);
                        }else{
                            echo Html::textInput('ruangan_tujuan',$data['ruangan_tujuan'],['class'=>'form-control','readonly'=>'true']);
                        }
                        ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=Yii::t('fe','Nama Dokter')?></label>
                        <div class="col-sm-9">
                        <?php 
                        if ($data['status_periksa'] <= 1){
                            echo $form->field($model, 'pegawai_id')->widget(DepDrop::classname(), [
                                'options'=>[
                                    'id' => 'depdrop_dokter',
                                    'class' => 'form-control konsulpoli-reset select2'
                                ],
                                'pluginOptions'=>[
                                    'depends'=>['select2_list_ruangan_id'],
                                    'placeholder'=> \Yii::t('fe', 'Pilih Dokter'),
                                    'url'=>Url::to(['/rajal/inf-konsul-poli/list-dokter'])
                                ]
                            ])->label(false); 
                            echo Html::activeHiddenInput($model, 'jadwaldokter_id');
                        }else{
                            echo Html::textInput('nama_dokter',$data['nama_dokter'],['class'=>'form-control','readonly'=>'true']);
                        }
                        ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="control-label col-sm-3"><?=Yii::t('fe','Catatan Dokter Konsul')?></label>
                        <div class="col-sm-9">
                        <?php
                        if ($data['status_periksa'] <= 1){
                            echo $form->field($model, 'catatan_dokter_konsul')->textarea(
                                    [
                                        'class' => 'form-control konsulpoli-reset-text', 
                                        'value' => @$data['catatan_dokter_konsul'], 
                                    ]
                                )->label(false);
                        }else{
                            echo Html::textInput('catatan_dokter_konsul', @$data['catatan_dokter_konsul'], ['class'=>'form-control','readonly'=>'true']);
                        }
                        ?>
                        </div>
                    </div>

                    <?php if($data['status_periksa'] > 1) : ?>
                    <div class="form-group">
                        <label class="control-label col-sm-3">&nbsp;</label>
                        <div class="col-sm-9">
                            <span class="label label-primary"><?php echo $data['status'] ?></span>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="hidden">
                        <?=Html::submitButton('simpan',['class'=>'simpan'])?>
                        <?=Html::resetButton('reset',['class'=>'reset'])?>
                    </div>
                    <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs("
    $(document).on('click', '.reset-diagnosa', function (e) {
        $('.konsulpoli-reset').val(null).trigger('change');
        $('.konsulpoli-reset-text').val(null);
    });

    $(document).on('click', '#submit-konsulpoli', function (e) {
        $('#infKonsulPoli-form').docoForm('submit',{
            success : function(data) {
                if (data.status == 201)
                    this.formInput[0].reset();
                table.draw();
            }
        });
    });

    $(document).on('change', '#depdrop_dokter', function(e){
        let selected = $(this).find(':selected');
        let jadwaldokter_id = selected.data('jadwaldokter');
        let target = $('#konsulpoliform-jadwaldokter_id');

        if (jadwaldokter_id !== 'undefined'){
            target.val(jadwaldokter_id);
        }
    });
", View::POS_END);
?>