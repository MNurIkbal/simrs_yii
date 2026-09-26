<?php
    use Doco\components\DocoHelpers;

    //umur
    $d1 = new DateTime(date("y-m-d"));
    $d2 = new DateTime($getHeader['tanggal_lahir']);
    $diff = $d2->diff($d1);

    $bb = !empty($getHeader['berat_badan']) ? $getHeader['berat_badan'] : '-';
    $tb = !empty($getHeader['tinggi_badan']) ? $getHeader['tinggi_badan'] : '-';
    $str_bb_tb =  $bb . ' Kg / ' . $tb . ' cm';
    
    $no_pendaftaran = !empty($getHeader['no_pendaftaran']) ? $getHeader['no_pendaftaran'] : '-';
?>

<!-- header -->
<h4 style="text-align:center; padding-top: -25px;">Resep Obat</h4>
<table class="tbl-header">
    <?php if ($type == "resep") : ?>
        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'Nama Dokter') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= !empty($getHeader['nama_pegawai']) ? $getHeader['nama_pegawai'] : '-' ?></td>
            
            <td class="lbl-header"><?= Yii::t('app', 'No. Rekam Medik') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header">
                <strong class="cn-highlight"><?= !empty($getHeader['no_rekam_medik']) ? $getHeader['no_rekam_medik'] : '-'  ?></strong>
            </td>
            <td class="lbl-header"><?= Yii::t('app', 'Tanggal .....................') ?></td>
            
           
        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'SIP Dokter') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= !empty($sip_dokter) ? wordwrap($sip_dokter, 31, "\n", true) : '-' ?></td>

            <td class="lbl-header"><?= Yii::t('app', 'Nama Pasien') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header">
                <strong class="cn-highlight"><?= !empty($getHeader['nama']) ? $getHeader['nama'] : '-'  ?></strong>
            </td>
            <td class="lbl-header" style="text-align:center;"><?= Yii::t('app', 'Nama dan Tanda Tangan Dokter')?></td>

        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'Tanggal Resep') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= !empty($getHeader['tglresep']) ? date("d M Y H:i", strtotime($getHeader['tglresep'])) : '-' ?></td>

            <td class="lbl-header"><?= Yii::t('app', 'Tanggal Lahir') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header">
                <strong class="cn-highlight"><?= !empty($getHeader['tanggal_lahir']) ? date("d M Y", strtotime($getHeader['tanggal_lahir'])) : '-' ?></strong>
            </td>
        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'No. Resep') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= !empty($getHeader['nomor']) ? $getHeader['nomor'] : '-' ?></td>

            <td class="lbl-header"><?= Yii::t('app', 'Umur') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"> <?= !empty($getHeader['tanggal_lahir']) ? $diff->y . ' Tahun' : '-' ?> </td>

        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'No. Registrasi') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= $no_pendaftaran ?> </td>

            <td class="lbl-header"><?= Yii::t('app', 'Jenis Kelamin') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"> <?= !empty($getHeader['jenis_kelamin']) ? $getHeader['jenis_kelamin'] : '-' ?> </td>

        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'Cara Bayar') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"> <?= !empty($getHeader['carabayar_nama']) ? $getHeader['carabayar_nama'] : '-' ?> </td>

            <td class="lbl-header"><?= Yii::t('app', 'Berat/Tinggi Badan') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"> <?= $str_bb_tb ?> </td>
        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'Ruangan') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"> <?= !empty($dataPasien['ruangan_nama']) ? $dataPasien['ruangan_nama'] : '-' ?> </td>

            <td class="lbl-header"><?= Yii::t('app', 'Alergi') ?></td>
            <td style=" padding-bottom: 0; width: 1%">:</td>
            <td class="cn-header"><?= !empty($getHeader['riwayat_alergi']) ? $getHeader['riwayat_alergi'] : '-' ?></td>
        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'Kamar') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"> <?= !empty($dataPasien['kamar']) ? $dataPasien['kamar'] : '-' ?> </td>

            <td class="lbl-header"><?= Yii::t('app', 'Alamat') ?></td>
            <td style=" padding-bottom: 0; width: 1%">:</td>
            <td class="cn-header"><?= !empty($getHeader['alamat_pasien']) ? $getHeader['alamat_pasien'] : '-' ?></td>
            <td class="lbl-header" style="text-align:center;" ><?= Yii::t('app', '(.....................)')?></td>

        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'Tempat Tidur') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"> <?= !empty($dataPasien['no_tempattidur']) ? $dataPasien['no_tempattidur'] : '-' ?> </td>

            <td class="lbl-header"><?= Yii::t('app', 'Nomor Telp.') ?></td>
            <td style=" padding-bottom: 0; width: 1%">:</td>
            <td class="cn-header">
                <?= !empty($getHeader['no_telepon_pasien']) ? $getHeader['no_telepon_pasien'] : '-' ?> / 
                <?= !empty($getHeader['no_mobile_pasien']) ? $getHeader['no_mobile_pasien'] : '-' ?>
            </td>
            <td class="lbl-header" style="text-align:center;"><?= Yii::t('app', 'No.SIP')?></td>

        </tr>
        
    <?php else : ?>
        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'Tanggal Dibuat') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= date('d M Y H:i') ?></td>

            <td class="lbl-header"><?= Yii::t('app', 'Nama Pasien') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header">
                <strong>
                    <?= !empty($getHeader['nama']) ? $getHeader['nama'] : '-'  ?>
                </strong>
            </td>
            <td class="lbl-header"><?= Yii::t('app', 'Tanggal .....................') ?></td>

        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'Nama Dokter') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= !empty($getHeader['nama_pegawai']) ? $getHeader['nama_pegawai'] : '-' ?></td>

            <td class="lbl-header"><?= Yii::t('app', 'Tanggal Lahir') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header">
                <strong>
                    <?= !empty($getHeader['tanggal_lahir']) ? date("d M Y", strtotime($getHeader['tanggal_lahir'])) : '-' ?>
                </strong>
            </td>
            <td class="lbl-header" style="text-align:center;"><?= Yii::t('app', 'Nama dan Tanda Tangan Dokter')?></td>

        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'No. SIP') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= !empty($sip_dokter) ? wordwrap($sip_dokter, 31, "\n", true) : '-' ?></td>

            <td class="lbl-header"><?= Yii::t('app', 'Umur') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"> <?= !empty($getHeader['tanggal_lahir']) ? $diff->y . ' Tahun' : '-' ?> </td>
        </tr>
            
        <tr style="border:1; border-style:groove;">
            <td class="lbl-header"><?= Yii::t('app', 'Tanggal Resep') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= !empty($getHeader['tglresep']) ? date("d M Y H:i", strtotime($getHeader['tglresep'])) : '-' ?></td>

            <td class="lbl-header"><?= Yii::t('app', 'Alamat') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= !empty($getHeader['alamat_pasien']) ? $getHeader['alamat_pasien'] : '-' ?></td>
        </tr>

        <tr>
            <td class="lbl-header"><?= Yii::t('app', 'No.Resep') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"><?= !empty($getHeader['nomor']) ? $getHeader['nomor'] : '-' ?></td>

            <td class="lbl-header"><?= Yii::t('app', 'Jenis Kelamin') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"> <?= !empty($getHeader['jenis_kelamin']) ? $getHeader['jenis_kelamin'] : '-' ?> </td>
        </tr>

        <tr>
            <td colspan="3"></td>
            <td class="lbl-header"><?= Yii::t('app', 'Berat/Tinggi Badan') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header"> <?= $str_bb_tb ?> </td>
            <td class="lbl-header" style="text-align:center;" ><?= Yii::t('app', '(.....................)')?></td>

        </tr>

        <tr>
            <td colspan="3"></td>
            <td class="lbl-header"><?= Yii::t('app', 'Alergi') ?></td>
            <td style="width: 1%;">:</td>
            <td class="cn-header">
                <strong class="cn-highlight"><?= !empty($getHeader['riwayat_alergi']) ? $getHeader['riwayat_alergi'] : '-' ?></strong>
            </td>
            <td class="lbl-header" style="text-align:center;"><?= Yii::t('app', 'No.SIP')?></td>

        </tr>
        
    <?php endif; ?>
    
</table>
