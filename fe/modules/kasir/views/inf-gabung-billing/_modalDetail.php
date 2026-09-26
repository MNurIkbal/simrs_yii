<?php
/**
 * @author Budi
 * Powered by Sirs
 */

use yii\helpers\Html;
use app\components\DocoHelpers;
?>

<div class="modal-header bg-inverse">
	<button type="button" class="close" data-dismiss="modal">&times;</button>
	<h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
	<div class="col-md-12">
		<div class="panel panel-default panel-bordered">
			<div class="panel-heading">
				<h6 class="panel-title"><?= $title ?></h6>
			</div>
			<div class="panel-toolbar clearfix">
			<div class="panel-body">
				<div class="row">
					<div class="col-md-12">
                  <table class="table datatable-basic table-hover dataTable no-footer" id="table-detail" style="width: 100%;">
							<thead>
								<tr class="bg-inverse">
									<th><?=Yii::t('fe', 'No'); ?></th>
									<th><?=Yii::t('fe', 'No Pendaftaran'); ?></th>
									<th><?=Yii::t('fe', 'Tanggal Pendaftaran'); ?></th>
									<th><?=Yii::t('fe', 'Tanggal Transaksi'); ?></th>
									<th><?=Yii::t('fe', 'Instalasi - Ruangan'); ?></th>
									<th><?=Yii::t('fe', 'Tindakan/Obat'); ?></th>
									<th><?=Yii::t('fe', 'Qty'); ?></th>
									<th><?=Yii::t('fe', 'Harga'); ?></th>
									<th>Cito</th>
									<th>Diskon</th>
									<th><?=Yii::t('fe', 'Sub Total'); ?></th>
									<th><?=Yii::t('fe', 'Penjamin'); ?></th>
									<th><?=Yii::t('fe', 'Dijamin'); ?></th>
									<th><?=Yii::t('fe', 'Dibayar Pasien'); ?></th>
								</tr>    
							</thead>
							<tbody>
								<tr>
									<td class="text-center" colspan="13"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
								</tr>
							</tbody>
						</table>
						<hr>
						<table width="30%" border="0">
							<tr>
								<td><b>TOTAL DIJAMIN</b></td>
								<td><b>:</b></td>
								<td><b><span class="total_dijamin"></span></b></td>
							</tr>
							<tr>
								<td><b>TOTAL DITAGIHKAN KE PASIEN</b></td>
								<td><b>:</b></td>
								<td><b><span class="total_dibayar_pasien"></span></b></td>
							</tr>
							<tr>
								<td><b>TOTAL TAGIHAN GABUNGAN</b></td>
								<td><b>:</b></td>
								<td><b><span class="total_tagihan"></span></b></td>
							</tr>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="modal-footer">
	<?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
		'class' => 'btn bg-slate btn-sm',
		'data-dismiss' => 'modal'
	]) ?>
</div>

<script type="text/javascript">
$(document).ready(function(){
	var tableDetail;
	var _id = "<?= $id ?>";
	var _pembayaran_id = "<?= $pembayaran_id ?>";
	var _subTotal = '<?= $sub_total ?>';
	var _tarifDijamin = '<?= $tarif_dijamin ?>';
	var _tarifDibayarkan = '<?= $tarif_dibayarkan ?>';
	var _transId = _pembayaran_id ? _pembayaran_id : _id;
	var _transType = _pembayaran_id ? 'pembayaran_id' : 'pendaftaran_id';

	$('#btn-submit').prop('disabled', true);
	tableDetail = $("#table-detail").docoTabel({
		filter: false,
		sorting: [[2, "desc"]],
		processing: true,
		serverSide: true,
		scrollX: true,
		scrollCollapse: true,
		ajax: baseUrl + "kasir/inf-gabung-billing/data-detail-gabung?" + _transType + '=' + _transId,
		columns: [
			{
				data: null,
				searchable: false,
				orderable: false,
				render: (data, rowElement, rowData, rowAdditionalData) => {
					var tableInfo = tableDetail.page.info()
					return tableInfo.start + rowAdditionalData.row + 1
				}
			},
			{
				data: "no_pendaftaran", 
				searchable: false,
				orderable: false,
			},
			{
				data: "tgl_pendaftaran", 
				searchable: false,
				orderable: false,
			},
			{
				data: "tgl_pelayanan", 
				searchable: false,
				orderable: false,
			},
			{
				data: "instalasi", 
				searchable: false,
				orderable: false,
			},
			{
				data: "tindakan", 
				searchable: false,
				orderable: false,
			},
			{
				data: "qty", 
				searchable: false,
				orderable: false,
				className: "text-right",
			},
			{
				data: "tarif_satuan",
				searchable: false,
				orderable: false,
				className: "text-right",
			},
			{
				data: "tarif_cyto",
				searchable: false,
				orderable: false,
				className: "text-right",
			},
			{
				data: "discount",
				searchable: false,
				orderable: false,
				className: "text-right",
			},
			{
				data: "sub_total",
				searchable: false,
				orderable: false,
				className: "text-right",
			},
			{
				data: "penjamin", 
				searchable: false,
				orderable: false,
			},
			{
				data: "dijamin",
				searchable: false,
				orderable: false,
				className: "text-right",
			},
			{
				data: "dibayar_pasien",
				searchable: false,
				orderable: false,
				className: "text-right",
			},
		],
	});
	$(".total_dijamin").html("Rp. " + docoHelper.convertToRupiah(_tarifDijamin));
	$(".total_dibayar_pasien").html("Rp. " + docoHelper.convertToRupiah(_tarifDibayarkan));
	$(".total_tagihan").html("Rp. " + docoHelper.convertToRupiah(_subTotal));
})
</script>
