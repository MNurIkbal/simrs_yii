<?php
$intesitas_nyeri = $durasi_nyeri = $frekuensi_nyeri = $karakteristik_nyeri = $lamanya_nyeri = $faktor_nyeri = $rencana_tindakan = '';
// Cek skala nyeri
$skala_nyeri = isset($model['pilih_skala']) && strtolower($model['pilih_skala']) == 'dewasa' ? 
                (isset($model['skala_nyeri']) ? $model['skala_nyeri'] : null) : 
                (isset($model['skala_nyeri_anak']) ? $model['skala_nyeri_anak'] : null);
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

    .tbl-skala-nyeri {
        width: 100%;
        margin-bottom: 10px;
    }

    .tbl-skala-nyeri td {
        padding: 5px;
    }

    .tbl-skala-nyeri .tr-skala-nyeri td {
        color: #fff;
        text-align: center;
        padding: 5px;
    }

    .skala-nyeri {
        background-color: #888888;
    }

    .skala-nyeri__0 {
        background-color: #04c86b;
    }

    .skala-nyeri__1 {
        background-color: #4fc354;
    }

    .skala-nyeri__2 {
        background-color: #8dbd33;
    }

    .skala-nyeri__3 {
        background-color: #c5da2c;
    }

    .skala-nyeri__4 {
        background-color: #f0f221;
    }

    .skala-nyeri__5 {
        background-color: #f2d51a;
    }

    .skala-nyeri__6 {
        background-color: #f2b610;
    }

    .skala-nyeri__7 {
        background-color: #f09409;
    }

    .skala-nyeri__8 {
        background-color: #ef7800;
    }

    .skala-nyeri__9 {
        background-color: #e54209;
    }

    .skala-nyeri__10 {
        background-color: #d61f01;
    }
</style>
<table class="tbl-skala-nyeri">
    <tr>
        <td colspan="11" style="text-align: center;">Skala Nyeri</td>
    </tr>
    <tr class="tr-skala-nyeri">
        <?php for ($i = 0; $i <= 10; $i++) : ?>
            <td class="skala-nyeri<?= !empty($skala_nyeri) && $skala_nyeri == $i ? '__' . $i : '' ?>"><?= $i ?></td>
        <?php endfor; ?>
    </tr>
</table>

