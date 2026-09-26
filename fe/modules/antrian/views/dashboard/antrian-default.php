<?php

/**
 * @author: ardi
 * @description: ui untuk display antrian default
**/

use app\components\DocoHelpers;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\View;
use yii\helpers\Url;

$this->title = $judulLayarAntrian;
$this->context->layout = 'antrian';
?>

<?php $imgRs = Url::to('@web/media/img/icon-app/icon-hospital.png');?>
<div class="container-fluid">
	<section class="panel box-antrian">
		<header class="panel-heading bgk-simrsdoco">
			<div class="panel-body">
				<div class="col-md-2">
					<div class="text-center img-rs">
						<img src="<?=$imgRs?>">
					</div>
				</div>
				<div class="col-md-10">
					<h1>DOCO HEALTH</h1>
					<h2 class="no-margin title-antrian">
						<?php echo $judulLayarAntrian; ?>&nbsp;<small class="display-block"><?=$jenisLayarAntrian; ?></small>
					</h2>
				</div>
			</div>
		</header>
		<div class="panel-body" style="border: 10px solid #407e7a">
			<div class="col-md-10 col-md-offset-1">
				<?php
				$form = ActiveForm::begin([
				    'id' => 'ambil-antrian-form-default', 
				    'type' => ActiveForm::TYPE_HORIZONTAL,
				    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
				]); 
				?>
				<div class="row">
					<div class="col-lg-12">
						<div class="col-md-4 col-md-offset-4 plan">
							<label class="btn btn-primary btn-lg raised btn-huge" id="default-antrian-btn">
								<div class="container-label">
								    <h4><b>Ambil Antrian</b></h4> 
								</div>
							</label>
						</div>
					</div>
				</div>
				<?php ActiveForm::end(); ?>
			</div>
		</div>
		<div class="panel-footer" style="background-color: #407e7a;
		border-color: #407e7a;">
			<div class="text-center">
				<div id="clock1" class="label label-rounded label-default"></div>
			</div>
		</div>
	</section>
</div>

<?php
	$this->registerCss($this->render('../assets/css/antrian.css'));
    $this->registerJs($this->render('../assets/js/antrian-default.js'));
?>