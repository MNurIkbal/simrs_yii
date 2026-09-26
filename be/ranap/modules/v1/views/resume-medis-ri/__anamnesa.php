<style>
    .section-table {
        padding: 8px;
        border: 1px solid black;
        border-radius: 12px;
    }
    .section-table table {
        width: 100%;
        line-height: 0;
    }
    .section-table table td, .content-top {
        vertical-align: top;
    }
    .label {
        font-weight: bold;
        width: 10%;
    }
    .value-label {
        width: 20%;
    }
</style>
<div class="section-table">
    <table class="tbl tbl-no-bordered" width="100%">
        <tr>
            <td class="label">Keluhan Utama</td>
            <td width="8px">:</td>
            <td class="value-label"><?= isset($data['keluhan_utama']) ? $data['keluhan_utama'] : '' ?></td>
            <td class="label">Riwayat Alergi</td>
            <td width="8px">:</td>
            <td class="value-label">
                <p>Obat : <?= isset($data['alergi_obat']) ? $data['alergi_obat'] : '' ?></p>
                <p>Makanan : <?= isset($data['alergi_makanan']) ? $data['alergi_makanan'] : ''?></p>
                <p>Lainnya : <?= isset($data['alergi_lainnya']) ? $data['alergi_lainnya'] : '' ?></p>
            </td>
            <td class="label">Obat-obatan yang dikonsumsi</td>
            <td width="8px">:</td>
            <td class="value-label"><?= isset($data['obat_diberikan']) ? $data['obat_diberikan'] : '' ?></td>
        </tr>
        <tr>
            <td class="label">Metode Nyeri</td>
            <td width="8px">:</td>
            <td class="value-label"><?= isset($data['metod_asmennyeri']) ? $data['metod_asmennyeri'] : '' ?></td>
            <td class="label">Skor Nyeri</td>
            <td width="8px">:</td>
            <td class="value-label"><?= isset($data['skala']) ? $data['skala'] : '' ?></td>
        </tr>
    </table>
</div>
