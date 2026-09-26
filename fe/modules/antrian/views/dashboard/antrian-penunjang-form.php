<?php

/**
 * @author: ardi
 * @modify : ali.padilah@docotel.com 1/8/2018
 * @description: ui untuk display antrian penunjang
**/

use app\components\DocoHelpers;
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use yii\web\View;
use yii\helpers\Url;

$this->title = $judulLayarAntrian;
// $this->context->layout = 'antrian';
?>

<div class="col-sm-12 text-right" style="font-size: 20px;">
	<a class="fa fa-chevron-up mr-2 hd-up" onclick="hideHeader()" ></a>
	<a class="fa fa-chevron-down mr-2 hd-down" onclick="showHeader()" style="display:none"></a>
</div>

<div class="site-index">
    <div class="data-module">
        <!-- List styles -->
        <h2 class="content-group text-semibold">
            <?= strtoupper($this->title)?>
        </h2>

        <div class="row pd-20">
			<?php
				$form = ActiveForm::begin([
				    'id' => 'ambil-antrian-form-penunjang', 
				    'type' => ActiveForm::TYPE_HORIZONTAL,
				    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
				]); 
				?>
				<div class="row">
					<div class="col-lg-12">
						<?php foreach($list_poly_penunjang as $poly_active){
							$is_disabled = '';
							if($poly_active['is_active'] == true){
								$status_poly_class = 'label-btn-buka';
								$status_poly_text = 'BUKA';
								// $status_poly_btn = "btn btn-primary btn-lg raised btn-huge";
								
							}else{
								$status_poly_class = 'label-btn-tutup';
								$status_poly_text = 'TUTUP';
								// $status_poly_btn = "btn btn-danger btn-lg raised btn-huge";
								$is_disabled = 'is_disabled';
							} 
							?>
						<div class="col-md-4 plan opt-poly <?=$is_disabled?>">
							<label for="<?='btn-pilih-poly-'.$poly_active['ruangan_id']?>" class="lbl-on btn-poly-penunjang" data-ruangan="<?=$poly_active['ruangan_id']?>" data-jenisantrian="<?=$jenisantrian_id?>" data-fungsiantrian="<?=$fungsiantrian_id?>" data-instalasi="<?=$id?>">
								<h4><b><?=@$poly_active['ruangan_nama']?></b></h4>
								
								<div class="row split-card-antrian-on">
									<div class="col-sm-12">
							    		<p><?= $status_poly_text ?></p>
									</div>
								</div>
							</label>
						</div>

						<?php }?>
					</div>
				</div>
			<?php ActiveForm::end(); ?>
        </div>
    </div>
</div>

<?php
	$this->registerCss($this->render('../assets/css/antrian.css'));
    $this->registerJs($this->render('../assets/js/antrian-penunjang.js'));
?>