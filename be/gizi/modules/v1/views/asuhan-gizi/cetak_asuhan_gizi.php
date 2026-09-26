<?php

/**
 * @Author: rizal
 * @Date:   2018-11-23 10:20:03
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-01-09 13:40:42
 */

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
        vertical-align: text-top;
    }
    .tbl-detail {
        border: 0px;
        padding: 5px;
    }
    .tbl-detail td {
        border: 0px;
        padding: 5px;
    }
</style>

<table width="100%" class="tbl-bordered">
    <tbody>
        <tr>
            <th align="center">RIWAYAT PERSONAL</th>
        </tr>
        <tr>
            <td>
                <table class="tbl-detail">
                    <tr >
                        <td>Riwayat Penyakit Keluarga</td>
                        <td>:</td>
                        <td width="50%">
                            <?php
                            echo $r_penyakitkeluarga;
                            ?>
                        </td>
                        <td>Status Merokok</td>
                        <td>:</td>
                        <td><?= $model['is_merokok'] ? 'Ya, ' . $model['jml_rokok'] . ' batang rokok per hari' : 'Tidak'; ?></td>
                    </tr>
                    <tr>
                        <td>Riwayat Sosial Ekonomi</td>
                        <td>:</td>
                        <td><?= $peskk; ?></td>
                        <td>Diagnosa Dokter</td>
                        <td>:</td>
                        <td><?= $diagnosa; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th align="center">RIWAYAT GIZI DAN MAKANAN</th>
        </tr>
        <tr>
            <?php
            $temp = json_decode($model['gizi_makanan'], true);
            ?>
            <td>
                <table class="tbl-detail">
                   <tr>
                       <td>Alergi Makanan</td>
                       <td>:</td>
                       <td><?= $temp['alergi_makanan'] ?></td>
                   </tr>
                   <tr>
                       <td>Pantangan Makanan</td>
                       <td>:</td>
                       <td><?= $temp['pantangan_makanan'] ?></td>
                   </tr>
                   <tr>
                       <td>Ketidaksukaan Makanan</td>
                       <td>:</td>
                       <td><?= $temp['ketidaksukaan_makanan'] ?></td>
                   </tr>
                   <tr>
                       <td>Pengalaman Diet / Konseling Sebelumnya</td>
                       <td>:</td>
                       <td><?= $temp['pengalaman_diet'] == 1 ? 'Ya, ' . $temp['pengalaman_diet_desc'] : 'Tidak' ?></td>
                   </tr>
               </table>
            </td>
        </tr>
        <tr>
            <th align="center">ANTROPOMETRI</th>
        </tr>
        <tr>
            <?php
            $temp = json_decode($model['antropometri'], true);

            if (isset($temp['bb_saatini']) && $temp['bb_saatini'] != '') {
                if(strpos($temp['bb_saatini'], '.') !== false) {
                    $temp['bb_saatini'] = str_replace('.', ',', $temp['bb_saatini']);
                } else {
                    if(strpos($temp['bb_saatini'], ',') !== false) {
                        
                    } else {
                        $temp['bb_saatini'] = $temp['bb_saatini'].',00';
                    }
                }
            }

            if (isset($temp['imt']) && $temp['imt'] != '') {
                if(strpos($temp['imt'], '.') !== false) {
                    $temp['imt'] = str_replace('.', ',', $temp['imt']);
                } else {
                    if(strpos($temp['imt'], ',') !== false) {
                        
                    } else {
                        $temp['imt'] = $temp['imt'].',00';
                    }
                }
            }

            if (isset($temp['bb_biasanya']) && $temp['bb_biasanya'] != '') {
                if(strpos($temp['bb_biasanya'], '.') !== false) {
                    $temp['bb_biasanya'] = str_replace('.', ',', $temp['bb_biasanya']);
                } else {
                    if(strpos($temp['bb_biasanya'], ',') !== false) {
                        
                    } else {
                        $temp['bb_biasanya'] = $temp['bb_biasanya'].',00';
                    }
                }
            }

            if (isset($temp['penurunan_bb']) && $temp['penurunan_bb'] != '') {
                if(strpos($temp['penurunan_bb'], '.') !== false) {
                    $temp['penurunan_bb'] = str_replace('.', ',', $temp['penurunan_bb']);
                } else {
                    if(strpos($temp['penurunan_bb'], ',') !== false) {
                        
                    } else {
                        $temp['penurunan_bb'] = $temp['penurunan_bb'].',00';
                    }
                }
            }
            ?>
            <td>
                <table class="tbl-detail">
                   <tr>
                       <td>Riwayat Penurunan BB</td>
                       <td>BB Saat ini</td>
                       <td>:</td>
                       <td><?= $temp['bb_saatini']?> Kg</td>
                       <td>PB / TB</td>
                       <td>:</td>
                       <td><?= $temp['pb_tb'] ?>cm</td>
                       <td>IMT</td>
                       <td>:</td>
                       <td><?= $temp['imt'] ?></td>
                       <td>Status Gizi</td>
                       <td>:</td>
                       <td><?= $temp['status_gizi'] ?></td>
                   </tr>
                   <tr>
                       <td></td>
                       <td>BB Biasanya</td>
                       <td>:</td>
                       <td><?= $temp['bb_biasanya'] ?> Kg</td>
                       <td>Penurunan BB</td>
                       <td>:</td>
                       <td><?= $temp['penurunan_bb'] ?> %</td>
                       <td>Kurun Waktu</td>
                       <td>:</td>
                       <td><?= $temp['kurun_waktu'] ?> Minggu</td>
                   </tr>
                   <tr>
                       <td>Pengukuran Lainnya</td>
                       <td>: <?= $temp['pengukuran_lainnya'] ? : '-'; ?></td>
                   </tr>
               </table>
            </td>
        </tr>
        <tr>
            <th align="center">BIOKIMIA TERKAIT GIZI</th>
        </tr>
        <tr>
           <td>
                <?php
                $temp = json_decode($model['biokimia'], true);
                ?>
                <table class="tbl-detail">
                   <tr>
                       <td>Biokimia Terkait Gizi</td>
                       <td>:</td>
                       <td><?= $temp['biokimia'] ? : '-'; ?></td>
                   </tr>
                   <tr>
                       <td>Prosedur</td>
                       <td>:</td>
                       <td><?= $temp['prosedur'] ? : '-'; ?></td>
                   </tr>
               </table>
           </td>
        </tr>
        <tr>
            <th align="center">FISIK KLINIS GIZI</th>
        </tr>
        <tr>
           <td>
                <?php
                $temp = json_decode($model['fisikklinis_gizi'], true);
                ?>
                <table class="tbl-detail">
                   <tr>
                       <td width="20%">Antropi Otot Lengan</td>
                       <td>:</td>
                       <td><?= $temp['antropi_otot_lengan'] ? 'Ada' : 'Tidak'; ?></td>
                       <td width="20%">Udem</td>
                       <td>:</td>
                       <td width="20%"><?= $temp['udem'] ? 'Ada' : 'Tidak'; ?></td>
                       <td width="20%">Hilang Lemak Subkutan</td>
                       <td>:</td>
                       <td><?= $temp['hilang_lemak_subkutan'] ? 'Ada' : 'Tidak'; ?></td>
                   </tr>
                   <tr>
                       <td>Nafsu Makan</td>
                       <td>:</td>
                       <td><?= $temp['nafsu_makan'] ? 'Ada' : 'Tidak'; ?></td>
                       <td>Mual</td>
                       <td>:</td>
                       <td><?= $temp['mual'] ? 'Ada' : 'Tidak'; ?></td>
                       <td>Muntah</td>
                       <td>:</td>
                       <td><?= $temp['muntah'] ? 'Ada' : 'Tidak'; ?></td>
                   </tr>
                   <tr>
                       <td>Kembung</td>
                       <td>:</td>
                       <td><?= $temp['kembung'] ? 'Ada' : 'Tidak'; ?></td>
                       <td>Konstipasi</td>
                       <td>:</td>
                       <td><?= $temp['konstipasi'] ? 'Ada' : 'Tidak'; ?></td>
                       <td>Diare</td>
                       <td>:</td>
                       <td><?= $temp['diare'] ? 'Ada' : 'Tidak'; ?></td>
                   </tr>
                   <tr>
                       <td>Gangguan Menelan</td>
                       <td>:</td>
                       <td><?= $temp['gangguan_menelan'] ? 'Ada' : 'Tidak'; ?></td>
                       <td>Gangguan Mengunyah</td>
                       <td>:</td>
                       <td><?= $temp['gangguan_mengunyah'] ? 'Ada' : 'Tidak'; ?></td>
                       <td>Gangguan Menghisap</td>
                       <td>:</td>
                       <td><?= $temp['gangguan_menghisap'] ? 'Ada' : 'Tidak'; ?></td>
                   </tr>
                   <tr>
                       <td>Kulit</td>
                       <td>:</td>
                       <td><?= $temp['kulit']; ?></td>
                   </tr>
                   <tr>
                       <td>Kepala & Mata</td>
                       <td>:</td>
                       <td><?= $temp['kepala_dan_mata']; ?></td>
                   </tr>
                   <tr>
                       <td>Gigi Geligi</td>
                       <td>:</td>
                       <td><?= $temp['gigi_geligi']; ?></td>
                   </tr>
                </table>
                <strong>Tanda-Tanda Vital</strong>
                <table class="tbl-detail">
                   <tr>
                       <td>Tekanan Darah</td>
                       <td>: <?= $temp['tekanan_darah_mmhg']; ?> /MmHg</td>
                       <td>Detak Nadi</td>
                       <td>:</td>
                       <td><?= $temp['detak_nadi']; ?> / Menit</td>
                       <td>Denyut Jantung</td>
                       <td>:</td>
                       <td><?= $temp['denyut_jantung']; ?></td>
                   </tr>
                   <tr>
                       <td></td>
                       <td width="25%"><?= $temp['tekanan_darah_kondisi']; ?></td>
                       <td>Pernapasan</td>
                       <td>:</td>
                       <td><?= $temp['pernapasan']; ?> / Menit</td>
                       <td>Suhu Tubuh</td>
                       <td>:</td>
                       <td><?= $temp['suhu_tubuh']; ?>&#8451;</td>
                   </tr>
                   <tr>
                     <td>Data Lain</td>
                     <td><?= isset($temp['data_lain']) ? $temp['data_lain'] : ''; ?></td>
                   </tr>
                </table>
           </td>
        </tr>
        <tr>
            <th align="center">DIAGNOSA GIZI</th>
        </tr>
        <tr>
            <td>
                <?php $temp = json_decode($model['diagnosa_gizi'], true); ?>
                <table class="tbl-detail">
                    
                    <tr>
                      <td>Diagnosa Gizi</td>
                      <td>:</td>
                      <td><?= implode(', ', explode(',', $temp['diagnosa_gizi'])); ?></td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <th align="center">INTERVENSI GIZI</th>
        </tr>
        <?php $temp = json_decode($model['intervensi_gizi'], true); ?>
        <tr>
            <td>
                <table class="tbl-bordered">
                    <tr>
                        <td width="505px" >
                            <table class="tbl-detail">
                                <tr>
                                    <td width="130px">Tujuan</td>
                                    <td>:</td>
                                    <td><?= $temp['tujuan']; ?></td>
                                </tr>
                                <tr>
                                    <td>Materi</td>
                                    <td>:</td>
                                    <td><?= $temp['materi']; ?></td>
                                </tr>
                                <tr>
                                    <td>Media</td>
                                    <td>:</td>
                                    <td><?= $temp['media']; ?></td>
                                </tr>
                                <tr>
                                    <td>Sasaran</td>
                                    <td>:</td>
                                    <td><?= $temp['sasaran']; ?></td>
                                </tr>
                            </table>
                        </td>
                        <td width="505px">
                            <table class="tbl-detail">
                                <tr>
                                    <td width="190px">Edukasi / Konseling Gizi</td>
                                    <td>:</td>
                                    <td><?= $temp['edukasi_gizi']; ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <table class="tbl-detail">
                                <tr>
                                    <td width="130px">Preskripsi Diet</td>
                                    <td> : <?= $temp['preskripsi_diet']; ?></td>
                                </tr>
                                <tr>
                                    <td>Jenis Diet</td>
                                    <td> : <?= implode(', ', $temp['jenis_diet']); ?></td>
                                </tr>
                                <tr>
                                    <td>Rute</td>
                                    <td> : <?= $temp['rute']; ?></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table class="tbl-detail">
                                <tr>
                                    <td width="190px">Target Intervensi</td>
                                    <td> : <?= isset($temp['target_intervensi']) ? $temp['target_intervensi'] : ''; ?></td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>

        <tr>
            <th align="center">RENCANA MONITORING EVALUASI GIZI</th>
        </tr>
        <tr>
            <?php $temp = json_decode($model['rencana_gizi'], true); ?>
            <td>
                <table class="tbl-detail">
                    <tr>
                        <td>Rencana Evaluasi Gizi</td>
                        <td> : <?= $temp['rencana_evaluasi']; ?></td>
                    </tr>
                </table>
            </td>
        </tr>
    </tbody>
</table>