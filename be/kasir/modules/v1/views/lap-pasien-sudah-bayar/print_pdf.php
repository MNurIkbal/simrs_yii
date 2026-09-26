<?php 
use yii\helpers\ArrayHelper;
use Doco\components\DocoHelpers;
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
<table width="100%" class="tbl-bordered">
  	<thead  style="font-size: 13px">
		<tr class="bg-inverse">
			<th width="1">No</th>
			<th><?= Yii::t('app', 'Tanggal Pembayaran') ?></th>
			<th><?= Yii::t('app', 'Tanggal Masuk - Pulang') ?></th>
			<th><?= Yii::t('app', 'Instalasi - Ruangan Akhir') ?></th>
			<th><?= Yii::t('app', 'No Pendaftaran') ?></th>
			<th><?= Yii::t('app', 'Nama Pasien') ?></th>
			<th><?= Yii::t('app', 'Nomor Rekam Medik') ?></th>
			<th><?= Yii::t('app', 'Cara Bayar') ?></th>
			<th><?= Yii::t('app', 'Penjamin') ?></th>
			<th><?= Yii::t('app', 'Jumlah Tagihan') ?></th>
			<th><?= Yii::t('app', 'Diskon') ?></th>
			<th><?= Yii::t('app', 'Jumlah Dibayar Penjamin') ?></th>
			<th><?= Yii::t('app', 'Jumlah Dibayar Pasien') ?></th>
		</tr>                                
   </thead>
	<tbody style="font-size: 13px">
	<?php 
	$no = 1;
	$subTotalTagihan = $subTotalDijamin = $subTotalDiskon = $subTotalDibayar = 0;
	foreach($data as $value) :
		$biayaAdministrasi = ArrayHelper::getValue($value, 'biaya_administrasi', 0);
		$totalTagihan = ArrayHelper::getValue($value, 'total_tagihan', 0);
		$totalTagihan = $totalTagihan + $biayaAdministrasi;
		$totalDijamin = ArrayHelper::getValue($value, 'subsidi_asuransi', 0);
		$totalDiskon = ArrayHelper::getValue($value, 'total_discountpembayaran', 0);
		$totalDibayar = $totalTagihan - $totalDijamin - $totalDiskon;

		$instalasiNama = ArrayHelper::getValue($value, 'instalasi_nama');
		$ruanganNama = ArrayHelper::getValue($value, 'ruangan_nama');
		$namaPasien = ArrayHelper::getValue($value, 'nama_pasien');
		$noRekamMedik = ArrayHelper::getValue($value, 'no_rekam_medik');
		$caraBayarNama = ArrayHelper::getValue($value, 'carabayar_nama');
		$penjaminNama = ArrayHelper::getValue($value, 'penjamin_nama');
		$tglPembayaran = ArrayHelper::getValue($value, 'tgl_pembayaran');
		$tglMasuk = ArrayHelper::getValue($value, 'tgl_pendaftaran');
		$tglPulang = ArrayHelper::getValue($value, 'tgl_pulang');
		$noPendaftaran = ArrayHelper::getValue($value, 'no_pendaftaran');
		
		$subTotalTagihan += $totalTagihan;
		$subTotalDijamin += $totalDijamin;
		$subTotalDiskon += $totalDiskon;
		$subTotalDibayar += $totalDibayar;
	?>
	<tr>
		<td><?= $no?></td>
		<td><?= date('d M Y H:i:s', strtotime($tglPembayaran)) ?></td>
		<td><?= date('d M Y H:i:s', strtotime($tglMasuk)) . '-' . date('d M Y H:i:s', strtotime($tglPulang)) ?></td>
		<td><?= $instalasiNama. ' - '.$ruanganNama ?></td>
		<td><?= $noPendaftaran ?></td>
		<td><?= $namaPasien ?></td>
		<td><?= $noRekamMedik ?></td>
		<td><?= $caraBayarNama ?></td>
		<td><?= $penjaminNama ?></td>
		<td style="text-align: right;"><?= DocoHelpers::formatNumber($totalTagihan) ?></td>
		<td style="text-align: right;"><?=DocoHelpers::formatNumber($totalDiskon)?></td>
		<td style="text-align: right;"><?=DocoHelpers::formatNumber($totalDijamin)?></td>
		<td style="text-align: right;"><?=DocoHelpers::formatNumber($totalDibayar)?></td>
	</tr>
	<?php 
	$no++;
	endforeach;
	?>
	<tr>
		<td colspan="9">Total</td>
		<td style="text-align: right;"><?=DocoHelpers::formatNumber($subTotalTagihan)?></td>
		<td style="text-align: right;"><?=DocoHelpers::formatNumber($subTotalDiskon)?></td>
		<td style="text-align: right;"><?=DocoHelpers::formatNumber($subTotalDijamin)?></td>
		<td style="text-align: right;"><?=DocoHelpers::formatNumber($subTotalDibayar)?></td>
	</tr>
	</tbody>  
</table>