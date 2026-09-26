<?php

use yii\web\View;
use yii\web\JsExpression;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DatePicker;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
?>

<style type="text/css">
.datepicker>div {
    display: block;
}
.kv-date-remove {
    display: none;
}
</style>

<div class="panel panel-flat">
    <div class="panel-heading">
        <div class="row">
            <div class="col-md-7">
                <h5 class="panel-title"><?= $title ?></h5>   
            </div>    
            <div class="col-md-4">                   
                <?= Html::dropDownList('template_id', '', array(),
                    [
                        'id'    => 'list-status-kesehatan',
                        'class' => 'form-control select2  input-sm',
                        'prompt' => Yii::t('fe', '-- Pilih Template --'),
                    ]
                ); ?>
            </div>   
            <div class="col-md-1">  
                <button type="button" class="btn btn-sm btn-info" id="pilih-status-kesehatan">Pilih</button>
            </div>   
        </div>
    </div>
    <div class="panel-toolbar clearfix">
        <div class="col-md-8">
            <?=DocoHelpers::generateToolbar([
                'save' => [
                    'attributes' => [
                        'form_id' => 'form-status-kesehatan', 
                        'id' => 'submit-status-kesehatan',
                    ]
                ],
                'cetak-report'=>[
                    'type'=>'button',
                    'title' => \Yii::t('fe', 'Cetak'),
                    'icon' => 'fa fa-print',
                    'method' => 'not-exist',
                    'attributes' => [
                        'id'=>'btn-print',
                        'class'=>'btn-print-cetak-report',
                        'disabled' => empty($model->pendaftaran_id) ? true : false,
                        'data-options'=>'link',
                        'target' => '_blank',
                    ]
                ],
            ],'');?>
        </div>
        <div class="col-md-4 template" align="right">
            <button  type="button" class="btn btn-sm btn-danger" id="hapus-template-status-kesehatan" disabled><li class="fa fa-trash"></li> Hapus Template</button>
            <?= Html::button('<i class="fa fa-floppy-o"></i> '. Yii::t('fe', 'Simpan Template'), [
                'class' => 'btn btn-primary save-template btn-sm',
                'id' => 'save-template-status-kesehatan',
                'data-toggle' => 'modal',
                'data-target' => '#modal_backdrop',
                'action'      => '/mcu/pemeriksaan/modal-template?id='.$pendaftaran_id.'&type=status_kesehatan&modal=is_modal',true,
                'disabled'    => 'disabled'
            ]); ?>
        </div>
    </div>
    <div class="panel-body">
        <div class="row">
            <div class="col-md-12">
                <?php
                    $form = ActiveForm::begin([
                        'id' => 'form-status-kesehatan',
                        'enableAjaxValidation'=>false,
                        'enableClientValidation'=>false,
                        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                    ]);
                ?>
                <div class="flex-container">
                    <div class="flex">
                        <?php $model->tgl_periksa = !empty($model->tgl_periksa) ? date('d-m-Y', strtotime($model->tgl_periksa)) : date('d-m-Y'); ?>
                        <?= $form->field($model, 'tgl_periksa', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-5'
                                ]
                            ])->widget(DatePicker::classname(), [
                                'name' => 'date_12',
                                'readonly' => true,
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd-mm-yyyy',
                                    'endDate' => "0d",
                                ]
                            ]); 
                        ?>
                        <?=$form->field($model, 'nama', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-5',
                                ]
                            ])->textInput([
                                'placeholder' => $model->getAttributeLabel('nama'),
                                'class' => 'form-control input-sm',
                                'readonly' => true,
                            ]);
                        ?>
                        <?=$form->field($model, 'no_rm', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-5',
                                ]
                            ])->textInput([
                                'placeholder' => $model->getAttributeLabel('no_rm'),
                                'class' => 'form-control input-sm',
                                'readonly' => true,
                            ]);
                        ?>
                        <?=$form->field($model, 'usia', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-5',
                                ]
                            ])->textInput([
                                'placeholder' => $model->getAttributeLabel('usia'),
                                'class' => 'form-control input-sm',
                                'readonly' => true,
                            ]);
                        ?>
                        <?=$form->field($model, 'bagian', [
                                'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-2',
                                    'wrapper' => 'col-md-5',
                                ]
                            ])->textInput([
                                'placeholder' => $model->getAttributeLabel('bagian'),
                                'class' => 'form-control input-sm',
                            ]);
                        ?>
                        <?= $form->field($model, 'status_kesehatan')->radioList($data_kelaikan, [
                            'item' => function($index, $label, $name, $checked, $value) {
                                $return = '<label class="modal-radio">';
                                if($checked) {
                                    $return .= '<input checked type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kelaikan">';
                                }
                                else {
                                    $return .= '<input type="radio" name="' . $name . '"value="' . $value . '" tabindex="3" class="opt_kelaikan">';
                                }
                                $return .= '<i></i>';
                                $return .= '&nbsp;&nbsp;<span>' . ucwords($label) . '</span>';
                                $return .= '</label>&nbsp;&nbsp;';
                                return $return;
                            },
                            'inline' => true, 
                        ]); ?>
                        <?= $form->field($model, 'catatan')->textarea([
                            'class' => 'form-control input-sm',
                            'placeholder' => $model->getAttributeLabel('catatan'),
                            'rows' => 5
                        ]); ?>
                        <?= $form->field($model, 'evaluasi', [
                                'addon' => ['append' => ['content' => 'tahun']],
                            ])->textInput([
                                'class' => 'form-control input-sm doco-number evaluasi',
                        ]); ?>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $('.select2').select2();
    $(document).ready(function(){
        $("#form-status-kesehatan").docoForm('submit', {
            skipErrorNotif: true,
            success : function(data) {
                $("#tab-status-kesehatan").trigger('click')
            }
        });
    });

    $("#btn-print").click(function(e){
        e.preventDefault();

        var url = "/reports/viewer/status-kesehatan-mcu?pendaftaran_id=<?= $model->pendaftaran_id ?>";
        $(this).attr("data-target", url);
    });
</script>

<?php
$this->registerJs("
    var id_form = 'form-status-kesehatan';
    var type = 'status-kesehatan';
    var detail_type = 'status_kesehatan';
    var tab = 'tab-status-kesehatan';
", View::POS_END);
$this->registerJs($this->render('../js/_template.js'), View::POS_END);
?>
