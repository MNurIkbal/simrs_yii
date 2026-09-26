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
		<td style="text-align: center;"><?=Yii::t('app', 'MASTER PERDA')?></td>
	</tr>
    <tr>
        <td style="text-align: center;"><?='Tanggal '.date("Y-m-d")?></td>
    </tr>
</table>
<br>
<table width="100%" class="tbl-bordered">
	<tr style="font-size: 13px">
        <th width="1">No</th>
        <th><?=\Yii::t("app", "Nomer Sk");?></th>
        <th><?=\Yii::t("app", "Nama Perda");?></th>
        <th><?=\Yii::t("app", "Nama Lainnya");?></th>
        <th><?=\Yii::t("app", "Tanggal Berlaku");?></th>
        <th><?=\Yii::t("app", "Detail");?></th>
        <th><?=\Yii::t("app", "Ditetapkan Oleh");?></th>
        <th><?=\Yii::t("app", "Status");?></th>
    </tr>
    <tbody style="font-size: 13px">
    	<?php
    	$no = 1;
    	foreach($data as $value):
    	?>
    	<tr>
    		<td><?=$no?></td>
    		<td><?=$value['perda_no']?></td>
    		<td><?=$value['perdanama_sk']?></td>
    		<td><?=$value['perdanama_sk']?></td>
        <td><?=$value['perda_tgl']?></td>
        <td><?=$value['perda_tentang']?></td>
        <td><?=$value['ditetapkan_oleh']?></td>
    		<td><?= $var_aktif = ($value['is_active'] = 0 ? "Tidak Aktif" : "Aktif"); ?></td>
    	</tr>
    	<?php
    	$no++;
    	endforeach;
    	?>
    </tbody>
</table>
