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
		<td style="text-align: center;"><?=Yii::t('app', 'MASTER KOMPONEN')?></td>
	</tr>
    <tr>
        <td style="text-align: center;"><?='Tanggal '.date("Y-m-d")?></td>
    </tr>
</table>
<br>
<table width="100%" class="tbl-bordered">
	<tr style="font-size: 13px">
        <th width="1">No</th>
        <th><?=\Yii::t("app", "Kode Komponen");?></th>
        <th><?=\Yii::t("app", "Nama Komponen");?></th>
        <th><?=\Yii::t("app", "Nama Lainnya");?></th>
        <th><?=\Yii::t("app", "Presentasi Delegasi");?></th>
        <th><?=\Yii::t("app", "Status");?></th>
        <th><?=\Yii::t("app", "Catatan");?></th>
    </tr>
    <tbody style="font-size: 13px">
    	<?php
    	$no = 1;
    	foreach($data as $value):
    	?>
    	<tr>
    		<td><?=$no?></td>
    		<td><?=$value['komponentarif_kode']?></td>
    		<td><?=$value['komponentarif_nama']?></td>
    		<td><?=$value['komponentarif_namalainnya']?></td>
    		<td><?=$value['persen_delegasi']?></td>
    		<td><?= $var_aktif = ($value['is_active'] = 0 ? "Tidak Aktif" : "Aktif"); ?></td>
        <td><?=$value['catatan']?></td>
    	</tr>
    	<?php
    	$no++;
    	endforeach;
    	?>
    </tbody>
</table>
