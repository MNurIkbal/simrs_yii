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
		<td style="text-align: center;"><?=Yii::t('app', 'Laporan Mutasi Obat Alkes')?></td>		
	</tr>
	<tr>
		<td style="text-align: center;"><?= Yii::t('app', 'Apotek Farmasi')?></td>
	</tr>
    <tr>
        <td style="text-align: center;"><?='Periode '.$filter?></td>
    </tr>
</table>
<br>
<table width="100%" class="tbl-bordered">
	<tr style="font-size: 13px">
        <th width="1">No</th>
        <th><?=\Yii::t("app", "Nomor Mutasi");?></th>
        <th><?=\Yii::t("app", "Tanggal Mutasi");?></th>
        <th><?=\Yii::t("app", "Nama Obat Alkes");?></th>
        <th><?=\Yii::t("app", "Qty Mutasi");?></th>
        <th><?=\Yii::t("app", "Satuan Besar");?></th>
        <th><?=\Yii::t("app", "Qty Mutasi");?></th>
        <th><?=\Yii::t("app", "Satuan Kecil");?></th>
    </tr>
    <tbody style="font-size: 13px">
    	<?php 
    	$no = 1;
    	$total = 0;
    	$no_mutasi = '';
    	foreach($data as $value):  
        if($no_mutasi == $value['nomutasioa']) : ?>
		<tr>
			<td>No Mutasi : </td>
		</tr>
		<?php else : ?>
    	<tr>
    		<td><?=$no?></td>
            <td><?=$value['nomutasioa']?></td>
            <td><?= date('d M Y', strtotime($value['tglmutasioa'])) ?></td>
    		<td><?=$value['obatalkes_namalain']?></td>
    		<td><?=$value['qty_satuan_besar']?></td>
    		<td><?=$value['satuanbesar_nama']?></td>
    		<td><?=$value['jumlah_mutasi']?></td>
    		<td><?=$value['satuankecil_nama']?></td>
    	</tr>
    	<?php 
    	$no++;
    	endif;
    	endforeach;
    	?>
    </tbody>  
</table>