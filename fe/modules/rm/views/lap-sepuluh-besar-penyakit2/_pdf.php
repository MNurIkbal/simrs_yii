<?php

use app\components\DocoHelpers;

?>
<div id="header">
    <div id="logo_cetak"></div>
    <div id="title">
        <div><?= Yii::$app->docoVars->identity("nama_rumahsakit"); ?></div>
        <div>BANDUNG - JAWA BARAT</div>
        <div style="font-size:11px;">Jl. Sukahaji No. 42, Bandung, Jawa Barat, 40152, Indonesia</div>
        <div style="font-size:11px;">+62 22 8324024</div>
    </div>
</div>
<br><br>
<table width="100%" border="1" cellpadding="5">
	<thead>
		<th>
			<tr>
	            <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
	            <th><?=\Yii::t("fe", "Kode Diagnosa");?></th>
                <th><?=\Yii::t("fe", "Nama Diagnosa");?></th>
                <th><?=\Yii::t("fe", "Klasifikasi Diagnosa");?></th>
                <th><?=\Yii::t("fe", "Jumlah Kasus");?></th>
	        </tr>
		</th>
	</thead>
	<tbody>
        <?php $no = 0; foreach ($body['response']['data'] as $key => $value) : $no++; ?>
		<tr>
			<td><?= $no ?></td>
			<td><?= DocoHelpers::display_label($value['diagnosa_kode']) ?></td>
			<td><?= DocoHelpers::display_label($value['diagnosa_nama']) ?></td>
			<td><?= DocoHelpers::display_label($value['klasifikasidiagnosa_nama']) ?></td>
			<td><?= DocoHelpers::display_label($value['total']) ?></td>
		</tr>
        <?php endforeach; ?>
    </tbody>
</table>
