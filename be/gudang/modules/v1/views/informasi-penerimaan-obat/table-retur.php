<?php
use Doco\components\DocoHelpers;
?>

<style type="text/css">
.text-right {
	text-align: right;
}
</style>

<table border="1" cellpadding="4" cellspacing="0" style="width:100%">
	<thead>
		<tr>
			<th>No</th>
			<th>No. Penerimaan</th>
			<th>No. Faktur</th>
			<th>Nama Obat</th>
			<th>Qty Terima</th>
			<th>Tanggal Kadaluarsa</th>
			<th>No. Batch</th>
			<th>Sub Total (Rp.)</th>
			<th>Qty Retur</th>
			<th>Total Retur (Rp.)</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$no = 1;
		$total_retur = 0;
		foreach ($detail as $row):
			$subtotal = $row['harga'] * ($row['qty_besar'] - $row['qty_input']);
			$subtotal_retur = $row['harga'] * $row['qty_input'];
			$qty_retur = isset($row['qty_input']) ? $row['qty_input'] : 0;
			$total_retur = $total_retur + $subtotal_retur;
		?>
			<tr>
				<td style="text-align: center;"><?= $no ?></td>
				<td><?= $row["no_penerimaan"] ?></td>
				<td><?= $row["no_faktur"] ?></td>
				<td><?= $row["obatalkes_nama"] ?></td>
				<td><?= $row["qty_besar"] ?> <?= $row["satuanunit_nama"] ?></td>
				<td><?= date("d-M-Y", strtotime($row["tgl_kadaluarsa"])) ?></td>
				<td><?= $row["no_batch"] ?></td>
				<td class="text-right"><?= DocoHelpers::formatNumber($subtotal) ?></td>
				<td class="text-right"><?= $qty_retur ?> <?= $row["satuanunit_nama"] ?></td>
				<td class="text-right"><?= DocoHelpers::formatNumber($subtotal_retur) ?></td>
			</tr>
		<?php
			$no++;
		endforeach ?>
			<tr>
				<td colspan="9" class="text-right"><strong>Total Retur (Rp.)</strong></td>
				<td class="text-right"><?= DocoHelpers::formatNumber($total_retur) ?></td>
			</tr>
	</tbody>
</table>
