<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>
<style>
    table {
        border-collapse: collapse;
        border-spacing: 0;
    }

    .global-size {
        font-size: 12px;
    }
   .bg-inverse th, .td-inverse td {
    border: 1px solid #000000;
    padding: 5px;
    text-align: left;
  }

  textarea { 
    border-style: none; 
    border-color: Transparent; 
    overflow: auto;     
    background-color: white;   
  }

    .top-aligned-row {
      vertical-align: top; 
    }
    
</style>
<div>
    <table class="table table-striped table-condensed table-hover" style="width:100%;" >
        <thead>
            <tr class="bg-inverse">
                <th colspan="3" width="1"  align="left" style="font-size:11px; font-weight: bold; border-right: 0; ">A.IDENTIFIKASI PASIEN</th>
                <th colspan="2" width="1"  align="right" style="font-size:15px; font-weight: bold;"><?= $title; ?></th>
            </tr>
        </thead>
        <tbody>
            <tr class="td-inverse">
                <td>
                    <span class="global-size" style=" font-weight: bold;">No. RM : </span>
                    <br>
                    <br>
                    <span class="global-size"><?= isset($data->no_rekam_medik)?$data->no_rekam_medik:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">No. Registrasi : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->no_registrasi)?$data->no_registrasi:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Tgl Registrasi : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->tgl_registrasi)?date('j M Y H:i:s', strtotime($data->tgl_registrasi)):''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Tgl Cetak : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= !empty($tgl_cetak) ? $tgl_cetak : '' ?></span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Dokter : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->nama_dokter)?$data->nama_dokter:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse"> 
                <td rowspan="3" colspan="2" class="top-aligned-row">
                    <span class="global-size" style=" font-weight: bold">Nama & Alamat </span>
                    <br>
                    <br>
                    <span class="global-size" style=" font-weight: bold"><?= isset($data->nama_pasien)?$data->nama_pasien:''; ?>&nbsp;</span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->alamat_pasien)?$data->alamat_pasien:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Tempat & Tgl Lahir: </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= !empty($ttl) ? $ttl : ''; ?> &nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Umur : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->umur)? $data->umur:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Jenis Kelamin : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->jeniskelamin)?$data->jeniskelamin:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse"> 
                <td>
                    <span class="global-size" style=" font-weight: bold">No. KTP : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= !empty($ktp) ? $ktp : ''; ?></span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Kebangsaan : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->kebangsaan)?$data->kebangsaan:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Suku : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->suku_nama)?$data->suku_nama: ''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse"> 
                <td>
                    <span class="global-size" style=" font-weight: bold">Status : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->statusperkawinan)?$data->statusperkawinan:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Agama : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->agama)?$data->agama:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">No. Asuransi : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->no_asuransi_pasien)?$data->no_asuransi_pasien:'';?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td>
                    <span class="global-size" style=" font-weight: bold">Pemberitahuan : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->penanggungjawab_nama)?$data->penanggungjawab_nama:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Hubungan : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= !empty($hubungan) ? $hubungan : '';?> &nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Telp : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->no_telepon_pasien)?$data->no_telepon_pasien:''; ?>&nbsp;</span>
                </td>
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Alamat : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->penanggungjawab_alamat)?$data->penanggungjawab_alamat:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="5">
                    <span class="global-size" style=" font-weight: bold">Pendidikan Pasien : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->pendidikan_nama)?$data->pendidikan_nama:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td>
                    <span class="global-size" style=" font-weight: bold">Pekerjaan : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->pekerjaan_nama)?$data->pekerjaan_nama:''; ?>&nbsp;</span>
                </td>
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Alamat Kantor : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->alamat_kantor)?$data->alamat_kantor:''; ?>&nbsp;</span>
                </td>
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Dokter Pengirim : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->dokter_pengirim)?$data->dokter_pengirim:''; ?>&nbsp;</span>
                </td>
            </tr>
            <tr class="td-inverse">
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Gol. Darah : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->golongandarah)?$data->golongandarah:''; ?>&nbsp;</span>
                </td>
                <td>
                    <span class="global-size" style=" font-weight: bold">Diet : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= isset($data->diet)?$data->diet:''; ?>&nbsp;</span>
                <td colspan="2">
                    <span class="global-size" style=" font-weight: bold">Alergi : </span>
                    <br>
                    <br>
                    <span class="global-size" style=""><?= !empty($alergi) ? $alergi : $alergi; ?>&nbsp;</span>
                </td>
            </tr>
        </tbody>
    </table>
    <br>
    <table class="table table-striped table-condensed table-hover" style="width:100%;">
        <thead>
            <tr class="bg-inverse">
                <th colspan="8" align="left" style="font-size:11px; font-weight: bold; width: 50px">B.DATA DASAR MEDIS</th>
                <th colspan="4" width="1"  align="left" style="font-size:9px; font-weight: light;">Riwayat penyakit sekarang, riwayat penyakit dahulu, pemeriksaan fisik, pemeriksaan penunjang (Lab, Radiologi, Ekg, Echo, DLL)diagnosa kerja, penatalaksanaan</th>
            </tr>
        </thead>
        <tbody>
            <tr class="td-inverse">
                <td colspan="12" style="height: 300px;">
                </td>
            </tr>
        </tbody>
    </table>

</div>
