<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    table {
        border-collapse: collapse;
    }
    .bg-inverse th, .td-inverse td {
        border: 1px solid #000000;
        padding: 10px;
        text-align: left;
    }
  /* tr:nth-child(even) {
    background-color: #eee;
  }
  tr:nth-child(odd) {
    background-color: #fff;
  }   */
</style>
<table id="lap-ptm" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th width="1">NO</th>
                <th align="center" >No KTP</th>
                <th align="center" >No BPJS</th>
                <th align="center" >NAMA PASIEN</th>
                <th align="center" >NO REKAM MEDIK</th>
                <th align="center" >TANGGAL LAHIR</th>
                <th align="center" >NO HP</th>
                <th align="center">EMAIL</th>
                <th align="center">ALAMAT</th>
                <th align="center">TANGGAL REGISTRASI</th>
                <th align="center">NO REGISTRASI</th>
                <th align="center">DIAGNOSA</th>
                <th align="center">SOAP (O)</th>
                <th align="center">UMUR</th>
                <th align="center">JUMLAH KUNJUNGAN</th>
                <th align="center">NAMA DOKTER</th>
                <th align="center">GOLONGAN DARAH</th>
                <th align="center">PEMERIKSAAN EKG</th>
                <th align="center">NAMA KELUARGA</th>
                <th align="center">TANGGAL PULANG</th>
                <th align="center">KEADAAN SEKARANG</th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['no_identitas_pasien'] ?></td>
                    <td align="center"><?= $value['nopeserta_bpjs'] ?></td>
                    <td align="center"><?= $value['nama_pasien'] ?></td>
                    <td align="center"><?= $value['no_rekam_medik'] ?></td>
                    <td align="center"><?= $value['tanggal_lahir'] ?></td>
                    <td align="center"><?= $value['no_telepon_pasien'] ?></td>
                    <td align="center"><?= $value['alamatemail'] ?></td>
                    <td align="center"><?= $value['alamat_pasien'] ?></td>
                    <td align="center"><?= $value['tgl_registrasi'] ?></td>
                    <td align="center"><?= $value['no_registrasi'] ?></td>
                    <td align="center"><?= $value['diag_utama'] ?></td>
                    <td align="center"><?= $value['object'] ?></td>
                    <td align="center"><?= $value['umur'] ?></td>
                    <td align="center"><?= $value['jumlah_kunjungan'] ?></td>
                    <td align="center"><?= $value['nama_dokter'] ?></td>
                    <td align="center"><?= $value['golongan_darah'] ?></td>
                    <td align="center"><?= $value['pemeriksaan_ekg'] ?></td>
                    <td align="center"><?= $value['nama_ayah'] ?></td>
                    <td align="center"><?= $value['tgl_pulang'] ?></td>
                    <td align="center"><?= $value['keadaan_sekarang'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>