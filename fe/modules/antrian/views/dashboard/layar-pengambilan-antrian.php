<?php

/**
 * @author: arief
 * @description: ui untuk display antrian
**/

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;
use yii\helpers\Url;

$this->title = $judulLayarAntrian;
$this->context->layout = 'antrian';
?>

<div class="row">
	<div class="col-md-10 col-md-offset-1">
		<div class="text-center" style="margin-bottom: 3%;">
			<div class="content-group-sm">
				<h1 class="no-margin text-black title-antrian">
					<?php echo $judulLayarAntrian; ?>&nbsp;<small class="display-block"><?=$jenisLayarAntrian; ?></small>
				</h1>
			</div>
			<div id="clock1" class="label label-rounded label-default"></div>
		</div>
		<div class="text-center">
			<div class="row">
			<?php if($layar != null): ?>
				<?php if(count($FungsiAntrianList) > 0) : 
				$offset = '';
				if(count($FungsiAntrianList) < 3){
					if(count($FungsiAntrianList) == 2) {
						$offset = 'col-md-offset-2';
					}
					else {
						$offset = 'col-md-offset-4';
					}
				}
				?>
				<div class="col-md-10 col-md-offset-1">
					<div class="row">
					<?php 
					$i = 0;
					foreach($FungsiAntrianList as $row): 
					$primaryKey = DocoHelpers::encrypt($row['konfigantrian_id']);
					?>
						<div class="col-md-4 <?php echo ($i == 0 ? $offset:'') ?>">
							<a href="<?php echo Url::to($module.'/wizard?id='.$primaryKey); ?>" type="button" class="btn btn-info btn-lg btn3d" 
								data-fungsi="<?php echo $primaryKey; ?>"
								data-toggle= "modal",
								data-target= "#modal_backdrop" >
								<h2 class="no-margin text-black">ANTRIAN<br/><?php echo strtoupper($row['fungsi_name']); ?></h2>
							</a>
						</div>
					<?php $i++;
					endforeach; ?>
					</div>
				</div>
				<?php else: ?>
				<div class="col-md-12">
					<span class="label label-rounded label-warning"><h2 class="no-margin"><i class="fa fa-exclamation-triangle"></i> Harap Mengecek Kembali Konfigurasi Antrian!</h2></span>
				</div>
				<?php endif; ?>
			<?php else: ?>
				<div class="col-md-12">
					<span class="label label-rounded label-danger"><h2 class="no-margin"><i class="fa fa-ban"></i> Maaf Terjadi Terjadi Kesalahan Pada Mengambilan Antrian!</h2></span>
				</div>
			<?php endif; ?>
			</div>
		</div>
	</div>
</div>

<div class="modal fade modal-step-antrian" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel">
	<div class="modal-dialog modal-lg" role="document">
		<div class="modal-content">
			...
		</div>
	</div>
</div>

<?php
$this->registerJs(
    "$(\".steps-basic\").steps({
        headerTag: \"h6\",
        bodyTag: \"fieldset\",
        transitionEffect: \"fade\",
        titleTemplate: '<span class=\"number\">#index#</span> #title#',
        labels: {
            finish: 'Cetak Antrian'
        },
        onFinished: function (event, currentIndex) {
            alert(\"Form submitted.\");
        }
    });
	$('.img-check').click(function(e) {
        $('.img-check').not(this).removeClass('check')
    		.find('input').prop('checked',false);
    	$(this).addClass('check')
            .find('input').prop('checked',true);
    });
    $('#clock1').clock({
    	'dateFormat':'l, d F Y',
    	'timeFormat':'H:i:s'
    	});
	",
    View::POS_READY,
    'layarantrian'
);
?>