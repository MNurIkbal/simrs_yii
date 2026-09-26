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

    .standout {
        font-size: 16px;
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
                    <td class="sep-detail"><span class="standout"><?= isset($detailBpjs['noSep']) ? $detailBpjs['noSep'] : ''; ?></span></td>
                </tr>
                <tr>
                    <th class="sep-detail">Tgl. SEP</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['tglSep']) ? date('d/m/Y', strtotime($detailBpjs['tglSep'])) : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">No. Kartu</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><span class="standout"><?= isset($detailBpjs['peserta']['noKartu']) ? $detailBpjs['peserta']['noKartu'] : ''; ?> (<?= isset($kunjungan->no_rekam_medik) ? $kunjungan->no_rekam_medik : '-'; ?>)</span></td>
                </tr>
                <tr>
                    <th class="sep-detail">Nama Peserta</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['peserta']['nama']) ? $detailBpjs['peserta']['nama'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Tgl. Lahir</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['peserta']['tglLahir']) ? date('d/m/Y', strtotime($detailBpjs['peserta']['tglLahir'])) : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">No. Telepon</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($getDataBpjs['response']['peserta']['mr']['noTelepon']) ? $getDataBpjs['response']['peserta']['mr']['noTelepon'] : (
                        isset($additional_request['request']['t_sep']['noTelp']) ? $additional_request['request']['t_sep']['noTelp'] : '-'); ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Jns. Kelamin</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['peserta']['kelamin']) ? $detailBpjs['peserta']['kelamin'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Sub/Spesialis</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($detailBpjs['poli']) ? $detailBpjs['poli'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">DPJP yg Melayani</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= $dpjp; ?></td>
                </tr>
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
            <table width="100%" style="margin-top: -30px;">
                <tr>
                    <th class="sep-detail">No. Reg</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : ''; ?></td>
                </tr>
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
                    <td class="sep-detail"><?= isset($jnsKunjungan) ? $jnsKunjungan : '-'; ?></td>
                </tr>
                <tr>
                    <th class="sep-detail">Kls. Hak</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= $kelasHak ?></td>
                </tr>
                <?php if($showKelasRawat == true ) : ?>
                <tr>
                    <th class="sep-detail">Kls. Rawat</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= $kelasRawat ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <th class="sep-detail">Penjamin</th>
                    <th class="sep-detail">:</th>
                    <td class="sep-detail"><?= isset($kunjungan->penjamin_nama) ? $kunjungan->penjamin_nama : '-'; ?></td>
                </tr>
            </table>
            <br>
            <br>
            <table width="100%">
                <tr>
                    <td>
                        <table class="ttd" style="width: 80%;">
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
                        <table border="1" cellpadding="1" cellspacing="0" style="width: 80%;">
                            <tbody>
                                <tr>
                                    <td style="text-align:center">
                                        No.Pendaftaran
                                        <br><b><?= !empty($kunjungan->no_pendaftaran) ? $kunjungan->no_pendaftaran : null ?></b>
                                        <br><b><?= !empty($kunjungan->ruangan_nama) ? $kunjungan->ruangan_nama : null ?></b>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td>
                        <table border="1" cellpadding="1" cellspacing="0" style="width: 150%;">
                            <tbody>
                                <tr>
                                    <td style="text-align: center;">
                                        <p><b>DIAGNOSA</b></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="padding: 7px; padding-right: 90px;">
                                        <p><b>PRIMER: </b></p>
                                        <br>
                                        <p><b>SEKUNDER: </b></p>
                                        <br><br><br>
                                        <br>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>