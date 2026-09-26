<?php

/**
 * @Author: rizal
 * @Date:   2018-11-23 10:20:03
<<<<<<< HEAD
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-01-08 14:44:00
=======
 * @Last Modified by:   Sigit
 * @Last Modified time: 2019-01-03 14:26:50
>>>>>>> d67a81e52b3c6399b760afdfd13275f711a1db20
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
    }
    .tbl-detail {
        border: 0px;
        padding: 5px;
    }
    .tbl-detail td {
        border: 0px;
        padding: 5px;
    }
</style>

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th width="5%">No</th>
            <th width="5%"></th>
            <th></th>
            <th>Kategori</th>
       </tr>
    </thead>
    <tbody>
        <tr>
            <td align="center">A</td>
            <td align="center">1</td>
            <td>
                <strong>Riwayat</strong>
                <table width="100%" class="tbl-detail">
                    <tbody>
                    <tr>
                        <td align="left" width="15%">BB Biasanya</td>
                        <td align="left" width="3%">:</td>
                        <td align="left" width="32%"><?= $model['bb_biasanya']; ?> Kg</td>
                        <td align="left" width="15%">BB Saat Ini</td>
                        <td align="left" width="3%">:</td>
                        <td align="left" width="32%"><?= $model['bb_saatini']; ?> Kg</td>
                    </tr>
                    <tr>
                        <td align="left" width="15%">Perubahan</td>
                        <td align="left" width="3%">:</td>
                        <td align="left" width="32%"><?= $model['perubahan_kg']; ?> Kg</td>
                        <td align="left" width="15%"></td>
                        <td align="left" width="3%"></td>
                        <td align="left" width="32%"><?= $model['perubahan_persen']; ?> %</td>
                    </tr>
                    <tr>
                        <td align="left">Perubahan</td>
                        <td align="left">:</td>
                        <td colspan="4" align="left"><?= $model['v_perubahan_hasil']; ?></td>
                    </tr>
                    </tbody>
                </table>
            </td>
            <td align="center"><?= $model['v_kategori_bb'] ?></td>
        </tr>
        <tr>
            <td align="center"></td>
            <td align="center">2</td>
            <td>
                <strong>Perubahan Asupan Makanan</strong>
                <table class="tbl-detail">
                    <tr>
                        <td><?= $model['v_asupanmkn']; ?></td>
                    </tr>
                </table>
            </td>
            <td align="center"><?= $model['v_kategori_asupanmkn'] ?></td>
        </tr>
        <tr>
            <td align="center"></td>
            <td align="center">3</td>
            <td>
                <strong>Perubahan Gastrointestinal</strong>
                <table class="tbl-detail">
                    <tr>
                        <td><?= $model['v_gastrointestinal_mual']; ?></td>
                    </tr>
                    <tr>
                        <td><?= $model['v_gastrointestinal_muntah']; ?></td>
                    </tr>
                    <tr>
                        <td><?= $model['v_gastrointestinal_diare']; ?></td>
                    </tr>
                    <tr>
                        <td><?= $model['v_gastrointestinal_anoreksia']; ?></td>
                    </tr>
                </table>
            </td>
            <td align="center"><?= $model['v_kategori_gastrointestinal'] ?></td>
        </tr>
        <tr>
            <td align="center"></td>
            <td align="center">4</td>
            <td>
                <strong>Perubahan Kapasitas Fungsional</strong>
                <table class="tbl-detail">
                    <tr>
                        <td><?= $model['v_fungsional']; ?></td>
                    </tr>
                </table>
            </td>
            <td align="center"><?= $model['v_kategori_fungsional'] ?></td>
        </tr>
        <tr>
            <td align="center"></td>
            <td align="center">5</td>
            <td>
                <strong>Penyakit dan Hubungannya Dengan Kebutuhan Gizi</strong>
                <table class="tbl-detail">
                    <tr>
                        <td><?= json_decode($model['diagnosa_medis'], true)['text']; ?></td>
                    </tr>
                    <tr>
                        <td>Hubungan dengan kebutuhan metabolik (Stress) : <?= $model['v_keb_metabolik']; ?></td>
                    </tr>
                </table>
            </td>
            <td align="center"><?= $model['v_kategori_hubungan'] ?></td>
        </tr>
        <tr>
            <td align="center">B</td>
            <td align="center"></td>
            <td>
                <strong>Penilaian Fisik</strong>
                <table class="tbl-detail">
                    <tr>
                        <td>Hilang Lemak Subkutan (Trisep, Dada) : <?= $model['fisik_lemak'] ? 'Ada' : 'Tidak Ada' ?></td>
                    </tr>
                    <tr>
                        <td>Hilang Massa Otot (Selangka, Scaptula/Tulang Belikat, Tulang Rusuk, Betis) : <?= $model['fisik_otot'] ? 'Ada' : 'Tidak Ada' ?></td>
                    </tr>
                    <tr>
                        <td>Udem : <?= $model['fisik_udem'] ? 'Ada' : 'Tidak Ada' ?></td>
                    </tr>
                    <tr>
                        <td>Asites : <?= $model['fisik_asites'] ? 'Ada' : 'Tidak Ada' ?></td>
                    </tr>
                </table>
            </td>
            <td align="center"><?= $model['v_kategori_fisik'] ?></td>
        </tr>
        <tr>
            <td align="center">C</td>
            <td align="center"></td>
            <td colspan="2">
                <strong>Penilaian SGA</strong>
                <table class="tbl-detail">
                    <tr>
                        <td><?= $model['v_penilaian_sga'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td align="center"></td>
            <td align="center"></td>
            <td colspan="2">
                <strong>Tindak Lanjut</strong>
                <table class="tbl-bordered" width="100%">
                    <tr>
                        <td width="30%">Diet</td>
                        <td><?= $model['diet'] ?></td>
                    </tr>
                    <tr>
                        <td>PAGT</td>
                        <td><?= $model['pagt'] ?></td>
                    </tr>
                    <tr>
                        <td>Saran Terapi</td>
                        <td><?= $model['saran_terapi'] ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>