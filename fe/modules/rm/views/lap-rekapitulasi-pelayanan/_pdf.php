<?php

use app\components\DocoHelpers;
$this->registerCss('
#global_print{width:14cm;letter-spacing: 2px}
#header{ height:100px; width:100%;}
#logo_cetak{float:left; height:100px; width:100px;}
#title{float:left; width:400px;}
#kepada{float:right; width:350px;}
#kepada .field{float:left; width:25%;}
#kepada .value{float:left; width:70%;}
#kuitansi{text-align:center; font-size:13px; font-weight:bold;}
#no_kuitansi{text-align:left; font-size:12px; font-weight:bold;}
table#table_list{width:100%; font-size:12px; border-collapse:0; border-spacing:0px;letter-spacing: 2px}
tr th{border-bottom:0px solid #000; border-top:0px solid #000;}
#footer{width:100%; font-size:12px;}
#last_line{font-size:11px; font-style:inherit; width:100%;}
</style>
<style type="text/css" media="print">
#global_print{width:14cm;letter-spacing: 2px}
#header{ height:100px; width:100%;}
#logo_cetak{float:left; height:80px; width:80px;}
#title{float:left; width:100%;}
#kepada{float:left; width:10px;}
#kepada .field{float:left; width:25%;}
#kepada .value{float:left; width:70%;}
#kuitansi{text-align:center; font-size:14px; font-weight:bold;}
#no_kuitansi{text-align:left; font-size:12px; font-weight:bold;}
table#table_list{width:100%; font-size:12px; border-collapse:0; border-spacing:0px;letter-spacing: 2px}
tr th{border-bottom:0px solid #000; border-top:0px solid #000;}
#footer{width:100%; font-size:12px;}
#last_line{font-size:11px; font-style:inherit; width:100%;}
');

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
<table id="table_list" width="100%" border="1" class="table" cellpadding="5">
	<thead>
		<th>
			<tr style="height:20px;">
	            <th width="1"><?=\Yii::t("fe", "Rownum");?></th>
	            <th><?=\Yii::t("fe", "Tanggal pelaporan");?></th>
	            <th><?=\Yii::t("fe", "Instalasi");?></th>
	            <th><?=\Yii::t("fe", "Ruangan");?></th>
	            <th><?=\Yii::t("fe", "Nama dokter");?></th>
	            <th><?=\Yii::t("fe", "Pasien lama");?></th>
	            <th><?=\Yii::t("fe", "Pasien baru");?></th>
	            <th><?=\Yii::t("fe", "Jumlah");?></th>
	        </tr>
		</th>
	</thead>
	<tbody style="height:200px;">
        <?php $no = 0; foreach ($body['response']['data'] as $key => $value) : 
        $no++;
        $get_pasien_lama = Yii::$app->docoRest->rm->get($controllerService.'get-total', ['query' => [
            'total' => $value['total'],
            'status_pasien' => $value['status_pasien'],
            'type' => 'pasien_lama'
        ]]);

        $total_pasien_lama = json_decode($get_pasien_lama->getBody(), True);
        $get_pasien_baru = Yii::$app->docoRest->rm->get($controllerService.'get-total', ['query' => [
            'total' => $value['total'],
            'status_pasien' => $value['status_pasien'],
            'type' => 'pasien_baru'
        ]]);

        $total_pasien_baru = json_decode($get_pasien_baru->getBody(), True);
        $subtotal = $total_pasien_lama['response'] + $total_pasien_baru['response'];

        ?>
		<tr>
			<td><?= $no ?></td>
			<td>
				<?= DocoHelpers::display_label($value['tgl_pendaftaran'], true, date('d M Y', strtotime($value['tgl_pendaftaran']))) ?>
			</td>
			<td><?= DocoHelpers::display_label($value['instalasi_nama']) ?></td>
			<td><?= DocoHelpers::display_label($value['ruangan_nama']) ?></td>
			<td><?= DocoHelpers::display_label($value['nama_dokter']) ?></td>
			<td><?= isset($total_pasien_lama['response']) ? $total_pasien_lama['response'] : 0 ?></td>
			<td><?= isset($total_pasien_baru['response']) ? $total_pasien_baru['response'] : 0 ?></td>
			<td><?= $subtotal ?></td>
		</tr>
        <?php endforeach; ?>
    </tbody>
</table>
