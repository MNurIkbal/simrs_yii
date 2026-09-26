<?php
//Author: Ardi Pratama

// Using
use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;
use kartik\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>
<div class="row body">
	<div class="col-md-12">
		<div>
			<div class="panel panel-white">
				<div class="panel-toolbar clearfix">
					<?= DocoHelpers::generateToolbar([
						'edit-instruksi' => [
							'type' => 'button',
							'title' => Yii::t('fe', 'Edit Instruksi'),
							'icon' => 'fa fa-pencil',
							'attributes' => [
								'id' => 'btn-edit-instruksi',
								'data-options' => 'click',
								/*
								Untuk sementara disable, karena fitur edit tindakan masih menggunakan form lama dan belum stabil
								'disabled' => ($disableByPulang || $disableByAkomodasi) ? true : false
								*/
								'disabled' => true
							],
						],
						'hapus-instruksi' => [
							'type' => 'button',
							'title' => Yii::t('fe', 'Hapus Instruksi'),
							'icon' => 'fa fa-trash',
							'attributes' => [
								'id' => 'btn-hapus-instruksi',
								'data-options' => 'click',
								'disabled' => ($disableByPulang || $disableByAkomodasi) ? true : false,
                                'class' => 'hide',
							],
						],
						'implementasi' => [
							'type' => 'button',
							'title' => Yii::t('fe', 'Implementasi'),
							'icon' => 'fa fa-plus',
							'attributes' => [
								'id' => 'btn-implementasi',
								'data-options' => 'click',
								'disabled' => ($disableByPulang || $disableByConfig) ? true : false
							],
						],
						'print-implementasi' => [
							'type' => 'link',
							'title' => Yii::t('fe', 'Cetak'),
							'icon' => 'fa fa-print',
							'attributes' => [
								'id' => 'btn-cetak-implementasi',
								'url' => $linkcetak,
								'data-options' => 'link',
								'data-target' => $linkcetak,
								'target' => '_blank'
							]
						],
					], '#tb-implementasi'); ?>
				</div>
				<div class="panel-header"></div>
				<div class="panel-body">
					<?php if ($disableByConfig) : ?>
						<div class="mt-10">
							<div class="alert alert-danger" role="alert"><strong>Tagihan pasien sudah dibayarkan. Jika ingin melakukan implementasi tindakan, harap lakukan pembatalan tagihan yang sudah dibayarkan dikasir.</strong></div>
						</div>
					<?php endif; ?>
					<table class="table table-bordered datatable-basic dataTable" id="tb-implementasi" style="width:100%">
						<thead>
							<tr class="bg-inverse">
								<th colspan="7" class="text-center"><?= Yii::t('fe', 'Instruksi') ?></th>
							</tr>
							<tr class="bg-inverse">
								<th></th>
								<th>No</th>
								<th><?= Yii::t('fe', 'Detail Implementasi') ?></th>
								<th><?= Yii::t('fe', 'Instruksi Dokter') ?></th>
								<th><?= Yii::t('fe', 'Dokter') ?></th>
								<th><?= Yii::t('fe', 'Status') ?></th>
							</tr>
						</thead>
						<tbody></tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
</div>

<?php

// Script
$this->registerJs('
	// Global vars
	var tanggalPukul = "' . (\Yii::t("fe", "Tanggal/Pukul")) . '";
	var instruksiDokter = "' . (\Yii::t("fe", "Instruksi Dokter")) . '";
	var dokter = "' . (\Yii::t("fe", "Dokter")) . '";
	var status = "' . (\Yii::t("fe", "Status")) . '";
	var implementasi = "' . (\Yii::t("fe", "Implementasi")) . '";
	var petugas1 = "' . (\Yii::t("fe", "Petugas 1")) . '";
	var petugas2 = "' . (\Yii::t("fe", "Petugas 2")) . '";

	// Datatable language
	var emptyTable = "' . (\Yii::t("fe", "Tidak ada data yang tersedia")) . '";
	var info = "' . (\Yii::t("fe", "Menampilkan _START_ sampai _END_ dari _TOTAL_ data")) . '";
	var infoEmpty = "' . (\Yii::t("fe", "Menampilkan 0 sampai 0 dari 0 data")) . '";
	var infoFiltered = "' . (\Yii::t("fe", "(disaring dari _MAX_ total data)")) . '";
	var lengthMenu = "' . (\Yii::t("fe", "Menampilkan _MENU_ data")) . '";
	var loadingRecords = "' . (\Yii::t("fe", "Memuat...")) . '";
	var processing = "' . (\Yii::t("fe", "Memproses...")) . '";
	var search = "' . (\Yii::t("fe", "Cari:")) . '";
	var zeroRecords = "' . (\Yii::t("fe", "Tidak ada data yang ditemukan")) . '";
	var sortAscending = "' . (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terkecil ke yang terbesar")) . '";
	var sortDescending = "' . (\Yii::t("fe", ": aktifkan untuk mengurutkan kolom dari yang terbesar ke yang terkecil")) . '";
', View::POS_END, 'index');

// File
$this->registerJs($this->render('js/index.js'), View::POS_END);
?>
