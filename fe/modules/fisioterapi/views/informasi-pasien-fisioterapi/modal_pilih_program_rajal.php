<?php
use yii\web\View;
use app\components\DocoHelpers;
?>

<!-- Modal Header -->
<div class="modal-header bg-inverse">
    <button type="button" class="close" id="btn-close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Pilih Program</h5>
</div>

<!-- Modal Body -->
<div class="modal-body">
    <div class="row">
		<div class="col-md-12">
			<?= DocoHelpers::generateToolbar([
				'periksa' => [
					'title' => 'Periksa',
					'icon' => 'fa fa-stethoscope',
					'attributes' => [
						'data-target' => '/fisioterapi/pemeriksaan?pendaftaran_id=',
						'id' => 'btn-periksa-modal',
						'data-options' => 'click',
						'disabled' => true
					]
				]
				], 'table_pilih_program_rajal') ?>
			<table id="table_pilih_program_rajal" class="table table-striped table-condensed table-hover" style="width: 100%;">
				<thead>
					<tr class="bg-inverse">
						<th></th>
						<th>No</th>
						<th>Tanggal Rujukan</th> 
						<th>Jenis Terapi</th> 
						<th>Nama Terapi</th> 
						<th>Dokter Perujuk</th> 
                        <th>Frekuensi</th> 
						<th>Realisasi</th> 
						<th>Sisa Terapi</th> 
						<th>Tidak Hadir</th> 
						<th>Bayar</th> 
						<th>Status</th> 
					</tr>
				</thead>
				<tbody>
					<tr>
						<td class="text-center" colspan="10">Data tidak ditemukan</td>
					</tr>
				</tbody>
			</table>
		</div>
    </div>
</div>

<!-- Javascript -->
<?php
$jsVar = [
	'pasien_id' => $pasien_id,
	'pendaftaran_id' => $pendaftaran_id,
	'jenis_pelayanan' => $jenis_pelayanan
];
$this->registerJsVar("jsVar", $jsVar);
$this->registerJs($this->render("js/modal_pilih_program_rajal.js"), View::POS_END, "js"); 
?>