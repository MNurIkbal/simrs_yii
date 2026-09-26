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
	            <th><?=\Yii::t("fe", "Tanggal Pendaftaran");?></th>
                <th><?=\Yii::t("fe", "No Pendaftaran");?></th>
                <th><?=\Yii::t("fe", "Nomor Rekam Medik");?></th>
                <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                <th><?=\Yii::t("fe", "Cara Bayar");?></th>
                <th><?=\Yii::t("fe", "Penjamin");?></th>
                <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                <th><?=\Yii::t("fe", "Instalasi");?></th>
                <th><?=\Yii::t("fe", "Ruangan");?></th>
                <th><?=\Yii::t("fe", "Dokter Penanggung Jawab");?></th>
	        </tr>
		</th>
	</thead>
	<tbody style="height:200px;">
        <?php $no = 0; foreach ($body['response']['data'] as $key => $value) : $no++; ?>
		<tr>
			<td><?= $no ?></td>
			<td>
				<?= DocoHelpers::display_label($value['tgl_pendaftaran'], true, date('d M Y', strtotime($value['tgl_pendaftaran']))) ?>
			</td>
			<td><?= DocoHelpers::display_label($value['no_pendaftaran']) ?></td>
			<td><?= DocoHelpers::display_label($value['no_rekam_medik']) ?></td>
			<td><?= DocoHelpers::display_label($value['nama_pasien']) ?></td>
			<td><?= DocoHelpers::display_label($value['jenis_kelamin']) ?></td>
			<td><?= DocoHelpers::display_label($value['carabayar_nama']) ?></td>
			<td><?= DocoHelpers::display_label($value['penjamin_nama']) ?></td>
			<td><?= DocoHelpers::display_label($value['jeniskasuspenyakit_nama']) ?></td>
			<td><?= DocoHelpers::display_label($value['instalasi_nama']) ?></td>
			<td><?= DocoHelpers::display_label($value['ruangan_nama']) ?></td>
			<td><?= DocoHelpers::display_label($value['nama_pegawai']) ?></td>
		</tr>
        <?php endforeach; ?>
    </tbody>
</table>
