<?php

/**
 * @Author: rizfardi@docotel.com
 * @Date:   2018-04-10 18:31:40
 * @Last Modified by:   afil
 * @Last Modified time: 2018-04-10 18:46:05
 * @Description:
 */
 use yii\helpers\ArrayHelper;

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
            <td>: <?=@$data['pasien']['tgl_pendaftaran'];?></td>
            <td>&nbsp;</td>
            <td><?=Yii::t('app', 'Umur');?></td>
            <td>: <?=@$data['pasien']['umur'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'No pendaftaran');?></td>
            <td>: <?=@$data['pasien']['no_pendaftaran'];?></td>
            <td>&nbsp;</td>
            <td><?=Yii::t('app', 'Dokter pemeriksa');?></td>
            <td>: <?=@$data['pasien']['nama_pegawai'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Nama pasien');?></td>
            <td>: <?=@$data['pasien']['nama_pasien'];?></td>
            <td>&nbsp;</td>
            <td><?=Yii::t('app', 'Kelas pelayanan');?></td>
            <td>: <?=@$data['pasien']['kelaspelayanan_nama'];?></td>
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
    <h5><u><?=Yii::t('app', 'Detail konsul');?></u></h5>
    <table width="100%">
        <tr>
            <td><?=Yii::t('app', 'Ruangan tujuan');?></td>
            <td>: <?=@$data['konsul']['ruangan_tujuan'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Dokter');?></td>
            <td>: <?=@$data['konsul']['nama_dokter'];?></td>
        </tr>
        <tr>
            <td><?=Yii::t('app', 'Tanggal Konsulpoli');?></td>
            <td>: <?= ArrayHelper::getValue($data, 'konsul.tgl_konsulpoli') != null ? date('d M Y, H:i:s', strtotime(ArrayHelper::getValue($data, 'konsul.tgl_konsulpoli'))) : ' - ';?></td>
        </tr>
        <?php if ($jenis == 'jawaban-konsul') { ?>
            <tr>
                <td><?=Yii::t('app', 'Tanggal Jawaban');?></td>
                <td>: <?= ArrayHelper::getValue($data, 'konsul.tgl_selesaikonsul') != null ? date('d M Y, H:i:s', strtotime(ArrayHelper::getValue($data, 'konsul.tgl_selesaikonsul'))) : ' - ';?></td>
            </tr>
            <tr>
                <td><?=Yii::t('app', 'Jawaban Konsulpoli');?></td>
                <td>: <?=@$data['konsul']['jawaban_konsul'];?></td>
            </tr>
        <?php } else { ?>
            <tr>
                <td><?=Yii::t('app', 'Catatan');?></td>
                <td>: <?=@$data['konsul']['catatan_dokter_konsul'];?></td>
            </tr>
         <?php } ?>
        ?>
        >
    </table>

    <br />
</div>
