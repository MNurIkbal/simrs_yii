<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    table {
        border-collapse: collapse;
    }

    .global-size {
        font-size: 11px;
    }
   .bg-inverse th, .td-inverse td {
    border: 1px solid #000000;
    padding: 5px;
    text-align: left;
    }

    .top-aligned-row {
      vertical-align: top; 
    }

    .bottom-aligned-row {
      vertical-align: bottom; 
    }

    .make-center {
        vertical-align: middle;
    }

    .border-bottom {
        border-bottom: 1px solid;
    }

    .header-fixed {
        width: 30px;
        overflow: hidden;
        display: inline-block;
        white-space: nowrap;
    }

    /* table td {
        white-space: nowrap;
    } */

    table td {
        word-wrap:break-word;
        white-space: normal;
    }
</style>
<div>
    <table class="table table-striped table-condensed table-hover test" style="width:100%">
        <thead>
            <tr class="bg-inverse">
                <th colspan="5" width="1"  align="right" style="font-size:15px; font-weight: bold;"><?= $title; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="td-inverse">
                <td>
                    <span class="global-size" style=" font-weight: bold;">No. RM : </span>
                    <br>
                    <span class="global-size"><?= isset($data->no_rekam_medik)?$data->no_rekam_medik:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">No. Registrasi : </span>
                    <br>
                    <span class="global-size"><?= isset($data->no_registrasi)?$data->no_registrasi:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Tgl Registrasi : </span>
                    <br>
                    <span class="global-size"><?= isset($data->tgl_registrasi)?date('j M Y H:i:s', strtotime($data->tgl_registrasi)):''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Tgl Cetak : </span>
                    <br>
                    <span class="global-size"><?= $tgl_cetak ?></span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Dokter : </span>
                    <br>
                    <span class="global-size"><?= isset($data->nama_dokter)?$data->nama_dokter:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse"> 
                <td rowspan="3" colspan="2" class="top-aligned-row">
                    <span class="global-size" style=" font-weight: bold">Nama & Alamat </span>
                    <br>
                    <br>
                    <span class="global-size" style=" font-weight: bold"><?= isset($data->nama_pasien)? $data->nama_pasien:''; ?>&nbsp;</span>
                    <br>
                    <br>
                    <span class="global-size" style="word-break:break-all;"><?= isset($data->alamat_pasien)?$data->alamat_pasien:''; ?>&nbsp;</span>
                </td>
                <td>
                <span class="global-size" style=" font-weight: bold">Tempat & Tgl Lahir:  : </span>
                    <br>
                    <span class="global-size" style=""><?= $ttl; ?> &nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Umur : </span>
                    <br>
                    <span class="global-size"><?= isset($data->umur)?$data->umur:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Jenis Kelamin : </span>
                    <br>
                    <span class="global-size"><?= isset($data->jeniskelamin)?$data->jeniskelamin:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse"> 
                <td>
                    <span class="global-size" style=" font-weight: bold">No. KTP : </span>
                    <br>
                    <span class="global-size"><?=$ktp; ?></span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Kebangsaan : </span>
                    <br>
                    <span class="global-size"><?= isset($data->kebangsaan)?$data->kebangsaan:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Suku : </span>
                    <br>
                    <span class="global-size"><?= isset($data->suku_nama)?$data->suku_nama: ''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse"> 
                <td>
                    <span class="global-size" style=" font-weight: bold">Status : </span>
                    <br>
                    <span class="global-size"><?= isset($data->statusperkawinan)?$data->statusperkawinan:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Agama : </span>
                    <br>
                    <span class="global-size"><?= isset($data->agama)?$data->agama:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">No. Asuransi : </span>
                    <br>
                    <span class="global-size"><?= isset($data->no_asuransi_pasien)?$data->no_asuransi_pasien:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td>
                    <span class="global-size" style=" font-weight: bold">Pemberitahuan : </span>
                    <br>
                    <span class="global-size"><?= isset($data->penanggungjawab_nama)?$data->penanggungjawab_nama:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Hubungan : </span>
                    <br>
                    <span class="global-size"><?= $hubungan;?> &nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Telp : </span>
                    <br>
                    <span class="global-size"><?= isset($data->no_telepon_pasien)?$data->no_telepon_pasien:''; ?>&nbsp;</span>
                </td>
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Alamat : </span>
                    <br>
                    <span class="global-size"><?= isset($data->penanggungjawab_alamat)?$data->penanggungjawab_alamat:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="3">
                    <span class="global-size" style=" font-weight: bold">Pendidikan Pasien : </span>
                    <br>
                    <span class="global-size"><?= isset($data->pendidikan_pasien)?$data->pendidikan_pasien:''; ?>&nbsp;</span>
                </td>
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Prosedur Masuk RS : </span>
                    <br>
                    <span class="global-size"><?= isset($data->prosedurmasuk_rs)?$data->prosedurmasuk_rs:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td>
                    <span class="global-size" style=" font-weight: bold">Pekerjaan : </span>
                    <br>
                    <span class="global-size"><?= isset($data->pekerjaan_nama)?$data->pekerjaan_nama:''; ?>&nbsp;</span>
                </td>
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Alamat Kantor : </span>
                    <br>
                    <span class="global-size"><?= isset($data->alamat_kantor)?$data->alamat_kantor:''; ?>&nbsp;</span>
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Dokter Pengirim : </span>
                    <br>
                    <span class="global-size"><?= isset($data->dokter_pengirim)?$data->dokter_pengirim:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="3">
                    <span class="global-size" style=" font-weight: bold">Diagnosa Masuk : </span>
                    <br>
                    <span class="global-size"><?= isset($data->diagnosa_masuk)?$data->diagnosa_masuk:''; ?>&nbsp;</span>
                </td>
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Gol. Darah : </span>
                    <br>
                    <span class="global-size"><?= isset($data->golongandarah)?$data->golongandarah:''; ?>&nbsp;</span>
                    <br>
                    <span class="global-size" style=" font-weight: bold">Alergi : </span>
                    <br>
                    <span class="global-size"><?= $alergi; ?></span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Kelas : </span>
                    <br>
                    <span class="global-size"><?= isset($data->kelas)?$data->kelas:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Ruang Rawat : </span>
                    <br>
                    <span class="global-size"><?= isset($data->ruangan_nama)?$data->ruangan_nama:''; ?>&nbsp;</span>
                </td>
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">No. Tempat Tidur : </span>
                    <br>
                    <span class="global-size"><?= isset($data->no_tempattidur)?$data->no_tempattidur:''; ?>&nbsp;</span>
                </td>
            </tr>
        </tbody>
    </table>

