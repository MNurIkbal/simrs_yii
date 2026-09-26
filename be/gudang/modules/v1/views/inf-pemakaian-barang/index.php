<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-04-06 14:43:15
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2018-04-06 15:40:52
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
<table style="width: 100%">
	<tr>
		<td style="text-align: center;"><strong><?=Yii::t('app', 'Pemakaian Barang')?></strong></td>		
	</tr>
	<tr>
		<td style="text-align: center;"><strong><?= Yii::t('app', 'Ruangan '.$detail['ruangan_nama'])?></strong></td>
	</tr>
</table>
<br>
<table width="100%" cellpadding="10" class="tabel">
	<tbody>
		<tr>
			<td class="bold"><?= Yii::t('app', 'Nomor Pemakaian') ?></td>
			<td class="header_noResep">: <?=!empty($detail['no_pemakaianbarang']) ? $detail['no_pemakaianbarang'] : '-' ?></td>			
			<td width="10%"></td>
			<td class="bold"><?= Yii::t('app', 'Tanggal Pemakaian') ?></td>
			<td class="header_noResep">: <?=!empty($detail['tgl_pemakaianbarang']) ? $detail['tgl_pemakaianbarang'] : '-' ?></td>			
		</tr>
	</tbody>
</table>
<br>
<table width="100%" class="tbl-bordered">
	<thead>
		<tr>
			<th width="1">No</th>
            <th><?= \Yii::t("app", "Nama Barang"); ?></th>
            <th><?= \Yii::t("app", "Qty"); ?></th>
            <th><?= \Yii::t("app", "Satuan"); ?></th>
            <th><?= \Yii::t("app", "Keterangan"); ?></th>            
		</tr>
	</thead>
	<tbody>
		<?php 
		$no = 0;
		foreach($data_barang as $value):
			$no++;
		?>
		<tr>
			<td><?= $no ?></td>
			<td><?= $value['barang_nama'] ?></td>	
			<td style="text-align: right"><?= $value['qty_kecil'] ?></td>	
			<td><?= $value['satuan_kecil'] ?></td>				
			<td><?= $value['catatan_barang'] ?></td>	
		</tr>
		<?php 
		endforeach;
		?>
	</tbody>
</table>