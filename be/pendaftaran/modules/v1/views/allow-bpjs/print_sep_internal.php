<?php

/**
 * @Author: rizal
 * @Date:   2019-01-07 17:27:00
 * @Last Modified by:   Rizqi Fitrianto
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
        font-size: 11px;
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
</style>
<table width="100%">
    <tr>
        <td width="55%">
            <table width="100%">
                <tr>
                    <th class="sep-detail">No. SEP</th>
                    <th class="sep-detail">:</th>
                    <td colspan=3 class="sep-detail"><?= isset($detailBpjs['noSep']) ? $detailBpjs['noSep'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Tgl. SEP</th>
                    <th class="sep-detail">:</th>
                    <td colspan=3 class="sep-detail"><?= isset($detailBpjs['tglSep']) ? date('d/m/Y', strtotime($detailBpjs['tglSep'])) : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">No. Kartu</th>
                    <th class="sep-detail">:</th>
                    <td colspan=3 class="sep-detail"><?= isset($detailBpjs['peserta']['noKartu']) ? $detailBpjs['peserta']['noKartu'] : ''; ?> (<?= isset($kunjungan->no_rekam_medik) ? $kunjungan->no_rekam_medik : ''; ?>)</td>
                </tr>
                <tr>
                    <th class="sep-detail">Nama Peserta</th>
                    <th class="sep-detail">:</th>
                    <td colspan=3 class="sep-detail"><?= isset($detailBpjs['peserta']['nama']) ? $detailBpjs['peserta']['nama'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Tgl. Lahir</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['peserta']['tglLahir']) ? date('d/m/Y', strtotime($detailBpjs['peserta']['tglLahir'])) : '-'; ?></td>
                    <th class="sep-detail">Kelamin :</th>
                    <td class="sep-detail"><?= isset($detailBpjs['peserta']['kelamin']) ? $detailBpjs['peserta']['kelamin'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">No. Telepon</th>
                    <th class="sep-detail">:</th>
                    <td colspan=3 class="sep-detail"><?= isset($getDataBpjs['response']['peserta']['mr']['noTelepon']) ? $getDataBpjs['response']['peserta']['mr']['noTelepon'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Sub/Spesialis</th>
                    <th class="sep-detail">:</th>
                    <td colspan=3 class="sep-detail"><?= isset($detailBpjs['poli']) ? $detailBpjs['poli'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">DPJP yg Melayani</th>
                    <th class="sep-detail">:</th>
                    <td colspan=3 class="sep-detail"><?= $dpjp; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Faskes Perujuk</th>
                    <th class="sep-detail">:</th>
                    <td colspan=3 class="sep-detail"><?= $faskesNama; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Diagnosa Awal</th>
                    <th class="sep-detail">:</th>
                    <td colspan=3 class="sep-detail"><?= !empty($detailBpjs['diagnosa']) ? $detailBpjs['diagnosa'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Catatan</th>
                    <th class="sep-detail">:</th>
                    <td colspan=3 class="sep-detail"><?= isset($detailBpjs['catatan']) ? $detailBpjs['catatan'] : '-'; ?></td>
                </tr>
                <tr>
                    <td colspan=5></td>
                </tr>
                <tr>
                    <td colspan=5 class="info">*<i>Saya menyetujui BPJS Kesehatan menggunaan informasi Medis Pasien Jika diperlukan</i></td>
                </tr>
                <tr>
                    <td colspan=5 class="info">*<i>SEP bukan sebagai bukti penjaminan peserta.</i></td>
                </tr>
                <tr>
                    <td colspan=5 class="cetakan">Cetakan ke <?= $bpjs->cetakan_ke; ?> - <?= date('d/m/Y H:i:s', strtotime($bpjs->tgl_cetak)) ?></td>
                </tr>
            </table>
        </td>
        <td width="45%" >
            <table width="100%" style="margin-top: -30px;">
                <!-- <tr>
                    <th class="sep-detail">No. Reg</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : ''; ?></td>
                </tr> -->
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
                    <th class="sep-detail">Jns. Rawat</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($jnsPelayanan) ? $jnsPelayanan : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Jns. Kunjungan</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail">- Kunjungan rujukan internal</td>
                </tr>
                <tr>
                    <th class="sep-detail">Poli Perujuk</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= $poliRujuk; ?></td>
                </tr>
                <!-- <tr>
                    <th class="sep-detail">Kls. Hak</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= $kelasHak ?></td>
                </tr> -->
                <tr>
                    <th class="sep-detail">Kls. Rawat</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= $kelasRawat ?></td>
                </tr>
                <?php if(isset($detailBpjs['informasi']['prolanisPRB'])) : ?>
                <?php $prb = explode(' :', $detailBpjs['informasi']['prolanisPRB']); ?>
                <?php if(!empty($prb[0] && !empty($prb[1]))) : ?>
                <tr>
                    <th class="sep-detail" style="font-weight: bold;"><?= $prb[0] ?></th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail" style="font-weight: bold;"><?= $prb[1] ?></td>
                </tr>
                <?php endif; ?>
                <?php endif; ?>
                <tr>
                    <th class="sep-detail">Penjamin</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($kunjungan->penjamin_nama) ? $kunjungan->penjamin_nama : '-'; ?></td>
                </tr>
            </table>
            <br>
            <br>
            <table class="ttd" width="80%">
                <tr>
                    <th style="font-weight: normal">Pasien / Keluarga Pasien</th>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <td>TTD</td>
                </tr>
            </table>
            <!-- <table border="1" class="ttd" cellpadding="1" cellspacing="0" style="width:80%">
                <tbody>
                    <tr>
                        <td style="text-align:center">
                            No.Pendaftaran
                            <br><b><?= !empty($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : null ?></b>
                            <br><b><?= !empty($kunjungan->ruangan_nama) ? $kunjungan->ruangan_nama : null ?></b>
                        </td>
                    </tr>
                </tbody>
            </table> -->
        </td>
    </tr>
</table>
