<?php

/*
* @Author: Sunarko / Master Tempat Tidur
* @Date:   2018-07-23 17:16:31
* @Last Modified by:  
* @Last Modified time: 
*/

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\DepDrop;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['/master']];
$this->params['breadcrumbs'][] = ['label' => \Yii::t('fe', 'Tempat Tidur'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<?php
    $this->registerCss("
        .w-ktt{
            max-width : 385px;
        }
        span.select2-selection.select2-selection--single {
            max-width: 89%;
        }
    ");
?>
<?php
    $form = ActiveForm::begin([
        'id' => 'ajax-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]);
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
            </div> 
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                        'save' => [
                            'attributes' => [
                                'data-target' => 'tempat-tidur-form'
                            ]
                        ],
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'#tempat-tidur-form'
                            ]
                        ],
                        'back'
                    ],'#table-tempat-tidur');
                ?>
            </div>
            <div class="panel-body">
                <div class="col-md-12">
                    <?php
                        $form = ActiveForm::begin([
                            'id' => 'tempat-tidur-form',
                            'enableAjaxValidation' => false,
                            'enableClientValidation' => false,
                            'type' => ActiveForm::TYPE_HORIZONTAL,
                            'formConfig' => [
                                'labelSpan' => 3,
                                'deviceSize' => ActiveForm::SIZE_SMALL
                            ],
                            'options' => [
                                'role' => 'form',
                                'enctype'=>'multipart/form-data'
                            ]
                        ]);
                    ?>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <?= $form->field($model, 'ruangan_id', [
                                'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ]
                                ])->dropDownList($data_ruangan, [
                                    'class' => 'form-control input-sm select2',
                                    'prompt' => Yii::t('fe', '-- Pilih Ruangan--'),
                                    'id' => 'ruangan_id',
                                ]);
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?=$form->field($model, 'no_tempattidur', [
                                    'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-3',
                                    'wrapper' => 'col-md-8'
                                ]
                                ])->textInput([
                                    'class' => 'form-control input-sm',
                                    'placeholder' => Yii::t('fe', $model->getAttributeLabel('no_tempattidur'))
                                ]); 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            <?= $form->field($model, 'kamarruangan_id', [
                                'horizontalCssClasses' => ['label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ]
                                ])->dropDownList($data_kamar, [
                                    'class' => 'form-control select2 input-sm',
                                    'prompt' => Yii::t('fe', '-- Pilih Kamar--'),
                                    'id' => 'kamarruangan_id',
                                ]);
                            ?>
                        </div>
                        <div class="col-md-6">
                                <div class="form-group checkbox-consignment">
                                    <label for="is_active" class="col-sm-3 control-label">
                                        <?= Yii::t('fe', 'Integrasi Aplikasi'); ?>
                                    </label>
                                    <div class="col-md-0">
                                        <?= $form->field($model, 'is_rekapkinerjaprofesi')->checkbox(['class' => 'is_rekapkinerjaprofesi'])->label(false); ?>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="col-md-12">
                        <div class="col-md-6">
                            
                        </div>
                        <div class="col-md-6">
                                <div class="form-group checkbox-consignment">
                                    <label for="is_active" class="col-sm-3 control-label">
                                        <?= Yii::t('fe', 'Setting Occupied'); ?>
                                    </label>
                                    <div class="col-md-0">
                                        <?= $form->field($model, 'is_terisi')->checkbox(['class' => 'is_terisi','disabled' => true])->label(false); ?>
                                    </div>
                                </div>
                            </div>
                    </div>
                    <div class="hidden">
                        <button type="reset"></button>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
    $this->registerJs("
    $(document).ready(function() {
        const url = document.URL;
        const patternAction = url.match(/update/g);
        const kamarruangan = $('#kamarruangan_id').val();
        if (kamarruangan) {
            if (patternAction) {
                // alert(patternAction[0] == 'update');
                // console.log(patternAction[0] == 'update');
                if (patternAction[0] == 'update') {
                    // setTimeout(() => {
                        $('#kamarruangan_id').val($model->kamarruangan_id).trigger('change').trigger('depdrop:change');
                        $('#kettempattidur_id').on('depdrop:afterChange', function (event, id, value) {
                            $(this).val($model->kettempattidur_id).trigger('change').trigger('depdrop:change');
                        });
                      

                    // }, 1200);
                }
            }
        }
    });

    ");

    $this->registerJs($this->render('../assets/js/tempattidur.js'));
?>