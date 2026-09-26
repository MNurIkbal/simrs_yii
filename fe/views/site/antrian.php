<?php

/**
 * @author: arief
 * @description: ui untuk display antrian
**/

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\web\View;

$this->title = 'Antrian';
$this->context->layout = 'antrian';
?>

<div class="row">
	<div class="col-lg-6 col-lg-offset-3">
		<div class="panel registration-form">
			<div class="panel-body">
				<div class="text-center">
					<span class="fa-stack fa-5x text-success">
						<i class="fa fa-square-o fa-stack-2x"></i>
						<i class="fa fa-heartbeat fa-stack-1x"></i>
					</span>
					<h3 class="content-group-lg text-success">SISTEM INFORMASI RUMAH SAKIT <small class="display-block">All fields are required</small></h3>
				</div>
				<div class="text-center">
					<div class="row">
						<div class="col-md-6"><button type="button" class="btn btn-primary btn-lg btn-block text-center">PASIEN BARU</button></div>
						<div class="col-md-6"><button type="button" class="btn btn-warning btn-lg btn-block text-center">PASIEN LAMA</button></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>