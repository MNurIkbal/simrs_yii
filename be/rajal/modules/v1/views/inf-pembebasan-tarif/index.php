<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-06 15:35:05
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-06 16:56:20
 * @Description: 
 */
?>

<div>
    <center>
        <h3><?=$title?></h3>
    </center>

    <br />
    <h5><u><?=Yii::t('app', 'Data pasien');?></u></h5>
    <table width="100%">
        <tr>
            <td><?=Yii::t('app', 'No rekam medik');?></td>
            <td>: <?=@$data['pasien']['no_rekam_medik'];?></td>
            <td>&nbsp;</td>
            <td><?=Yii::t('app', 'Tanggal lahir');?></td>
            <td>: <?=@$data['pasien']['tanggal_lahir'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Tanggal pendaftaran');?></td>
            <td>: <?=@$data['pendaftaran']['tgl_pendaftaran'];?></td>
            <td>&nbsp;</td>
            <td><?=Yii::t('app', 'Umur');?></td>
            <td>: <?=@$data['pendaftaran']['umur'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'No pendaftaran');?></td>
            <td>: <?=@$data['pendaftaran']['no_pendaftaran'];?></td>
            <td>&nbsp;</td>
            <td><?=Yii::t('app', 'Dokter pemeriksa');?></td>
            <td>: <?=@$data['nama_dokter'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Nama pasien');?></td>
            <td>: <?=@$data['pasien']['nama_pasien'];?></td>
            <td>&nbsp;</td>
            <td><?=Yii::t('app', 'Kelas pelayanan');?></td>
            <td>: <?=@$data['kelaspelayanan_nama'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Jenis kelamin');?></td>
            <td>: <?=@$data['pasien']['jeniskelamin'];?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Kasus penyakit');?></td>
            <td>: <?=@$data['jeniskasuspenyakit_nama'];?></td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
    </table>

    <br />
    <h5><u><?=Yii::t('app', 'Detail pembebasan');?></u></h5>
    <table width="100%">
        <tr>
            <td><?=Yii::t('app', 'Tanggal pembebasan');?></td>
            <td>: <?=@$data['tgl_pembebasantarif'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'No transaksi');?></td>
            <td>: <?=@$data['no_pembebasantarif'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Total tagihan');?></td>
            <td>: <?=@$data['total_pembebasantarif'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Total tarif yang dibebaskan');?></td>
            <td>: <?=@$data['total_tagihan'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Catatan');?></td>
            <td>: <?=@$data['catatan'];?></td>
        </tr>
    </table>

    <br />
    <table width="100%">
        <tr>
            <td width="30%"><?=Yii::t('app', 'Mengetahui');?></td>
            <td width="40%">&nbsp;</td>
            <td width="30%"><?=Yii::t('app', 'Menyetujui');?></td>
        </tr>
        <tr>
            <td width="30%"><?=Yii::t('app', 'Mengetahui');?></td>
            <td width="40%">&nbsp;</td>
            <td width="30%"><?=Yii::t('app', 'Menyetujui');?></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
            <td>&nbsp;</td>
        </tr>
        <tr>
            <td width="30%"><?=@$data['jabatanmengetahui_nama'];?></td>
            <td width="40%">&nbsp;</td>
            <td width="30%"><?=@$data['jabatanmenyetujui_nama'];?></td>
        </tr>
        <tr>
            <td width="30%"><?=@$data['pegawaimengetahui_nama'];?></td>
            <td width="40%">&nbsp;</td>
            <td width="30%"><?=@$data['pegawaimenyetujui_nama'];?></td>
        </tr>
    </table>
</div>