<table class="tbl-bordered" style="width:100%;">
    <thead>
        <tr class="bg-inverse">
            <th width="5%">No</th>
            <th width="18%">Pengkajian</th>
            <?php foreach ($modelResiko as $key => $value) : ?>
                <th class="form-column"><?= isset($value['tgl_pengkajian']) && !empty($value['tgl_pengkajian']) ? $value['tgl_pengkajian'] : '' ?></th>
            <?php endforeach; ?>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td class="text-center">1</td>
            <td>Skala Nyeri</td>
            <?php foreach ($modelResiko as $key => $value) : ?>
                <td class="form-column"><?= isset($value['skala_nyeri']) && !empty($value['skala_nyeri']) ? $value['skala_nyeri'] : '' ?></td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td class="text-center">2</td>
            <td>Lokasi Nyeri</td>
            <?php foreach ($modelResiko as $key => $value) : ?>
                <td class="form-column"><?= isset($value['lokasi_nyeri']) && !empty($value['lokasi_nyeri']) ? $value['lokasi_nyeri'] : '' ?></td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td class="text-center">3</td>
            <td>Intensitas Nyeri</td>
            <?php foreach ($modelResiko as $key => $value) : ?>
                <td class="form-column">
                    <div class="row">
                        <?php
                        foreach ($configData['intesitas_nyeri'] as $k => $v) {
                            $intesitas_nyeri .= strpos($value['intesitas_nyeri'], $k) !== false ? '<input checked="checked" type="checkbox" /> ' . $v . ' ' : '<input type="checkbox" /> ' . $v . ' ';
                        }
                        echo $intesitas_nyeri;
                        unset($intesitas_nyeri);
                        $intesitas_nyeri = '';
                        ?>
                    </div>
                </td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td class="text-center">4</td>
            <td>Durasi Nyeri</td>
            <?php foreach ($modelResiko as $key => $value) : ?>
                <td class="form-column">
                    <div class="row">
                        <?php
                        foreach ($configData['durasi_nyeri'] as $k => $v) {
                            $durasi_nyeri .= strpos($value['durasi_nyeri'], $k) !== false ? '<input checked="checked" type="checkbox" /> ' . $v . ' ' : '<input type="checkbox" /> ' . $v . ' ';
                        }
                        echo $durasi_nyeri;
                        unset($durasi_nyeri);
                        $durasi_nyeri = '';
                        ?>
                    </div>
                </td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td class="text-center">5</td>
            <td>Frekuensi Nyeri</td>
            <?php foreach ($modelResiko as $key => $value) : ?>
                <td class="form-column">
                    <div class="row">
                        <?php
                        foreach ($configData['frekuensi_nyeri'] as $k => $v) {
                            $frekuensi_nyeri .= strpos($value['frekuensi_nyeri'], $k) !== false ? '<input checked="checked" type="checkbox" /> ' . $v . ' ' : '<input type="checkbox" /> ' . $v . ' ';
                        }
                        echo $frekuensi_nyeri;
                        unset($frekuensi_nyeri);
                        $frekuensi_nyeri = '';
                        ?>
                    </div>
                </td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td class="text-center">6</td>
            <td>Karakteristik Nyeri</td>
            <?php foreach ($modelResiko as $key => $value) : ?>
                <td class="form-column">
                    <div class="row">
                        <?php
                        foreach ($configData['karakteristik_nyeri'] as $k => $v) {
                            $karakteristik_nyeri .= strpos($value['karakteristik_nyeri'], $k) !== false ? '<input checked="checked" type="checkbox" /> ' . $v . ' ' : '<input type="checkbox" /> ' . $v . ' ';
                        }
                        echo $karakteristik_nyeri;
                        unset($karakteristik_nyeri);
                        $karakteristik_nyeri = '';
                        ?>
                    </div>
                </td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td class="text-center">7</td>
            <td>Lamanya Nyeri</td>
            <?php foreach ($modelResiko as $key => $value) : ?>
                <td class="form-column">
                    <div class="row">
                        <?php
                        foreach ($configData['lamanya_nyeri'] as $k => $v) {
                            $lamanya_nyeri .= strpos($value['lamanya_nyeri'], $k) !== false ? '<input checked="checked" type="checkbox" /> ' . $v . ' ' : '<input type="checkbox" /> ' . $v . ' ';
                        }
                        echo $lamanya_nyeri;
                        unset($lamanya_nyeri);
                        $lamanya_nyeri = '';
                        ?>
                    </div>
                </td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td class="text-center">8</td>
            <td>Faktor yang Meningkatkan Nyeri</td>
            <?php foreach ($modelResiko as $key => $value) : ?>
                <td class="form-column">
                    <div class="row">
                        <?php
                        foreach ($configData['faktor_nyeri'] as $k => $v) {
                            $faktor_nyeri .= strpos($value['faktor_nyeri'], $k) !== false ? '<input checked="checked" type="checkbox" /> ' . $v . ' ' : '<input type="checkbox" /> ' . $v . ' ';
                        }
                        echo $faktor_nyeri;
                        unset($faktor_nyeri);
                        $faktor_nyeri = '';
                        ?>
                    </div>
                </td>
            <?php endforeach; ?>
        </tr>
        <tr>
            <td class="text-center">9</td>
            <td>Rencana tindakan</td>
            <?php foreach ($modelResiko as $key => $value) : ?>
                <td class="form-column">
                    <div class="row">
                        <?php
                        foreach ($configData['rencana_tindakan'] as $k => $v) {
                            $rencana_tindakan .= strpos($value['rencana_tindakan'], $k) !== false ? '<input checked="checked" type="checkbox" /> ' . $v . ' ' : '<input type="checkbox" /> ' . $v . ' ';
                        }
                        echo $rencana_tindakan;
                        unset($rencana_tindakan);
                        $rencana_tindakan = '';
                        ?>
                    </div>
                </td>
            <?php endforeach; ?>
        </tr>
    </tbody>
</table>
