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
<h3>PASIEN MASUK RAWAT DARI TPP RAWAT INAP</h3>
<table id="lap-sensus-harian-ranap-masuk" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th colspan="10" align="center">
                    PASIEN MASUK RAWAT DARI TPP RAWAT INAP
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="1">NO</th>
                <th align="center" >NAMA PASIEN</th>
                <th align="center" >NO REKAM MEDIK</th>
                <th align="center" >KELAS</th>
                <th align="center" >RUANGAN</th>
                <th align="center" >KAMAR</th>
                <th align="center" >BED</th>
                <th align="center">JAMINAN</th>
                <th align="center">DOKTER</th>
                <th align="center">DIAGNOSIS</th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data['dataMasuk'] as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['nama_pasien'] ?></td>
                    <td align="center"><?= $value['no_rekam_medik'] ?></td>
                    <td align="center"><?= $value['kelaspelayanan_nama'] ?></td>
                    <td align="center"><?= $value['ruangan_nama'] ?></td>
                    <td align="center"><?= $value['kamar'] ?></td>
                    <td align="center"><?= $value['tempattidur'] ?></td>
                    <td align="center"><?= $value['penjamin_nama'] ?></td>
                    <td align="center"><?= $value['nama_dokter'] ?></td>
                    <td align="center"><?= $value['diagnosa_nama'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>
<br>

<h3>PASIEN SEDANG RAWAT INAP</h3>
<table id="lap-sensus-harian-sedang-ranap" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th colspan="10" align="center">
                    PASIEN SEDANG RAWAT INAP
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="1">NO</th>
                <th align="center" >NAMA PASIEN</th>
                <th align="center" >NO REKAM MEDIK</th>
                <th align="center" >JENIS KELAMIN</th>
                <th align="center" >NO TELEPON</th>
                <th align="center" >KELAS</th>
                <th align="center" >RUANGAN</th>
                <th align="center" >Kamar</th>
                <th align="center" >Bed</th>
                <th align="center" >Jaminan</th>
                <th align="center" >Dokter</th>
                <th align="center" >Diagnosis</th>
                <th align="center" >Tgl Masuk Ruangan</th>
                <th align="center" >Lama Rawat</th>
                <th align="center" >Hari</th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data['dataSedangRanap'] as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['nama_pasien'] ?></td>
                    <td align="center"><?= $value['no_rekam_medik'] ?></td>
                    <td align="center"><?= $value['jeniskelamin_nama'] ?></td>
                    <td align="center"><?= $value['no_telepon_pasien'] ?></td>
                    <td align="center"><?= $value['kelaspelayanan_nama'] ?></td>
                    <td align="center"><?= $value['ruangan_nama'] ?></td>
                    <td align="center"><?= $value['kamar'] ?></td>
                    <td align="center"><?= $value['tempattidur'] ?></td>
                    <td align="center"><?= $value['penjamin_nama'] ?></td>
                    <td align="center"><?= $value['nama_dokter'] ?></td>
                    <td align="center"><?= $value['diagnosa_nama'] ?></td>
                    <td align="center"><?= $value['tgl_masukkamar'] ?></td>
                    <td align="center"><?= $value['jam_rawat'] ?></td>
                    <td align="center"><?= $value['lama_rawat'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>
<br>

<h3> PASIEN KELUAR RAWAT </h3>
<table id="lap-sensus-harian-ranap-keluar" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th colspan="13" align="center">
                    PASIEN KELUAR RAWAT
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="1">NO</th>
                <th align="center" >NAMA PASIEN </th>
                <th align="center">NO REKAM MEDIK </th>
                <th align="center">KELAS </th>
                <th align="center">RUANGAN </th>
                <th align="center">KAMAR </th>
                <th align="center">BED </th>
                <th align="center">JAMINAN </th>
                <th align="center">DOKTER </th>
                <th align="center">DIAGNOSIS </th>
                <th align="center">TGL MASUK RUANGAN</th>
                <th align="center">LAMA RAWAT</th>
                <th align="center">HARI </th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data['dataKeluar'] as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['nama_pasien'] ?></td>
                    <td align="center"><?= $value['no_rekam_medik'] ?></td>
                    <td align="center"><?= $value['kelaspelayanan_nama'] ?></td>
                    <td align="center"><?= $value['ruangan_nama'] ?></td>
                    <td align="center"><?= $value['kamar'] ?></td>
                    <td align="center"><?= $value['tempattidur'] ?></td>
                    <td align="center"><?= $value['penjamin_nama'] ?></td>
                    <td align="center"><?= $value['nama_dokter'] ?></td>
                    <td align="center"><?= $value['diagnosa_nama'] ?></td>
                    <td align="center"><?= $value['tgl_masukkamar'] ?></td>
                    <td align="center"><?= $value['jam_rawat'] ?></td>
                    <td align="center"><?= $value['lama_rawat'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>

<br>

<h3> PASIEN KELUAR RUJUK RS LAIN </h3>
<table id="lap-sensus-harian-ranap-keluar-rujuk" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th colspan="14" align="center">
                    PASIEN KELUAR RUJUK RS LAIN
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="1">NO</th>
                <th align="center" >NAMA PASIEN </th>
                <th align="center">NO REKAM MEDIK </th>
                <th align="center">KELAS </th>
                <th align="center">RUANGAN </th>
                <th align="center">KAMAR </th>
                <th align="center">BED </th>
                <th align="center">JAMINAN </th>
                <th align="center">DOKTER </th>
                <th align="center">DIAGNOSIS </th>
                <th align="center">TGL MASUK RUANGAN</th>
                <th align="center">LAMA RAWAT (JAM)</th>
                <th align="center">HARI </th>
                <th align="center">RS TUJUAN </th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data['dataKeluarRujuk'] as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['nama_pasien'] ?></td>
                    <td align="center"><?= $value['no_rekam_medik'] ?></td>
                    <td align="center"><?= $value['kelaspelayanan_nama'] ?></td>
                    <td align="center"><?= $value['ruangan_nama'] ?></td>
                    <td align="center"><?= $value['kamar'] ?></td>
                    <td align="center"><?= $value['tempattidur'] ?></td>
                    <td align="center"><?= $value['penjamin_nama'] ?></td>
                    <td align="center"><?= $value['nama_dokter'] ?></td>
                    <td align="center"><?= $value['diagnosa_nama'] ?></td>
                    <td align="center"><?= $value['tgl_masukkamar'] ?></td>
                    <td align="center"><?= $value['jam_rawat'] ?></td>
                    <td align="center"><?= $value['lama_rawat'] ?></td>
                    <td align="center"><?= $value['rumahsakit_rujukan'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>

<br>

<h3> PASIEN PINDAHAN DARI INSTALASI / RUANGAN LAIN </h3>
<table id="lap-sensus-harian-ranap-pindahan-dari" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th colspan="10" align="center">
                    PASIEN PINDAHAN DARI INSTALASI / RUANGAN LAIN
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="1">NO</th>
                <th align="center" >NAMA PASIEN </th>
                <th align="center">NO REKAM MEDIK </th>
                <th align="center">KELAS </th>
                <th align="center">RUANGAN </th>
                <th align="center">KAMAR </th>
                <th align="center">BED </th>
                <th align="center">JAMINAN </th>
                <th align="center">RUANGAN ASAL </th>
                <th align="center">DIAGNOSIS </th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data['dataPindahanDari'] as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['nama_pasien'] ?></td>
                    <td align="center"><?= $value['no_rekam_medik'] ?></td>
                    <td align="center"><?= $value['kelaspelayanan_nama'] ?></td>
                    <td align="center"><?= $value['ruangan_skrg'] ?></td>
                    <td align="center"><?= $value['kamar_dari'] ?></td>
                    <td align="center"><?= $value['tempattidur_dari'] ?></td>
                    <td align="center"><?= $value['penjamin_nama'] ?></td>
                    <td align="center"><?= $value['ruangan_dari'] ?></td>
                    <td align="center"><?= $value['diagnosa_nama'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>

<br>

<h3> PASIEN PINDAHAN KE INSTALASI / RUANGAN LAIN </h3>
<table id="lap-sensus-harian-ranap-pindahan-ke" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th colspan="14" align="center">
                    PASIEN PINDAHAN KE INSTALASI / RUANGAN LAIN
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="1">NO</th>
                <th align="center" >NAMA PASIEN </th>
                <th align="center">NO REKAM MEDIK </th>
                <th align="center">KELAS </th>
                <th align="center">RUANGAN </th>
                <th align="center">KAMAR </th>
                <th align="center">BED </th>
                <th align="center">JAMINAN </th>
                <th align="center">DIAGNOSIS </th>
                <th align="center">TGL MASUK RUANGAN </th>
                <th align="center">LAMA RAWAT </th>
                <th align="center">HARI </th>
                <th align="center">KAMAR TUJUAN </th>
                <th align="center">DOKTER </th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data['dataPindahanKe'] as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['nama_pasien'] ?></td>
                    <td align="center"><?= $value['no_rekam_medik'] ?></td>
                    <td align="center"><?= $value['kelaspelayanan_nama'] ?></td>
                    <td align="center"><?= $value['ruangan_skrg'] ?></td>
                    <td align="center"><?= $value['kamar_skrg'] ?></td>
                    <td align="center"><?= $value['tempattidur_skrg'] ?></td>
                    <td align="center"><?= $value['penjamin_nama'] ?></td>
                    <td align="center"><?= $value['diagnosa_nama'] ?></td>
                    <td align="center"><?= $value['tgl_masukkamar'] ?></td>
                    <td align="center"><?= $value['jam_rawat'] ?></td>
                    <td align="center"><?= $value['lama_rawat'] ?></td>
                    <td align="center"><?= $value['kamar_ke'] ?></td>
                    <td align="center"><?= $value['dokter_admisi'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>

<br>

<h3> PASIEN MENINGGAL </h3>
<table id="lap-sensus-harian-ranap-meninggal" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th colspan="13" align="center">
                    PASIEN MENINGGAL
                </th>
            </tr>
            <tr class="bg-inverse">
                <th rowspan="2" width="1">NO</th>
                <th rowspan="2" align="center" >NAMA PASIEN </th>
                <th rowspan="2" align="center">NO REKAM MEDIK </th>
                <th rowspan="2" align="center">KELAS </th>
                <th rowspan="2" align="center">RUANGAN </th>
                <th rowspan="2" align="center">KAMAR </th>
                <th rowspan="2" align="center">BED </th>
                <th rowspan="2" align="center">JAMINAN </th>
                <th rowspan="2" align="center">DOKTER </th>
                <th rowspan="2" align="center">DIAGNOSIS </th>
                <th rowspan="2" align="center">TGL MASUK RUANGAN </th>
                <th colspan="2" align="center">LAMA RAWAT </th>
            </tr>
            <tr class="bg-inverse">
                <th align="center">< 48 JAM</th>
                <th align="center">> 48 JAM</th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data['dataMeninggal'] as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['nama_pasien'] ?></td>
                    <td align="center"><?= $value['no_rekam_medik'] ?></td>
                    <td align="center"><?= $value['kelaspelayanan_nama'] ?></td>
                    <td align="center"><?= $value['ruangan_nama'] ?></td>
                    <td align="center"><?= $value['kamar'] ?></td>
                    <td align="center"><?= $value['tempattidur'] ?></td>
                    <td align="center"><?= $value['penjamin_nama'] ?></td>
                    <td align="center"><?= $value['nama_dokter'] ?></td>
                    <td align="center"><?= $value['diagnosa_nama'] ?></td>
                    <td align="center"><?= $value['tgl_masukkamar'] ?></td>
                    <td align="center"><?= $value['lama_rawat_kur48'] ?></td>
                    <td align="center"><?= $value['lama_rawat_leb48'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>

<br>

<h3> REKAPITULASI </h3>
<table id="lap-sensus-harian-ranap-rekapitulasi" class="table table-striped table-condensed table-hover" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th colspan="3" align="center">
                    REKAPITULASI
                </th>
            </tr>
            <tr class="bg-inverse">
                <th width="1">NO</th>
                <th align="center" >KETERANGAN </th>
                <th align="center">JUMLAH </th>
            </tr>
        </thead>
            <tbody>
            <?php
                $no = 1;
                foreach ($data['dataRekapitulasi'] as $value) :
            ?>
                <tr class="td-inverse">
                    <td align="center"><?= $no++ ?></td>
                    <td align="center"><?= $value['keterangan'] ?></td>
                    <td align="center"><?= $value['jumlah'] ?></td>
                </tr>
            <?php
                endforeach;
            ?>
        </tbody>
</table>