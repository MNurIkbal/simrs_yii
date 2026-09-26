<?php

/**
 * @Author: Arief Saputra
 * @Date:   2018-04-18 11:22:11.999
 */
use yii\web\View;

use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
// use yii\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\Depdrop;

use yii\web\JsExpression;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = [ 'label' => Yii::t('fe', 'Master Identitas Sosial'), 'url' => ['/master/identitas-sosial#view-pendidikan'] ];
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
                        'url' => '/',
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([                       
                    'back' => ['attributes' => ['href' => '/master/identitas-sosial#view-pendidikan']],
                    'save' => ['attributes' => ['data-target' => 'pendidikan-form']],
                    'reset'=> [
                            'attributes'=>[
                                'id'=>'reset-pendidikan',
                                'data-parent'=>'.pendidikan-form'
                            ]
                        ],
                ]);?>
            </div>
            <div class="panel-body">
                    <div class="col-md-12">
                    <?php 
                        $form = ActiveForm::begin([
                            'id'=>'pendidikan-form',
                            'options'=>[
                                'class'=>'form-horizontal pendidikan-form',
                            ],
                            'enableClientValidation'=>false
                        ]);
                    ?>
                    <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4"><?=Yii::t('fe','Nama Pendidikan')?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'pendidikan_nama')->textInput(['class' => 'form-control','placeholder' => $model->getAttributeLabel('pendidikan_nama')])->label(false); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4"><?=Yii::t('fe','Indexing')?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'indexing_id')->widget(Select2::classname(), [
                                        'data' => $ddl_indexing,
                                        'options' => ['placeholder' => \Yii::t('fe', 'Pilih')],
                                        'pluginOptions' => [
                                            'allowClear' => true
                                        ],
                                    ])->label(false);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4"><?=Yii::t('fe','Nama Lainnya')?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'pendidikan_namalainnya')->textInput(['class' => 'form-control','placeholder' => $model->getAttributeLabel('pendidikan_namalainnya')])->label(false); ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4"><?=Yii::t('fe','Urutan Pendidikan')?></label>
                            <div class="col-sm-8">
                                <?php echo $form->field($model, 'pendidikan_urutan')->textInput(['class' => 'form-control docoNumberOnly','placeholder' => $model->getAttributeLabel('pendidikan_urutan')])->label(false); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="control-label col-sm-4"><?=Yii::t('fe','Status')?></label>
                            <div class="col-sm-8">
                                <?= $form->field($model, 'is_active', [
                                    'horizontalCssClasses' => [
                                        'label' => 'text-left control-label col-sm-2',
                                        'wrapper' => 'col-md-4',
                                    ]
                                    ])->checkbox(['label' => 'Aktif']);
                                ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                    </div>
                </div>
                <?php ActiveForm::end() ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
    $('#reset-pendidikan').on('click', function () {
        location.reload();
    });

    $('#pendidikan-form').docoForm('submit',{
        success : function(data) {
        if (data.status == 201)
            this.formInput[0].reset();
        }
    });

    $('.hidebtn').hide();
JS;

$this->registerJs($script,View::POS_END,'jkun');
?>