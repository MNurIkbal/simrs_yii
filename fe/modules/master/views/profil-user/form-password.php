<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-01-09 14:03:49
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-01-10 11:53:22
 */


use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\View;
$this->title = 'Ubah Kata Sandi';
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-white">
			<div class="panel-heading">
				<h3 class="panel-title"><b><?=$this->title?></b></h3>
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
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
			</div>
			<div class="panel-body">
				<div class="row">					
					<div class="col-md-8">
						
						
						<?php 						
						$form = ActiveForm::begin([
							'id'=>'rubahpasword-form',
							//'type'=>ActiveForm::TYPE_HORIZONTAL,
							// 'formConfig'=>[
							// 	'labelSpan'=>5
							// ],
							'options'=>[
								'class' => 'form-horizontal', 			                    
			                    'role' => 'form',
							],
						]);
						?>
						<div class="form-group required">
							<div class="col-lg-8">
								<?=$form->field($model, 'nama_pemakai')->textInput()?>
							</div>
						</div>
						<div class="form-group required">
							<div class="col-lg-8">
								<?=$form->field($model, 'kata_sandi')->passwordInput()?>
							</div>
						</div>
						<div class="form-group required">
							<div class="col-lg-8">
								<?=$form->field($model, 'kata_sandi_baru')->passwordInput()?>
							</div>
						</div>
						<div class="form-group required">
							<div class="col-lg-8">
								<?=$form->field($model, 'kata_sandi_konfirmasi')->passwordInput()?>
							</div>
						</div>
						<div class="form-group">
					        <div class="col-md-8 text-right">
					            <?= Html::submitButton(\Yii::t('fe','Simpan'), ['class' => 'btn btn-primary']) ?>
					            <?= Html::resetButton(\Yii::t('fe','Ulang'), ['class' => 'btn btn-default','style'=>'margin-right: 10px']) ?>
					        </div>
					    </div>
						<?php ActiveForm::end(); ?>
					</div>
				</div>
			</div>
		</div>

	</div>
</div>
<?php 
$this->registerJs("
	$('#rubahpasword-form').docoForm('submit',{
        success : function(data) {
            this.formInput[0].reset();            
        }
    });
	");
?>