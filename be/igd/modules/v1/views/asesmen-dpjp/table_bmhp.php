<?php

use Doco\components\DocoHelpers;
?>
<table border="1">
	<thead>
		<tr>
			<th>No</th>
			<th>Tanggal Tindakan</th>
			<th>Nama Tindakan</th>
			<th>Obat/Alkes</th>
			<th>Perawat 1</th>
			<th>Perawat 2</th>
			<th>Qty</th>
			<th>Ditagihkan</th>
			<th>Jumlah Tarif</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$total_jumlah_tarif = 0;
			$rowNum = 1;
			foreach ($data_tindakan_bmhp as $key => $value) {
				if($value['tipe_instruksi'] == 'BMHP'){
		?>
			<tr>
				<td><?=$rowNum?></td>
				<td><?= date('d F Y', strtotime($value['tgl_instruksi']))?></td>
				<?php
					$bmhp_tindakan = '-';
					if(!empty($value['bmhp_tindakandetail'])){
						$bmhp_tindakan_json = json_decode($value['bmhp_tindakandetail'],TRUE);
						$bmhp_tindakan = isset($bmhp_tindakan_json['daftartindakan_nama']) ? $bmhp_tindakan_json['daftartindakan_nama'] : '-';
					}
				?>
				<td><?= $bmhp_tindakan?></td>
				<td><?=$value['tindakaninstruksi_nama']?></td>
				<td><?=$value['pegawaibmhp_1']?></td>
				<td><?=$value['pegawaibmhp_2']?></td>
				<td><?=$value['qty']?></td>
				<td><?= $value['is_ditagihkan_bmhp'] != null && $value['is_ditagihkan_bmhp'] === true? 'Ya' : 'Tidak' ?></td>
				<td><?=DocoHelpers::formatNumber($value['jumlah_tarif_bmhp'])?></td>
			</tr>
		<?php
					$total_jumlah_tarif += $value['jumlah_tarif_bmhp'];
					$rowNum++;
				}
			}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="8">Total</td>
			<td><?=DocoHelpers::rupiahDisplay($total_jumlah_tarif)?></td>
		</tr>
	</tfoot>
</table>