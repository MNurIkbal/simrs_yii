
<?php
use yii\helpers\Html;
use kartik\widgets\ActiveForm;
?>

<?php $form = ActiveForm::begin([
    'id' => 'bpjs-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
]); ?>

<center><h3><span class="font-design" id="bpjsnew_detail_nama"><?= $nama_peserta; ?></span> </h3></center>

<div class="row">
	<div class="form-group">
		<label class="col-form-label col-sm-3">No Kartu</label>
		<div class="col-sm-6">
			<span id="bpjsnew_detail_no_kartu"><?= $no_kartu; ?></span>
		</div>
	</div>
</div>

<div class="row">
	<div class="form-group">
		<label class="col-form-label col-sm-3">Jenis Pelayanan</label>
		<div class="col-sm-6">
			<span id="bpjsnew_detail_jenis_pelayanan"><?= $jenis_pelayanan; ?></span>
		</div>
	</div>
</div>

<div class="row">
	<div class="form-group">
		<label class="col-form-label col-sm-3">Poli</label>
		<div class="col-sm-6">
			<span id="bpjsnew_detail_poli"><?= $poli_tujuan; ?></span>
		</div>
	</div>
</div>

<div class="row">
	<div class="form-group">
		<label class="col-form-label col-sm-3">Tanggal Lahir</label>
		<div class="col-sm-6">
			<span id="bpjsnew_detail_tglLahir"><?= $tgl_lahir; ?></span>
		</div>
	</div>
</div>

<div class="row">
	<div class="form-group">
		<label class="col-form-label col-sm-3">Jenis Peserta</label>
		<div class="col-sm-6">
			<span id="bpjsnew_detail_jnsPeserta"><?= $nmjenispeserta; ?></span>
		</div>
	</div>
</div>

<div class="row">
	<div class="form-group">
		<label class="col-form-label col-sm-3">Kelas Rawat</label>
		<div class="col-sm-6">
			<span id="bpjsnew_detail_hakKelas"><?= $kelas_rawat; ?></span>
		</div>
	</div>
</div>
<?php ActiveForm::end(); ?>