<?php

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>

<div class="modal-body">
    <table id="example-detail" class="table datatable-basic table-hover dataTable no-footer" style="width: 100%;">
		<thead>
			<tr class="bg-inverse">
				<th><?=Yii::t('fe', 'Tanggal Permintaan'); ?></th>
				<th><?=Yii::t('fe', 'Estimasi Jam Mulai'); ?></th>
				<th><?=Yii::t('fe', 'Estimasi Jam Selesai'); ?></th>
				<th><?=Yii::t('fe', 'Ruangan - No. Kamar Bedah'); ?></th>
				<th><?=Yii::t('fe', 'Status'); ?></th>
				<th><?=Yii::t('fe', 'Keterangan'); ?></th>
				<th><?=Yii::t('fe', 'Tanggal'); ?></th>
                <th><?=Yii::t('fe', 'Oleh'); ?></th>
			</tr>    
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> ". Yii::t('fe', 'Kembali'),[
        'class' => 'btn bg-slate btn-sm',
        'data-dismiss' => 'modal'
    ]) ?>
</div>

<script type="text/javascript">
    $(document).ready(function(){
		var pasienkirimkeunitlain_id = "<?= $pasienkirimkeunitlain_id; ?>"

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
				url: "/bedah/jadwal-operasi/riwayat-operasi-get-data?pasienkirimkeunitlain_id="+pasienkirimkeunitlain_id,
			},
			columns: [
				{
					data: 'tgl_permintaan',
					render: (data) => {
						return data == "" || data == null ? "-" : moment(data).format("DD MMMM YYYY")
					}
				},
				{
					data: 'jam_rencana_mulai'
				},
				{
					data: 'jam_rencana_selesai'
				},
				{
					data: 'ruangan_nama',
					render: (data, display, row) => {
						const ruangan_nama = data == "" || data == null ? "" : data
						const kamarruangan_nokamar = row.kamarruangan_nokamar == "" || row.kamarruangan_nokamar == null ? "" : row.kamarruangan_nokamar

						return ruangan_nama+' - '+kamarruangan_nokamar
					}
				},
				{
					data: 'status_operasi'
				},
				{
					data: 'keterangan'
				},
				{
					data: 'tgl_perubahan',
					render: (data) => {
						return data == "" || data == null ? "-" : moment(data).format("DD MMMM YYYY HH:mm:ss")
					}
				},
				{
					data: 'nama_pegawai'
				},
			],
		});
	})
</script>