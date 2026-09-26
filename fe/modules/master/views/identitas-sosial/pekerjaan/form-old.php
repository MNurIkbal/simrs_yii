<?php

/**
 * @Author: Arief Saputra
 * @Date:   2018-04-18 11:22:11.999 numpang merge
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\Depdrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = [ 'label' => Yii::t('fe', 'Master Identitas Sosial'), 'url' => ['/master/identitas-sosial#view-Pekerjaan'] ];
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">                
                <?=DocoHelpers::generateToolbar([                        
                    'back' => ['attributes' => ['href' => '/master/identitas-sosial#view-Pekerjaan']],
                    'save' => ['attributes' => ['data-target' => 'Pekerjaan-form']],
                    'reset',
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-5">
                    <?php 
                        $form = ActiveForm::begin([
                            'id'=>'Pekerjaan-form',
                            'options'=>[
                                'class'=>'form-horizontal',
                            ],
                            'enableClientValidation'=>false
                        ]);
                    ?>
	                    <div class="form-group">
	                        <label class="control-label col-sm-4"><?=Yii::t('fe','Pekerjaan')?></label>
	                        <div class="col-sm-8">
	                        	<?php echo $form->field($model, 'pekerjaan_nama')->textInput(['class' => 'form-control'])->label(false); ?>
	                        </div>
	                    </div>
	                    <div class="form-group">
	                        <label class="control-label col-sm-4"><?=Yii::t('fe','Nama Lainnya')?></label>
	                        <div class="col-sm-8">
	                        	<?php echo $form->field($model, 'pekerjaan_namalainnya')->textInput(['class' => 'form-control'])->label(false); ?>
                        	</div>
	                    </div>
                    <?php ActiveForm::end() ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$script = <<< JS
	$('#Pekerjaan-form').docoForm('submit',{
		success : function(data) {
		if (data.status == 201)
		    this.formInput[0].reset();
		}
	});
JS;

$this->registerJs($script,View::POS_END,'jkun');
?>