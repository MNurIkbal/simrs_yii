<?php

use Doco\components\DocoHelpers;

//umur
$d1 = new DateTime(date("y-m-d"));
$d2 = new DateTime($getHeader['tanggal_lahir']);
$diff = $d2->diff($d1);
?>

<style type="text/css">
    .tbl-resep {
        font-family: Tahoma, serif;
        font-size: 10px;
        margin-top: 10px;
        table-layout: fixed;
        width: 100%;
        padding: 0;
    }
    .tbl-resep tr {
        padding: 0;
    }
    .tbl-resep tr td {
        padding: 1px;
        font-size: 10px;
    }
    .tbl-resep tr td.text-bigger {
        font-size: 14px;
        font-weight: bold;
    }

    .tbl-info {
        width: 100%;
        table-layout:fixed;
        font-size: 10px;
    }
    .tbl-info tr td {
        vertical-align: center;
    }

    .tbl-info .header-info {
        padding-top: 10px;
    }

    .tbl-judul {
        width: 100%;
        table-layout:fixed;
        font-size: 10px;
    }
    .upper { text-transform: uppercase; }
    .tbl-judul tr td {
        padding: 2px;
        height: 30px;
        vertical-align: center;
        text-align: center;
        border-spacing: 10px;
    }
    .tbl-judul .table {
        display: table;
        border-collapse: separate;
        border-spacing: 10px;
        padding-top: 10px;
    }
    .tbl-judul .row {
        display: table-row;
    }
    .tbl-judul .cell {
        display: table-cell;
        padding: 10px;
        background-color: gold;
    }
    .tbl-judul tr td.text-bigger {
        font-size: 16px;
    }

</style>
<?php
$nama_rs = !empty($profil_rs->nama_rumahsakit) ? $profil_rs->nama_rumahsakit : "Rumah Sakit";
$alamat = !empty($profil_rs->alamatlokasi_rumahsakit) ? $profil_rs->alamatlokasi_rumahsakit : "-";
$telp = !empty($profil_rs->no_telp_profilrs) ? "Telp" . $profil_rs->no_telp_profilrs : "-";
$profile = $alamat . ", " .  $telp;
?>
<table class="tbl-judul">
    <tbody>
        <tr>
            <td style="" class="text-bigger"><?= $nama_rs ?></td>
        </tr>
        <tr>
            <td>
                <div class="table">
                    <div class="row">
                        <div class="cell"><?= $profile ?></div>
                    </div>
                    <div class="row">
                        <div class="cell">Apoteker : <?= !empty($apoteker) ? $apoteker : "-" ?></div>
                    </div>
                    <div class="row">
                        <div class="cell">SIA : <?= !empty($no_sipa) ? $no_sipa : "-" ?></div>
                    </div>
                </div>
            </td>
        </tr>
        <tr>
            <td style="border-bottom: 5px solid #000; padding: 0; height: 10px;"></td>
        </tr>
    </tbody>
