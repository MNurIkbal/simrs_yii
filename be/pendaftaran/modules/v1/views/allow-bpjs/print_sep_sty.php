<?php

/**
 * @Author: rizal
 * @Date:   2022-01-28 13:27:00
 * @Last Modified by:   Ikhwanu arriyad
 * @Last Modified time: 2019-03-11 17:27:21
 */

?>

<style>
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
    .text-center {
        text-align: center;
    }
    body{
        font-family: tahoma;
    }
    .sep-detail {
        text-align: left;
        vertical-align: top;
        /*font-weight: normal;*/
        font-size: 14px;
    }
    .ttd {
        font-size: 14px;
        text-align: center;
    }
    .cetakan {
        font-size: 10px;
    }
    .info {
        font-size: 12px;
    }
    td {
        font-size: 14px;
    }

    td .sep-detail {
        font-weight: bold;

    }

    .top-right {
        position: absolute;
        top: 50px;
        right: 125px;
    }
</style>
<?php if($prb != false ) : ?>
<div class="top-right">
    <h4 class="sep-detail" style="font-weight: bold;"><?= $prb ?></h4>
</div>
<?php endif; ?>

<table width="100%">
    <tr>
        <td width="55%">
            <table width="100%">
                <tr>
                    <th class="sep-detail">No. SEP</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['noSep']) ? $detailBpjs['noSep'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Tgl. SEP</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['tglSep']) ? date('Y-m-d', strtotime($detailBpjs['tglSep'])) : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">No. Kartu</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['peserta']['noKartu']) ? $detailBpjs['peserta']['noKartu'] : ''; ?> (MR. <?= isset($kunjungan->no_rekam_medik) ? $kunjungan->no_rekam_medik : ''; ?>)</td>
                </tr>
                <tr>
                    <th class="sep-detail">Nama Peserta</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['peserta']['nama']) ? $detailBpjs['peserta']['nama'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Tgl. Lahir</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['peserta']['tglLahir']) ? date('Y-m-d', strtotime($detailBpjs['peserta']['tglLahir'])) : '-';?>
                    <b>&nbsp; </b> Kelamin:<b>&nbsp; </b><?= isset($detailBpjs['peserta']['kelamin']) ? $detailBpjs['peserta']['kelamin'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">No. Telepon</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($getDataBpjs['response']['peserta']['mr']['noTelepon']) ? $getDataBpjs['response']['peserta']['mr']['noTelepon'] : '-'; ?></td>
                </tr>
                <!-- <tr>
                    <th class="sep-detail">Jns. Kelamin</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?php// echo isset($detailBpjs['peserta']['kelamin']) ? $detailBpjs['peserta']['kelamin'] : '-'; ?></td>
                </tr> -->
                <tr>
                    <th class="sep-detail">Sub/Spesialis</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['poli']) ? $detailBpjs['poli'] : '-'; ?></td>
                </tr>
                <?php 
                    if ($is_ranap) {
                        ?>
                        <tr>
                            <th class="sep-detail">Dokter</th>
                            <th class="sep-detail">:</th>
                            <td class="sep-detail"><?= $dpjp; ?></td>
                        </tr>
                    <?php
                    }else {
                        ?>
                            <tr>
                                <th class="sep-detail">Dokter</th>
                                <th class="sep-detail">:</th>
                                <td class="sep-detail"><?= $dpjp; ?></td>
                            </tr>
                        <?php
                    }
                
                ?>
                
                <tr>
                    <th class="sep-detail">Faskes Perujuk</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= $faskesNama; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Diagnosa Awal</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= !empty($detailBpjs['diagnosa']) ? $detailBpjs['diagnosa'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Catatan</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['catatan']) ? $detailBpjs['catatan'] : '-'; ?></td>
                </tr>
                <tr>
                    <td colspan=3></td>
                </tr>
                <tr>
                    <td colspan=3 class="info">Catatan:<br>*<i>Saya menyetujui BPJS Kesehatan menggunaan informasi Medis Pasien Jika diperlukan</i></td>
                </tr>
                <tr>
                    <td colspan=3 class="info">*<i>SEP bukan sebagai bukti penjaminan pasien</i></td>
                </tr>
                <tr>
                    <td colspan=3 class="cetakan">Cetakan ke <?= $bpjs->cetakan_ke; ?> - <?= date('d/m/Y H:i:s', strtotime($bpjs->tgl_cetak)) ?></td>
                </tr>
            </table>
        </td>
        <td width="45%" >
            <table width="100%" >
                <tr>
                    <th class="sep-detail">Peserta</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['peserta']['jnsPeserta']) ? $detailBpjs['peserta']['jnsPeserta'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">COB</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($bpjs->is_cob) ? $bpjs->is_cob : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail"></th>
                    <th class="sep-detail"></th>
                    <td class="sep-detail"></td>
                </tr>
                <tr>
                    <th class="sep-detail"></th>
                    <th class="sep-detail"></th>
                    <td class="sep-detail"></td>
                </tr>
                <tr>
                    <th class="sep-detail"></th>
                    <th class="sep-detail"></th>
                    <td class="sep-detail"></td>
                </tr>
                <tr>
                    <th class="sep-detail"></th>
                    <th class="sep-detail"></th>
                    <td class="sep-detail"></td>
                </tr>
                <tr>
                    <th class="sep-detail"></th>
                    <th class="sep-detail"></th>
                    <td class="sep-detail"></td>
                </tr>
                <tr>
                    <th class="sep-detail">Jns. Rawat</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail" ><?= isset($jnsPelayanan) ? $jnsPelayanan : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Jns. Kunjungan</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($jnsKunjungan) ? $jnsKunjungan : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Kls. Hak</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail">
                        <?php 
                            echo $kelasHak;
                        ?></td>
                </tr>
                <?php if($showKelasRawat == true ) : ?>
                <tr>
                    <th class="sep-detail">Kls. Rawat</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail">
                        <?php if ($is_ranap){
                            echo $kelasRawat;
                        } else {
                            echo ' - ';
                        }  ?></td>
                    </td>
                </tr>
                <?php endif; ?>
            </table>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <table class="ttd" width="80%">
                <tr>
                    <th style="font-weight: bold">Pasien / Keluarga Pasien</th>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <td style="font-weight: bold">TTD</td>
                </tr>
            </table>
            
        </td>
    </tr>
</table>