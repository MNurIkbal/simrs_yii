<?php

// use app\components\DHtml;
use yii\helpers\Html;
// Handle undefined index nutrisi
$n1a = isset($model['nutrisi_1a']) ? $model['nutrisi_1a'] : null;
$n1b = isset($model['nutrisi_1b']) ? $model['nutrisi_1b'] : null;
$n2 = isset($model['nutrisi_2']) ? $model['nutrisi_2'] : null;
// Handle undefined index strongkids
$sk_kurus = isset($model['strongkids_kurus']) ? $model['strongkids_kurus'] : null;
$sk_turunbb = isset($model['strongkids_turunbb']) ? $model['strongkids_turunbb'] : null;
$sk_kondisikhusus = isset($model['strongkids_kondisikhusus']) ? $model['strongkids_kondisikhusus'] : null;
$sk_keadaan_beresiko = isset($model['strongkids_keadaan_beresiko']) ? $model['strongkids_keadaan_beresiko'] : null;
?>
<style type="text/css">
    .table-nutrition tfoot {
        border-top: 1px solid #d6d6d6;
        font-weight: bold;
    }
</style>

<table style="width: 100%">
    <tr>
        <th colspan="2">Pasien Dewasa</th>
    </tr>
    <tr>
        <td>1. Apakah pasien mengalami penurunan berat badan yang tidak diinginkan dalam 6 bulan terakhir?</td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Tidak penurunan berat badan</td>
                    <td><?= isset($model['nutrisi_1a']) && $model['nutrisi_1a'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak yakin / tidak tahu /terasa baju lebih longgar</td>
                    <td><?= isset($model['nutrisi_1a']) && $model['nutrisi_1a'] == 2 ? '<input checked="checked" type="checkbox" /> 2' : '<input type="checkbox" /> 2'; ?></td>
                </tr>
                <tr>
                    <td>c. Jika ya, berapa penurunan berat badan tersebut</td>
                    <td>&nbsp;</td>
                </tr>
                <tr>
                    <td>1-5 Kg</td>
                    <td><?= isset($model['nutrisi_1b']) && $model['nutrisi_1b'] == 1 ? '<input checked="checked" type="checkbox" /> 1' : '<input type="checkbox" /> 1'; ?></td>
                </tr>
                <tr>
                    <td>6-10 Kg</td>
                    <td><?= isset($model['nutrisi_1b']) && $model['nutrisi_1b'] == 2 ? '<input checked="checked" type="checkbox" /> 2' : '<input type="checkbox" /> 2'; ?></td>
                </tr>
                <tr>
                    <td>10-15 Kg</td>
                    <td><?= isset($model['nutrisi_1b']) && $model['nutrisi_1b'] == 3 ? '<input checked="checked" type="checkbox" /> 3' : '<input type="checkbox" /> 3'; ?></td>
                </tr>
                <tr>
                    <td>>15 Kg</td>
                    <td><?= isset($model['nutrisi_1b']) && $model['nutrisi_1b'] == 4 ? '<input checked="checked" type="checkbox" /> 4' : '<input type="checkbox" /> 4'; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>2. Apakah asupan makan berkurang karena berkurangnya nafsu makan?</td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Ya</td>
                    <td><?= isset($model['nutrisi_2']) && $model['nutrisi_2'] == 1 ? '<input checked="checked" type="checkbox" /> 1' : '<input type="checkbox" /> 1'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak</td>
                    <td><?= isset($model['nutrisi_2']) && $model['nutrisi_2'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
                <tfoot>
                    <tr>
                        <td>Total Skor</td>
                        <td id="score-section"><?= isset($n1b) || isset($n2) ? $n1b + $n2 : null; ?></td>
                    </tr>
                </tfoot>
            </table>
        </td>
    </tr>
    <tr>
        <td>3. Pasien dengan diagnosa khusus</td>
    </tr>
    <tr>
        <td>
            <table>
                <tr>
                    <td><?= isset($model['diagnosa_khusus']) && $model['diagnosa_khusus'] == 0 ? '<input checked="checked" type="checkbox" /> Tidak' : '<input type="checkbox" /> Tidak'; ?></td>
                    <td><?= isset($model['diagnosa_khusus']) && $model['diagnosa_khusus'] == 1 ? '<input checked="checked" type="checkbox" /> Ya' : '<input type="checkbox" /> Ya'; ?></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <?php if (isset($model['diagnosa_khusus']) && $model['diagnosa_khusus'] == 1 && isset($model['jenis_diagnosa_khusus'])) : ?>
                            <?php foreach ($configData['jenis_diagnosa_khusus'] as $row) : ?>
                                <?php foreach ($row as $key => $value) : ?>
                                    <input type="checkbox" <?= strpos($model['jenis_diagnosa_khusus'], $key) !== false ? 'checked="checked"' : '' ?>> <?= $value ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                            <?= substr($model['jenis_diagnosa_khusus'], strpos($model['jenis_diagnosa_khusus'], '00') + 3) !== false ? ' ' . ucwords(substr($model['jenis_diagnosa_khusus'], strpos($model['jenis_diagnosa_khusus'], '00') + 3)) : null; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <th colspan="2">Pasien Anak (adaptasi <i>STRONG-kids</i>)</th>
    </tr>
    <tr>
        <td>1. Apakah pasien tampak Kurus?</td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Ya</td>
                    <td><?= isset($model['strongkids_kurus']) && $model['strongkids_kurus'] == 1 ? '<input checked="checked" type="checkbox" /> 1' : '<input type="checkbox" /> 1'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak</td>
                    <td><?= isset($model['strongkids_kurus']) && $model['strongkids_kurus'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>2. Apakah terdapat penurunan BB selama 1 bulan terakhir? <br> ( berdasarkan data penurunan BB objektif bila ada/penilaian subjektif dari orang tua ATAU untuk bayi < 1 tahun: BB tidak naik dalam 3 bulan terakhir )</td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Ya</td>
                    <td><?= isset($model['strongkids_turunbb']) && $model['strongkids_turunbb'] == 1 ? '<input checked="checked" type="checkbox" /> 1' : '<input type="checkbox" /> 1'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak</td>
                    <td><?= isset($model['strongkids_turunbb']) && $model['strongkids_turunbb'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>3. Apakah terdapat salah satu kondisi berikut? <br> ( diare >= 5x / hari atau muntah >= 3x / hari dalam 1 minggu terakhir atau asupan makanan berkurang selama 1 minggu terakhir )</td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Ya</td>
                    <td><?= isset($model['strongkids_kondisikhusus']) && $model['strongkids_kondisikhusus'] == 1 ? '<input checked="checked" type="checkbox" /> 1' : '<input type="checkbox" /> 1'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak</td>
                    <td><?= isset($model['strongkids_kondisikhusus']) && $model['strongkids_kondisikhusus'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>4. Apakah terdapat penyakit/keadaan yang mengakibatkan berisiko mengalami malnutrisi?</td>
    </tr>
    <tr>
        <td>
            <table class="table-nutrition">
                <tr>
                    <td>a. Ya</td>
                    <td><?= isset($model['strongkids_keadaan_beresiko']) && $model['strongkids_keadaan_beresiko'] == 2 ? '<input checked="checked" type="checkbox" /> 2' : '<input type="checkbox" /> 2'; ?></td>
                </tr>
                <tr>
                    <td>b. Tidak</td>
                    <td><?= isset($model['strongkids_keadaan_beresiko']) && $model['strongkids_keadaan_beresiko'] == 0 ? '<input checked="checked" type="checkbox" /> 0' : '<input type="checkbox" /> 0'; ?></td>
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
            Sudah dibaca dan diketahui oleh <?= isset($model['pegawaiverifikasigizi_nama']) && !empty($model['pegawaiverifikasigizi_nama']) ? $model['pegawaiverifikasigizi_nama'] : '....' ?> pada tanggal <?= isset($model['tgl_verifikasigizi']) && !empty($model['tgl_verifikasigizi']) ? date('d/m/Y H:i:s', strtotime($model['tgl_verifikasigizi'])) : '....' ?>
        </td>
    </tr>
</table>
