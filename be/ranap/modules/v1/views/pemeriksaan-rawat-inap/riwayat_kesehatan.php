<?php

/**
 * @Author: rizal
 * @Date:   2018-11-23 10:20:03
 * @Last Modified by:
 * @Last Modified time:
 */

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
        vertical-align: text-top;
    }
    .tbl-main td {
        vertical-align: text-top;
        border: 0px;
        padding: 5px;
    }
    .tbl-detail {
        border: 0px;
        /*padding: 5px;*/
    }
    .tbl-detail td {
        border: 0px;
        /*padding: 5px;*/
    }
</style>

<table width="90%" class="tbl-main">
    <tbody>
        <tr>
            <td width="5%"><strong>1.</strong></td>
            <td width='40%'><strong>Keluhan Utama</strong></td>
            <td>: <?= @$data_riwayatkesehatan['keluhan_utama']; ?></td>
        </tr>
        <tr>
            <td><strong>2.</strong></td>
            <td><strong>Riwayat Kesehatan Sekarang</strong></td>
            <td>: <?= @$data_riwayatkesehatan['r_kes_sekarang'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td>Diagnosa Masuk</td>
            <td>: <?= @$data_riwayatkesehatan['diagnosa_nama'] ?></td>
        </tr>
        <tr>
            <td><strong>3.</strong></td>
            <td><strong>Riwayat Kesehatan Masa Lalu</strong></td>
            <td></td>
        </tr>
        <tr>
            <td></td>
            <td>a. Pernah Dirawat</td>
            <td>: <?= @$data_riwayatkesehatan['ket_pernahdirawat'] . ' ' . ($data_riwayatkesehatan['pernah_dirawat'] && $data_riwayatkesehatan['tgl_dirawat'] ? date('d-m-Y H:i:s', strtotime($data_riwayatkesehatan['tgl_dirawat'])) : '') ?> <br>Alasan : <?= @$data_riwayatkesehatan['alasan_dirawat'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td>b. Operasi / Tindakan</td>
            <td>: <?= @$data_riwayatkesehatan['ket_pernahtindakan'] . ' ' . ($data_riwayatkesehatan['pernah_tindakan'] && $data_riwayatkesehatan['tgl_tindakan'] ? date('d-m-Y H:i:s', strtotime($data_riwayatkesehatan['tgl_tindakan'])) : '') ?><br>Jenis : <?= @$data_riwayatkesehatan['golonganoperasi_nama'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td>c. Riwayat Alergi</td>
            <td>: <?= @$data_riwayatkesehatan['ket_pernahalergi']?> <?= $data_riwayatkesehatan['r_alergi'] ? ', ' . $data_riwayatkesehatan['nama_alergi'] : ''; ?></td>
        </tr>
        <tr>
            <td></td>
            <td>d. Transfusi Darah</td>
            <td>: <?= @$data_riwayatkesehatan['ket_transfusi'] ?> <?= $data_riwayatkesehatan['transfusi'] ? ', Reaksi : ' . @$data_riwayatkesehatan['reaksi'] : '' ?></td>
        </tr>
        <tr>
            <td><strong>4.</strong></td>
            <td><strong>Riwayat Kehamilan</strong></td>
            <td> :
                <span>
                    <table class='tbl-detail'>
                        <tr>
                            <td>G <?= @$data_riwayatkesehatan['r_kehamilan_g'] ?></td><td>P <?= @$data_riwayatkesehatan['r_kehamilan_p'] ?></td><td> A <?= @$data_riwayatkesehatan['r_kehamilan_a'] ?> </td></tr>
                        <tr><td colspan="3">HPHT <?= $data_riwayatkesehatan['hpht'] ? date('d-m-Y H:i:s', strtotime($data_riwayatkesehatan['hpht'])) : '' ?></td></tr>
                        <tr><td colspan="3">Haid : <?= $data_riwayatkesehatan['haid_teratur'] !== null ? $data_riwayatkesehatan['ket_haid'] : '' ?></td></tr>
                    </table>
                </span>
            </td>
        </tr>
        <tr>
            <td><strong>5.</strong></td>
            <td><strong>Ketergantungan Terhadap</strong></td>
            <td>: <?= @$list_asmenketergantungan ?></td>
        </tr>
        <tr>
            <td><strong>6.</strong></td>
            <td><strong>Riwayat Penyakit Keluarga</strong></td>
            <td>: <?= @$list_asmenpenyakitkel ?></td>
        </tr>
    </tbody>
</table>