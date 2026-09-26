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
        <?=DocoHelpers::generateToolbar([
            'save' => [
                'attributes' => [
                    'form_id' => 'form-kesimpulan', 
                    'id' => 'submit-kesimpulan',
                ]
            ],
            'cetak-report'=>[
                'type'=>'button',
                'title' => \Yii::t('fe', 'Cetak Report Umum'),
                'icon' => 'fa fa-print',
                'method' => 'not-exist',
                'attributes' => [
                    'id'=>'btn-print-cetak',
                    'class'=>'btn-print-cetak-report',
                    'disabled' => $isCetakMcu,
                    'data-options'=>'link',
                    'target' => '_blank',
                ]
            ],
        ],'');?>
    </div>
    <div class="panel-body">
        <div class="row">
            <?php
                $form = ActiveForm::begin([
                    'id' => 'form-kesimpulan',
                    'enableAjaxValidation'=>false,
                    'enableClientValidation'=>false,
                    // 'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                ]);
            ?>
            <div class="flex-container">
                <div class="flex-50">
                    <?=$form->field($modelKesimpulan, 'pegawai_nama', [
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2',
                                'wrapper' => 'col-md-5',
                            ]
                        ])->textInput([
                            'placeholder' => $modelKesimpulan->getAttributeLabel('pegawai_nama'),
                            'class' => 'form-control input-sm',
                            'readonly' => 'readonly'
                            ]);
                    ?>
                    <?=Html::activeHiddenInput($modelKesimpulan, 'pegawai_id', ['class'=>'form-control input-sm'])?>
                    <?=$form->field($modelKesimpulan, 'ikhtisar_singkat')->textarea([
                        'class' => 'form-control input-sm',
                        'placeholder' => $modelKesimpulan->getAttributeLabel('ikhtisar_singkat'),
                        'rows' => 5
                    ]); ?>
                    <?= $form->field($modelKesimpulan, 'kesimpulan')->widget(Select2::classname(), [
                            'data' => $data_saran,
                            'options' => ['placeholder' => Yii::t('fe', '— Pilih Hasil —')],
                            'pluginOptions' => [
                                // 'allowClear' => true
                            ],
                        ]);
                    ?>
                    <?=$form->field($modelKesimpulan, 'saran')->textarea([
                        'class' => 'form-control input-sm',
                        'placeholder' => $modelKesimpulan->getAttributeLabel('saran'),
                        'rows' => 5
                    ]); ?>
                    <?=$form->field($modelKesimpulan, 'catatan')->textarea([
                        'class' => 'form-control input-sm',
                        'placeholder' => $modelKesimpulan->getAttributeLabel('catatan'),
                        'rows' => 5
                    ]); ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function(){
        $("#form-kesimpulan").docoForm('submit', {
            success : function(data) {
                $("#tab-kesimpulan").trigger('click')
                // docoResetForm($("#form-kesimpulan"));
                // $("#resumemedisform-kesimpulan").val(null).trigger('change');
            }
        });
    });

    $("#btn-print-cetak").click(function(e){
        e.preventDefault();

        var url = "/mcu/pemeriksaan/cetak-report?id=" + pendaftaran_id;
        $(this).attr("data-target", url);
    });
</script>