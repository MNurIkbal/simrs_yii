<?php

use Doco\components\DocoHelpers;
use yii\helpers\ArrayHelper;
?>

<style type="text/css">
    ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .tbl-bordered {
        border-collapse: collapse;
        width: 100%;
    }

    .tbl-bordered th,
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
        font-size: 16px;
        white-space: normal;
    }
    .strike-text {
        text-decoration: line-through;
    }

    th, .content {
        font-size: 16px;
    }

    .page-break {
        page-break-before: always;
    }
</style>

<table class="tbl-bordered">
    <thead>
        <tr class="bg-inverse">
            <th><?= \Yii::t("app", "No"); ?></th>
            <th><?= \Yii::t("app", "Ruangan / Profesi"); ?></th>
            <th><?= \Yii::t("app", "Hasil Asesmen Penatalaksanaan Pasien"); ?></th>
            <th><?= \Yii::t("app", "Instruksi"); ?></th>
            <th><?= \Yii::t("app", "Verifikasi"); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php
        foreach ($data as $index => $row_cppt) {
            $is_deleted = isset($row_cppt['is_deleted_soap']) && $row_cppt['is_deleted_soap'];
            $strike_text = $is_deleted ? '' : '';
            $pegawai_update_nama = ArrayHelper::getValue($row_cppt, 'pegawai_update_nama', ' - ');
            $created_date = ArrayHelper::getValue($row_cppt, 'created_date', ' - ');
            $ket_edit = '<p>Data sudah diubah oleh <br>'
                        . $pegawai_update_nama . ' - <br>'
                        . $created_date . '</p>';

            // Gunakan strip_tags secara selektif
            $penatalaksanaan = strip_tags($row_cppt['penatalaksanaan'], '<ul><li><b><s>');

            // Hitung jumlah kata di penatalaksanaan
            $words = preg_split('/\s+/', $penatalaksanaan);
            $wordCount = count($words);

            // Batas maksimum kata per baris
            $maxWordsPerRow = 350;

            // Jika melebihi batas kata, gunakan wordwrap
            $wrappedText = wordwrap($penatalaksanaan, $maxWordsPerRow, '|', true);
            $wrappedLines = explode('|', $wrappedText);

            // Tampilkan hasil ke dalam baris
            foreach ($wrappedLines as $lineIndex => $line) {
                echo '<tr>';
                
                // Hanya tampilkan No, Ruangan / Profesi di baris pertama
                if ($lineIndex === 0) {
                    echo '<td class="' . $strike_text . ' content">' . @$row_cppt['no'] . '</td>';
                    echo '<td class="' . $strike_text . ' content">' . @$row_cppt['ruanganprofesi'] . '</td>';
                } else {
                    // Di baris-baris selanjutnya, isi No, Ruangan / Profesi kosong
                    echo '<td class="content"></td>';
                    echo '<td class="content"></td>';
                }
                
                echo '<td class="' . $strike_text . ' content">' . DocoHelpers::purifyText($line) . '</td>';
                echo '<td class="' . $strike_text . ' content">' . DocoHelpers::purifyText(@$row_cppt['instruksi_dpjp']) . '</td>';
                echo '<td class="' . $strike_text . ' content">' . ($is_deleted ? $ket_edit : @$row_cppt['verifikasi']) . '</td>';
                echo '</tr>';
            }

           
        }
        ?>
    </tbody>
</table>
