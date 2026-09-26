<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\helpers\Url;
use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<div class="modal-body">
	<p><b>Detail No Invoice Gabungan</b></p>
	<table id="example-detail" class="table datatable-basic table-hover dataTable no-footer" style="width: 100%;">
		<thead>
			<tr class="bg-inverse">
				<th><?=Yii::t('fe', 'No'); ?></th>
				<th><?=Yii::t('fe', 'No Pendaftaran'); ?></th>
				<th><?=Yii::t('fe', 'No Invoice'); ?></th>
				<th><?=Yii::t('fe', 'Pasien / No RM'); ?></th>
				<th><?=Yii::t('fe', 'Penjamin'); ?></th>
				<th><?=Yii::t('fe', 'Tanggal Invoice'); ?></th>
				<th><?=Yii::t('fe', 'Nilai Invoice'); ?></th>
			</tr>    
		</thead>
		<tbody>
		</tbody>
	</table>
	<p class="mt-3"><b>Total Invoice Gabungan : <?= $total; ?></b></p>
</div>

<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>

<script type="text/javascript">
	$(document).ready(function(){
		var id = "<?= $id; ?>"

		$("#example-detail").docoTabel({
			filter: false,
			paging: false,
			info: false,
			columnDefs: [],
			select: {
				style: 'os',
				selector: 'tr'
			},
			sorting: [],
			processing: true,
			serverSide: true,
			scrollX: true,
			scrollY: false,
			ajax: {
				url: "/kasir/inf-gabung-invoice/detail-invoice-get-data?id="+id,
			},
			columns: [
				{
					data: null,
					searchable: false,
					orderable: false,
					render: (data, rowElement, rowData, rowAdditionalData) => {
					var tableInfo = table.page.info()
					return tableInfo.start + rowAdditionalData.row + 1
					}
				},
				{
					data: 'no_pendaftaran'
				},
				{
					data: 'no_pembayaran'
				},
				{
					data: 'pasien',
				},
				{
					data: 'penjamin_nama'
				},
				{
					data: 'tanggal_invoice'
				},
				{
					data: 'nilai_invoice',
					className: "text-right",
				}
			],
		});
	})
</script>