</table>
<table class="tbl-info">
    <tbody>
        <tr>
            <td style="width: 55%;" class="header-info"><?= Yii::t('app', 'Dokter') ?> : </td>
            <td style="width: 5%;"></td>
            <td class="header-info"><?= Yii::t('app', 'Tanggal Resep') ?> : </td>
        </tr>
        <tr>
            <td><?= !empty($getHeader['nama_pegawai']) ? $getHeader['nama_pegawai'] : '-' ?></td>
            <td></td>
            <td><?= !empty($getHeader['tgl_resep_dibuat']) ? date("d M Y H:i", strtotime($getHeader['tgl_resep_dibuat'])) : '-' ?></td>
        </tr>

        <tr>
            <td class="header-info"><?= Yii::t('app', 'SIP Dokter') ?> : </td>
            <td></td>
            <td class="header-info"><?= Yii::t('app', 'Tanggal Copy Resep') ?> : </td>
        </tr>
        <tr>
            <td><?= !empty($sip_dokter) ? $sip_dokter : '-' ?></td>
            <td></td>
            <td><?= date("d M Y H:i") ?></td>
        </tr>

        <tr>
            <td class="header-info"><?= Yii::t('app', 'No Resep') ?> : </td>
            <td></td>
            <td class="header-info"><?= Yii::t('app', 'Umur') ?> : </td>
        </tr>
        <tr>
            <td><?= !empty($getHeader['nomor']) ? $getHeader['nomor'] : '-' ?></td>
            <td></td>
            <td><?= !empty($getHeader['tanggal_lahir']) ? $diff->y . ' Tahun' : '-' ?></td>
        </tr>

        <tr>
            <td class="header-info"><?= Yii::t('app', 'Nama Pasien') ?> : </td>
            <td></td>
            <td class="header-info"><?= Yii::t('app', 'No. HP') ?> : </td>
        </tr>
        <tr>
            <td><?= !empty($getHeader['nama']) ? $getHeader['nama'] : '-' ?></td>
            <td></td>
            <td><?= !empty($getPasien['no_telepon_pasien']) ? $getPasien['no_telepon_pasien'] : ( !empty($getPasien['no_mobile_pasien']) ? $getPasien['no_mobile_pasien'] : '-' ) ?></td>
        </tr>

        <tr>
            <td class="header-info" colspan="3"><?= Yii::t('app', 'Alamat') ?> : </td>
        </tr>
        <tr>
            <td colspan="3"><?= !empty($getHeader['alamat_pasien']) ? $getHeader['alamat_pasien'] : '-' ?></td>
        </tr>
    </tbody>
</table>
<table class="tbl-resep">
<?php
if(!empty($data_obat['non_racikan'])){
    $total = count($data_obat['non_racikan'])-1;
    foreach($data_obat['non_racikan'] as $index => $value):
?>
    <tr>
        <td style="width: 10%;" class="text-bigger">R/</td>
        <td colspan="2"><?=$value['obatalkes_nama'].' '.$value['qty'].' '.$value['satuan_input'];?></td>
    </tr>
    <tr>
        <td style="width: 10%;"> </td>
        <td style="" colspan="2"><?=$value['signa_nama'];?></td>
    </tr>
    <tr>
        <td style="width: 10%;"> </td>
        <td style="" colspan="2" class="upper">Catatan : <?= !empty($value['etiket'])?$value['etiket']:'-';?></td>
    </tr>
    <?php if(!empty($value['det']) && $value['det'] < $value['qty']){ ?>
        <tr>
            <td style="width: 10%;"> </td>
            <td>
                <hr style='border: none; height: 5px; background-color: #000; width:100%;' />
            </td>
            <td style="width: 35%;">&nbsp;<strong>DET <?= $value['det'].' '.$value['satuan_input'];?></strong></td>
        </tr>
    <?php  } else if($index < $total || !empty($data_obat['racikan'])){ ?>
        <tr>
            <td colspan="3"> </td>
        </tr>
    <?php } ?>
<?php
    endforeach;
}
if(!empty($data_obat['racikan'])){
//    $firts = reset($data_obat['racikan']);
//    array_shift($data_obat['racikan']);
    foreach($data_obat['racikan'] as $index => $value): ?>
?>
    <tr>
       <td style="width: 10%;" class="text-bigger">R/</td>
       <td colspan="2">
        <?php
            echo $value['obatalkes_nama'].' '.$value['qty'].' '.$value['satuan_input'];
            if(!empty($value['det']) && $value['det'] < $value['qty']) echo "<strong> - DET ".$value['det'].' '.$value['satuan_input'].'</strong>';
        ?>
       </td>
    </tr>
    <tr>
        <td style="width: 10%;"> </td>
        <td style="" colspan="2"><?=$value['signa_nama'];?></td>
    </tr>
    <tr>
       <td style="width: 10%;"> </td>
       <td style="" colspan="2" class="upper">Catatan : <?= !empty($value['etiket'])?$value['etiket']:'-';?></td>
    </tr>
<?php endforeach;  ?>
<?php
}
?>
</table>
<div style="text-align: right; font-size: 10px; font-family: Tahoma, serif; padding: 3px 0 0 0;">PCC</div>
