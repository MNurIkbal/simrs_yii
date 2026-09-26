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
    .rujukan-detail {
        text-align: left;
        vertical-align: top;
        font-size: 14px;
    }
    .rujukan-non-detail {
        text-align: left;
        vertical-align: top;
        font-size: 14px;
        font-weight: normal;
    }
    .ttd {
        font-size: 11px;
        text-align: center;
    }
    .info {
        font-size: 12px;
    }
    td {
        font-size: 10px;
    }
</style>

<br><br>
<table width="100%">
    <tr>
        <td width="75%" style="vertical-align: top;">
            <table width="100%">
                <tr>
                    <th class="rujukan-detail" width="25%">Kepada Yth</th>
                    <th class="rujukan-detail" width="5%">:</th>
                    <td class="rujukan-detail"><?= isset($rujukan['dirujukke_nama']) ? $rujukan['dirujukke_nama'] : '-'; ?></td>
                </tr>
                
                <br>
                <br>
                <tr>
                    <th class="rujukan-non-detail" colspan="3">Mohon Pemeriksaan dan Penanganan Lebih Lanjut :</th>
                </tr>
                <tr>
                    <th class="rujukan-detail">No. Kartu</th>
                    <th class="rujukan-detail">:</th>
                    <td class="rujukan-detail"><?= isset($peserta['noKartu']) ?  $peserta['noKartu'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="rujukan-detail">Nama Peserta</th>
                    <th class="rujukan-detail">:</th>
                    <td class="rujukan-detail"><?= isset($peserta['nama']) ? $peserta['nama'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="rujukan-detail">Tanggal Lahir</th>
                    <th class="rujukan-detail">:</th>
                    <td class="rujukan-detail"><?= isset($peserta['tglLahir']) ? $peserta['tglLahir'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="rujukan-detail">Diagnosa</th>
                    <th class="rujukan-detail">:</th>
                    <td class="rujukan-detail"><?= isset($rujukan['diagnosa_rujukan_nama']) ? $rujukan['diagnosa_rujukan_nama'] : '-'; ?></td>
                </tr>
                <tr>
                    <th class="rujukan-detail">Keterangan</th>
                    <th class="rujukan-detail">:</th>
                    <td class="rujukan-detail"><?= isset($rujukan['catatan_rujukan']) ? $rujukan['catatan_rujukan'] : '-'; ?></td>
                </tr>
                <br>
                <br>
                <tr>
                    <th class="rujukan-non-detail" colspan="3">Demikian atas bantuannya,diucapkan banyak terima kasih.</th>
                </tr>
                <br>
                <br>
                <br>
                <br>
                <br>
                <tr>
                    <td colspan="3"><i>Tgl cetak <?= date('Y-m-d H:i:s')  ?></i></td>
                </tr>
            </table>
        </td>
        <td width="25%">
            <br>
            <table class="ttd" width="100%">
                <tr>
                    <th class="rujukan-non-detail">== Rujukan <?= isset($rujukan['rujukan']) ? strtoupper($rujukan['rujukan']) : '-' ?> ==</th>
                </tr>
                <tr>
                    <th class="rujukan-non-detail"><?= isset($rujukan['jenis_pelayanan_bpjs']) ? $rujukan['jenis_pelayanan_bpjs'] : '-' ?></th>
                </tr>
            </table>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <br>
            <table class="ttd" width="100%">
                <tr>
                    <th style="font-weight:14px;">Mengetahui,</th>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <th>&nbsp;</th>
                </tr>
                <tr>
                    <td>_________________________</td>
                </tr>
            </table>
        </td>
    </tr>
</table>