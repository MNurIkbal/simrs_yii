<table border="1" cellpadding="1" cellspacing="0" style="width:100%">
	<thead>
		<tr>
			<th>No</th>
			<th>Tanggal Retur</th>
			<th>No. Retur</th>
			<th>No. Penerimaan</th>
			<th>No. Faktur</th>
			<th>Supplier</th>
			<th>Nama Barang</th>
			<th>Qty</th>
			<th>Alasan</th>
		</tr>
	</thead>
	<tbody>
		<?php
		$no = 1;
		foreach ($query as $row):
		?>
			<tr>
				<td style="text-align: center;"><?= $no ?></td>
				<td><?= $row["tgl_retur"] ?></td>
				<td><?= $row["no_returpenerimaanbarang"] ?></td>
				<td><?= $row["no_penerimaan"] ?></td>
				<td><?= $row["no_faktur"] ?></td>
				<td><?= $row["supplier_nama"] ?></td>
				<td><?= $row["barang_nama"] ?></td>
				<td><?= $row["qty_input"] ?> <?= $row["satuanunit_nama"] ?></td>
				<td><?= $row["alasan_retur"] ?></td>
			</tr>
		<?php
			$no++;
		endforeach ?>
	</tbody>
</table>