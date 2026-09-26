<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-09 13:19:07
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-09 13:27:32
 */
?>
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
<table border="0" style="width: 50%">
	<?php 
	foreach ($filter as $key => $value) :
	?>
	<tr>
		<td>
			<?=$key?>
		</td>
		<td>
			<?=$value?>
		</td>
	</tr>
	<?php
	endforeach;
	?>
</table>

<br>
<table width="100%" class="tbl-bordered">
	<thead>
		<tr>
			<th width="1">No</th>
            <th><?= \Yii::t("app", "Tanggal retur"); ?></th>
            <th><?= \Yii::t("app", "Nomor retur"); ?></th>
            <th><?= \Yii::t("app", "Nama pasien"); ?></th>
            <th><?= \Yii::t("app", "Nomor resep"); ?></th>           
		</tr>
	</thead>
	<tbody>
		<?php 
		$no = 0;
		foreach($detail as $value):
			$no++;
		?>
		<tr>
			<td><?= $no ?></td>
			<td><?= $value['tgl_retur'] ?></td>	
			<td><?= $value['no_returresep'] ?></td>	
			<td><?= $value['nama_pasien'] ?></td>	
			<td><?= $value['noresep'] ?></td>			
		</tr>
		<?php 
		endforeach;
		?>
	</tbody>
</table>