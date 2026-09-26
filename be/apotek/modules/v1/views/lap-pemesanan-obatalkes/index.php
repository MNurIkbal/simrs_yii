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
		<td style="text-align: center;"><?=Yii::t('app', 'Laporan Pemesanan Obat Alkes')?></td>		
	</tr>
	<tr>
		<td style="text-align: center;"><?= $ruangan_nama ?></td>
	</tr>
    <tr>
        <td style="text-align: center;"><?='Periode '.$filter?></td>
    </tr>
</table>
<br>
<table width="100%" class="tbl-bordered">
	<tr style="font-size: 13px">
        <th width="1">No</th>
        <th><?=\Yii::t("app", "Tanggal Pemesanan");?></th>
        <th><?=\Yii::t("app", "Nomor Pemesanan");?></th>
        <th><?=\Yii::t("app", "Instalasi Tujuan");?></th>
        <th><?=\Yii::t("app", "Ruangan Tujuan");?></th>
        <th><?=\Yii::t("app", "Nama Obat Alkes");?></th>
        <th><?=\Yii::t("app", "Qty Pemesanan");?></th>
        <th><?=\Yii::t("app", "Nama Satuan Besar");?></th>
        <th><?=\Yii::t("app", "Qty Pemesanan");?></th>
        <th><?=\Yii::t("app", "Nama Satuan Kecil");?></th>
    </tr>
    <tbody style="font-size: 13px">
    	<?php 
    	$no = 1;
    	$total = 0;
    	$no_pemesanan = '';
    	foreach($data as $value):  
        if($no_pemesanan == $value['nopemesanan']) : ?>
		<tr>
			<td>No Pemesanan : </td>
		</tr>
		<?php else : ?>
    	<tr>
    		<td><?=$no?></td>
            <td><?= date('d M Y', strtotime($value['tglpemesanan'])) ?></td>
            <td><?=$value['nopemesanan']?></td>
            <td><?=$value['instalasi_tujuan']?></td>
            <td><?=$value['ruangan_tujuan']?></td>
    		<td><?=$value['obatalkes_namalain']?></td>
    		<td style="text-align: right;"><?=$value['qty_besar']?></td>
    		<td><?=$value['satuan_besar']?></td>
    		<td style="text-align: right;"><?=$value['jumlah_pesan']?></td>
    		<td><?=$value['satuan_kecil']?></td>
    	</tr>
    	<?php 
    	$no++;
    	endif;
    	endforeach;
    	?>
    </tbody>  
</table>