<br>

    <table class="table table-striped table-condensed table-hover" style="width:100%;">
        <thead>
        </thead>
        <tbody>
            <tr class="td-inverse">
                <td colspan="3">
                    <span class="global-size" style="font-weight: bold">Diagnosis Utama : </span>
                    <br>
                    <span class="global-size"><?= isset($data->diag_utama)?$data->diag_utama:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold;">Kode Penyakit </span>
                    <br>
                    <span class="global-size"><?= isset($data->diag_utama_kode)?$data->diag_utama_kode:''; ?>&nbsp;</span>
                </td>
                <td rowspan="4" class="top-aligned-row">
                    <span class="global-size" style=" font-weight: bold">Status Pulang : </span>
                    <br>
                    <!-- <span class="global-size"><?= isset($data->carakeluar_nama)?$data->carakeluar_nama:''; ?>&nbsp;</span> -->
                    <span class="global-size" style="font-size:12px;">
                        <ul>1. Dipulangkan</ul>
                        <ul>2. Atas Permintaan Sendiri</ul>
                        <ul>3. Melarikan Diri</ul>
                        <ul>4. Dirujuk Ke</ul>
                        <ul>5. Selesai Observasi</ul>
                        <ul>6. Meninggal : &nbsp; < 48 Jam &nbsp; > 48 Jam</ul>
                        <ul>7. Pindah RS. Lain</ul>
                        <ul>8. Cuti Sakit : ..... Hari</ul>
                    </span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="3">
                    <span class="global-size" style=" font-weight: bold;">Diagnosis Sekunder : </span>
                    <br>
                    <span class="global-size"><?= isset($data->diag_penyerta)?$data->diag_penyerta:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold"> </span>
                    <br>
                    <span class="global-size"><?= isset($data->diag_penyerta_kode)?$data->diag_penyerta_kode:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="3">
                    <span class="global-size" style=" font-weight: bold;">Penyulit : </span>
                    <br>
                    <span class="global-size"><?= isset($data->penyulit)?$data->penyulit:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold"> </span>
                    <br>
                    <span class="global-size">&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold;">Penyebab Kematian : </span>
                    <br>
                    <span class="global-size"><?= isset($data->penyebab_kematian)?$data->penyebab_kematian:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Tgl/Jam :</span>
                    <br>
                    <span class="global-size">&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold"> </span>
                    <br>
                    <span class="global-size">&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="3">
                    <span class="global-size" style=" font-weight: bold;">Operasi/Tindakan Khusus Tinggal : </span>
                    <br>
                    <span class="global-size"><?= isset($data->operasi_tindakan)?$data->operasi_tindakan:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold"> </span>
                    <br>
                    <span class="global-size">&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Lama Dirawat : </span>
                    <br>
                    <span class="global-size"><?= isset($data->lama_rawat)?$data->lama_rawat:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="3">
                    <span class="global-size" style=" font-weight: bold;">Infeksi Nosokomial : </span>
                    <br>
                    <span class="global-size"><?= isset($data->infeksi_nosokomial)?$data->infeksi_nosokomial:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold"> </span>
                    <br>
                    <span class="global-size">&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Tgl Keluar : </span>
                    <br>
                    <span class="global-size"><?= isset($data->tgl_keluar)?date('j M Y H:i:s', strtotime($data->tgl_keluar)):''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="2" style="text-align: center;">
                    <span class="global-size" style=" font-weight: bold;">Keadaan Keluar</span>
                </td>
                <td style="text-align: center;">
                    <span class="global-size" style=" font-weight: bold;"> Prognosis </span>
                </td>
                <td colspan="2" rowspan="5" class="top-aligned-row" style=" text-align: center;">
                    <span class="global-size"><?= $lokasi; ?> </span>
                    <br>
                    <br>
                    <span class="global-size">A.n Direktur <?= !empty($nama_rs) ? $nama_rs : '-' ?></span>
                    <br>
                    <span class="global-size">Dokter Yang Merawat</span>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <br>
                    <span class="global-size border-bottom" ><?= isset($data->nama_dokter)?$data->nama_dokter:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td style="text-align: center;">
                    <span class="global-size" style=" font-weight: bold;">Sembuh</span>
                </td>
                <td style="text-align: center;">
                    <span class="global-size" style=" font-weight: bold"> Baik </span>
                </td>
                <td rowspan="2" class="top-aligned-row">
                    <span class="global-size">
                        <ul>Sangat Memuaskan</ul>
                        <ul>Baik</ul>
                        <ul>Sedikit Harapan</ul>
                        <ul>Tidak Ada Harapan</ul>
                    </span>
                    &nbsp;
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="2">
                    <span class="global-size"><?= isset($data->infeksi_nosokomial)?$data->infeksi_nosokomial:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="3">
                    <span class="global-size" style=" font-weight: bold;">Transfusi Darah</span>
                    &nbsp;
                    <label class="checkbox-inline ">
                    <input type="checkbox" value=""><span class="global-size">Ya</span>
                    </label>
                    &nbsp;
                    <label class="checkbox-inline">
                    <input type="checkbox" value=""><span class="global-size">Tidak</span>
                    </label>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="3">
                    <span class="global-size" style=" font-weight: bold;">Pengobatan Dengan Radioterapi/Nuklir : </span>
                    &nbsp;
                    <label class="checkbox-inline ">
                    <input type="checkbox" value=""><span class="global-size">Ya</span>
                    </label>
                    &nbsp;
                    <label class="checkbox-inline">
                    <input type="checkbox" value=""><span class="global-size">Tidak</span>
                </td>
            </tr>
        </tbody>
    </table>
</div>
