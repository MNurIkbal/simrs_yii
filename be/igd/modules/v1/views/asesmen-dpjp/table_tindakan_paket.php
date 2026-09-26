<?php

use Doco\components\DocoHelpers;
?>
<table border="1">
	<thead>
		<tr>
			<th>No</th>
			<th>Tanggal Tindakan</th>
			<th>Nama Tindakan</th>
			<th>Dokter Pemeriksa</th>
			<th>Dokter Delegasi</th>
			<th>Perawat 1</th>
			<th>Perawat 2</th>
			<th>Qty</th>
			<th>Tarif Satuan</th>
			<th>Tarif Cyto</th>
			<th>Jumlah Tarif(Rp)</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$total_jumlah_tarif = 0;
			$rowNum = 1;
			foreach ($data_tindakan_bmhp as $key => $value) {
				if($value['tipe_instruksi'] == 'TINDAKAN' || $value['tipe_instruksi'] == 'PAKET'){
		?>
			<tr>
				<td><?=$rowNum?></td>
				<td><?= date('d F Y H:i:s', strtotime($value['tgl_instruksi']))?></td>
				<?php
					$detail_paket = '';
						// var_dump($value['daftar_paket']);exit;
					if($value['tipe_instruksi'] == 'PAKET' && isset($value['daftar_paket'])){
						$detail_paket_json = json_decode($value['daftar_paket'],TRUE);
						if(is_array($detail_paket_json)){
							$detail_paket = '<br><ul>';
							foreach ($detail_paket_json as $key_detail => $val_detail) {
								$detail_paket .= '<li>'.@$val_detail.'</li>';
							}
							$detail_paket .= '</ul>';
						}
					}
				?>
				<td><?=$value['tindakaninstruksi_nama'].$detail_paket?></td>
				<td><?=$value['dokterdpjp_tindakan']?></td>
				<td><?=$value['dokterdelegasi_tindakan']?></td>
				<td><?=$value['pegawaitindakan_1']?></td>
				<td><?=$value['pegawaitindakan_2']?></td>
				<td><?=$value['qty']?></td>
				<td><?=DocoHelpers::formatNumber($value['tarif_satuan_tindakan'])?></td>
				<td><?=DocoHelpers::formatNumber($value['tarif_cyto_tindakan'])?></td>
				<td><?=DocoHelpers::formatNumber($value['jumlah_tarif_tindakan'])?></td>
			</tr>
		<?php
					$total_jumlah_tarif += $value['jumlah_tarif_tindakan'];
					$rowNum++;
				}
			}
		?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="10">Total</td>
			<td><?=DocoHelpers::rupiahDisplay($total_jumlah_tarif)?></td>
		</tr>
	</tfoot>
</table>