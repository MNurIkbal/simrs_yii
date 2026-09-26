<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }

    .tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }
</style>
<table border="1" width="100%" class="tbl-bordered">
	<thead>
		<tr>
			<th>No</th>
			<th>Jenis Diet</th>
			<th>Menu</th>
			<th>Waktu Diet</th>
			<th>Keterangan</th>
			<th>Jumlah</th>
		</tr>
	</thead>
	<tbody>
		<?php
			$no = 1; 
			foreach($data_riwayat as $v_riwayat){?>
		<tr>
			<td><?=$no?></td>
			<td><?=@$v_riwayat['jenisdiet_nama']?></td>
			<td><?=@$v_riwayat['makanandiet_nama']?></td>
			<td><?=@$v_riwayat['waktu']?></td>
			<td><?=@$v_riwayat['keterangan']?></td>
			<td><?=@$v_riwayat['jumlah']?></td>
		</tr>
		<?php 
			$no++;
			}?>
	</tbody>
</table>