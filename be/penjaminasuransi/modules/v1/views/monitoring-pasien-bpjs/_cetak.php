<?php
use Doco\components\DocoHelpers;
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
</style>

<table width="100%" class="tbl-bordered">
    <thead>
       <tr>
            <th>No.</th>
            <th>Tanggal Masuk</th>
            <th>Tanggal Keluar</th>
            <th>No Rekam Medik</th>
            <th>No SEP</th>
            <th>Nama Pasien</th>
            <th>Penjamin</th>
            <th>Ruangan</th>
            <th>Kamar-Bed</th>
            <th>Hak Kelas</th>
            <th>Dokter Penanggung Jawab</th>
            <th>Diagnosa Utama</th>
            <th>Diagnosa Penyerta</th>
            <th>Tindakan</th>
            <th>Tagihan RS</th>
            <th>Tarif Inacbg</th>
            <th>Persentase (%)</th>
            <th>Status</th>
       </tr>
    </thead>
    <tbody>
        <?php $no = 1; foreach ($data as $key => $value) : 
        $diagnosaTindakanNama = $diagnosaPenyertaNama = '';
        if(!empty($value['set_diagnosatindakan'])) {
            $listDiagnosaTindakan = json_decode($value['set_diagnosatindakan'], true);
            if(!empty($listDiagnosaTindakan)) {
                foreach ($listDiagnosaTindakan as $k => $v) {
                    $diagnosaTindakanNama = $v['kode'].' - '.$v['text'];
                }
            }
        }

        if(!empty($value['set_diagnosapenyerta'])) {
            $listDiagnosaPenyerta = json_decode($value['set_diagnosapenyerta'], true);
            if(!empty($listDiagnosaPenyerta)) {
                foreach ($listDiagnosaPenyerta as $k => $v) {
                    $diagnosaPenyertaNama = $v['kode'].' - '.$v['text'];
                }
            }
        }

        if($value['tarif_inacbg'] == 0) {
            $persentase = 0;
        }
        else {
            $persentase = ceil(($value['tagihan_rs']/$value['tarif_inacbg']) * 100);
        }
        ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= date(' d M Y', strtotime($value['tgl_pendaftaran'])) ?></td>
                <td><?= !empty($value['tglpasienpulang']) ? date('d M Y', strtotime($value['tglpasienpulang'])) : ''; ?></td>
                <td><?= $value['no_rekam_medik'] ?></td>
                <td><?= $value['nosep'] ?></td>
                <td><?= $value['nama_pasien'] ?></td>
                <td><?= $value['penjamin_nama'] ?></td>
                <td><?= $value['ruangan_nama'] ?></td>
                <td><?= $value['kamarruangan_nokamar'].' - '.$value['no_tempattidur'] ?></td>
                <td><?= $value['hak_kelas'] ?></td>
                <td><?= $value['dokter_dpjp'] ?></td>
                <td><?= $value['set_diagnosautama'] ?></td>
                <td><?= $diagnosaPenyertaNama ?></td>
                <td><?= $diagnosaTindakanNama ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tagihan_rs']) ?></td>
                <td style="text-align: right;"><?= DocoHelpers::formatNumber($value['tarif_inacbg']) ?></td>
                <td style="text-align: right;"><?= $persentase ?></td>
                <td><?= $value['status_monitor'] ?></td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>