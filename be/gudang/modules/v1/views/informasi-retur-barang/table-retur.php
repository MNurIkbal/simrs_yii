<table border="1" cellpadding="1" cellspacing="0" style="width:100%">
	<thead>
		<tr>
			<th>No</th>
			<th>No. Penerimaan</th>
			<th>No. Faktur</th>
			<th>Nama Barang</th>
			<th>Qty Terima</th>
			<th>Tanggal Kadaluarsa</th>
			<th>No. Batch</th>
			<th>Qty Retur</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$no = 1;
		foreach ($detail as $row):
		?>
			<tr>
				<td style="text-align: center;"><?= $no ?></td>
				<td><?= $row["no_penerimaan"] ?></td>
				<td><?= $row["no_faktur"] ?></td>
				<td><?= $row["barang_nama"] ?></td>
				<td><?= $row["qty_diterima"] ?> <?= $row["satuanunit_nama"] ?></td>
				<td><?= isset($row["tgl_kadaluarsa"]) ? date("d-M-Y", strtotime($row["tgl_kadaluarsa"])) : "-" ?></td>
				<td><?= $row["no_batch"] ?></td>
				<td style="text-align: right;"><?= $row["qty_input"] ?> <?= $row["satuanunit_nama"] ?></td>
			</tr>
		<?php
			$no++;
		endforeach ?>
	</tbody>
</table>