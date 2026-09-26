<?php

// use app\components\DHtml;
use yii\helpers\Html;
// Handle undefined index nutrisi

$nilai_nutrisi = isset($data['nilai_nutrisi']) ? $data['nilai_nutrisi'] : null;
// Handle undefined index strongkids
$sk_kurus = isset($data['strongkids_kurus']) ? $data['strongkids_kurus'] : null;
$sk_turunbb = isset($data['strongkids_turunbb']) ? $data['strongkids_turunbb'] : null;
$sk_kondisikhusus = isset($data['strongkids_kondisikhusus']) ? $data['strongkids_kondisikhusus'] : null;
$sk_keadaan_beresiko = isset($data['strongkids_keadaan_beresiko']) ? $data['strongkids_keadaan_beresiko'] : null;
?>
<style type="text/css">
    .table-nutrition tfoot {
        border-top: 1px solid #d6d6d6;
        font-weight: bold;
    }
</style>

<table style="width: 100%">

    <tr>
        <td><b>Skrining Gizi (Berdasarkan Malnutrition Screening Tool/MST)</b></td>
    </tr>

    <tr>
        <td>(Pilih skor seseuai dengan jawaban, Total skor adalah jumlah skor yang diinginkan)</td>
    </tr>
    <tr>
        <td><b>1. Apakah pasien mengalami penurunan berat badan yang tidak diinginkan dalam 6 bulan terakhir?</b></td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Tidak penurunan berat badan</td>
                    <td><?= isset($data['nutrisi_1b']) && ($data['nutrisi_1b'] === '0' || $data['nutrisi_1b'] === 0) ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak yakin / tidak tahu /terasa baju lebih longgar</td>
                    <td><?= (isset($data['nutrisi_1b']) && $data['nutrisi_1b'] == '2b') ? '<input checked="checked" type="checkbox" /> 2' : '<input type="checkbox" /> 2'; ?></td>
                </tr>
                <tr>
                    <td>c. Jika ya, berapa penurunan berat badan tersebut</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>1-5 Kg</td>
                    <td><?= isset($data['nutrisi_1b']) && ($data['nutrisi_1b'] === '1' || $data['nutrisi_1b'] === 1) ? '<input checked="checked" type="checkbox" /> 1' : '<input type="checkbox" /> 1'; ?></td>
                </tr>
                <tr>
                    <td>6-10 Kg</td>
                    <td><?= isset($data['nutrisi_1b']) && ($data['nutrisi_1b'] === '2' || $data['nutrisi_1b'] === 2) ? '<input checked="checked" type="checkbox" /> 2' : '<input type="checkbox" /> 2'; ?></td>
                </tr>
                <tr>
                    <td>10-15 Kg</td>
                    <td><?= isset($data['nutrisi_1b']) && ($data['nutrisi_1b'] === '3' || $data['nutrisi_1b'] === 3) ? '<input checked="checked" type="checkbox" /> 3' : '<input type="checkbox" /> 3'; ?></td>
                </tr>
                <tr>
                    <td>>15 Kg</td>
                    <td><?= isset($data['nutrisi_1b']) && ($data['nutrisi_1b'] === '4' || $data['nutrisi_1b'] === 4) ? '<input checked="checked" type="checkbox" /> 4' : '<input type="checkbox" /> 4'; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td><b>2. Apakah asupan makan berkurang karena berkurangnya nafsu makan?</b></td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Ya</td>
                    <td><?= isset($data['nutrisi_2']) && $data['nutrisi_2'] == 1 ? '<input checked="checked" type="checkbox" /> 1' : '<input type="checkbox" /> 1'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak</td>
                    <td><?= isset($data['nutrisi_2']) && $data['nutrisi_2'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
                <tfoot>
                    <tr>
                        <td>Total Skor</td>
                        <td id="score-section"><?= $nilai_nutrisi ?></td>
                    </tr>
                </tfoot>
            </table>
        </td>
    </tr>

    <tr>
        <td><b>3. Pasien dengan diagnosa khusus</b></td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td><?= $diagnosa_khusus_row1 ?></td>
                </tr>
                <tr>
                    <td><?= $diagnosa_khusus_row2 ?></td>
                </tr>
            </table>
        </td>
    </tr>

    <tr>
        <td><b>Skrining Gizi (Adaptasi <i>STRONG-kids</i>)</b></td>
    </tr>
    <tr>
        <td><b>1. Apakah pasien tampak Kurus?</b></td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Ya</td>
                    <td><?= isset($data['strongkids_kurus']) && $data['strongkids_kurus'] == 1 ? '<input checked="checked" type="checkbox" /> 1' : '<input type="checkbox" /> 1'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak</td>
                    <td><?= isset($data['strongkids_kurus']) && $data['strongkids_kurus'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td><b>2. Apakah terdapat penurunan BB selama 1 bulan terakhir? <br> ( berdasarkan data penurunan BB objektif bila ada/penilaian subjektif dari orang tua ATAU untuk bayi < 1 tahun: BB tidak naik dalam 3 bulan terakhir )</b>
        </td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Ya</td>
                    <td><?= isset($data['strongkids_turunbb']) && $data['strongkids_turunbb'] == 1 ? '<input checked="checked" type="checkbox" /> 1' : '<input type="checkbox" /> 1'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak</td>
                    <td><?= isset($data['strongkids_turunbb']) && $data['strongkids_turunbb'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td><b>3. Apakah terdapat salah satu kondisi berikut? <br> ( diare >= 5x / hari atau muntah >= 3x / hari dalam 1 minggu terakhir atau asupan makanan berkurang selama 1 minggu terakhir )</b></td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Ya</td>
                    <td><?= isset($data['strongkids_kondisikhusus']) && $data['strongkids_kondisikhusus'] == 1 ? '<input checked="checked" type="checkbox" /> 1' : '<input type="checkbox" /> 1'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak</td>
                    <td><?= isset($data['strongkids_kondisikhusus']) && $data['strongkids_kondisikhusus'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td><b>4. Apakah terdapat penyakit/keadaan yang mengakibatkan berisiko mengalami malnutrisi?</b></td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Ya</td>
                    <td><?= isset($data['strongkids_keadaan_beresiko']) && $data['strongkids_keadaan_beresiko'] == 2 ? '<input checked="checked" type="checkbox" /> 2' : '<input type="checkbox" /> 2'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak</td>
                    <td><?= isset($data['strongkids_keadaan_beresiko']) && $data['strongkids_keadaan_beresiko'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
                <tfoot>
                    <tr>
                        <td>Total Skor</td>
                        <td id="strongkids-score-section"><?= isset($sk_kurus) || isset($sk_turunbb) || isset($sk_kondisikhusus) || isset($sk_keadaan_beresiko) ? $sk_kurus + $sk_turunbb + $sk_kondisikhusus + $sk_keadaan_beresiko : null; ?></td>
                    </tr>
                </tfoot>
            </table>
        </td>
    </tr>

    <tr>
        <td>
            Sudah dibaca dan diketahui oleh <?= isset($data['pegawaiverifikasigizi_nama']) && !empty($data['pegawaiverifikasigizi_nama']) ? $data['pegawaiverifikasigizi_nama'] : '....' ?> pada tanggal <?= isset($data['tgl_verifikasigizi']) && !empty($data['tgl_verifikasigizi']) ? date('d/m/Y H:i:s', strtotime($data['tgl_verifikasigizi'])) : '....' ?>
        </td>
    </tr>
</table>
