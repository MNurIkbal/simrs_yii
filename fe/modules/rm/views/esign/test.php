<?php

$this->context->layout = 'main';
?>

<div class="row body">
    <div class="col-md-6 col-md-offset-3 col-sm-12">
        <div class="panel panel-white">
            <div class="panel-heading">
            	<h2 class="text-center"><b>Dokumen TTE Valitation</b></h2>
            </div>
            <div class="panel-body">
            	<div class="row">
            		<div class="col-md-3">Nama Dokumen</div>
            		<div class="col-md-1 text-right">:</div>
            		<div class="col-md-8"><?= $data['filename']; ?></div>
            	</div>
            	<div class="row">
            		<div class="col-md-3">Tipe Dokumen</div>
            		<div class="col-md-1 text-right">:</div>
            		<div class="col-md-8"><?= strtoupper(pathinfo($data['filename'], PATHINFO_EXTENSION)); ?></div>
            	</div>
            	<div class="row">
            		<div class="col-md-3">Nama Penandatangan</div>
            		<div class="col-md-1 text-right">:</div>
            		<div class="col-md-8"><?= $data['signers'][0]['name']; ?></div>
            	</div>
            	<div class="row">
            		<div class="col-md-3">Nama Perusahaan</div>
            		<div class="col-md-1 text-right">:</div>
            		<div class="col-md-8"><?=  Yii::$app->docoVars->identity("nama_rumahsakit"); ?></div>
            	</div>
            	<div class="row">
            		<div class="col-md-3">Tanggal</div>
            		<div class="col-md-1 text-right">:</div>
            		<div class="col-md-8"><?= $data['signers'][0]['datetime']; ?></div>
            	</div>
            	<div class="row">
            		<div class="col-md-3">Alasan</div>
            		<div class="col-md-1 text-right">:</div>
            		<div class="col-md-8"><?= $data['signers'][0]['reason']; ?></div>
            	</div>
            	<div class="row">
            		<div class="col-md-3">Lokasi</div>
            		<div class="col-md-1 text-right">:</div>
            		<div class="col-md-8"><?= $data['signers'][0]['location']; ?></div>
            	</div>
            </div>
        </div>
    </div>
</div>