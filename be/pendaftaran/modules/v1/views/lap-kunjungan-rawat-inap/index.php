<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-03-15 17:40:36
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-15 17:49:49
 */

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
<table class="tbl-bordered" cellpadding="1" cellspacing="1" style="width:100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Tanggal Pendaftaran</th>
            <th>Info Kunjungan</th>
            <?php 
            foreach ($header as $key => $value) {
                ?>
                <th><?=$value['title']?></th>
                <?php
            }
            ?>
        </tr>
    </thead>
    <tbody>
        <?php $no = 1;
        foreach ($data as $index => $value) {
            ?>
            <tr>
                <td><?=$no?></td>
                <td><?=DocoHelpers::convDateTime($value->tgl_pendaftaran)?></td>
                <td><?=$value->no_pendaftaran. ' <br> '.$value->no_rekam_medik. ' - '.(($value->namadepan) ? $value->namadepan . " " : '').$value->nama_pasien?></td>
                <?php
                 $statusTitipan = '-';
                 if($value->carabayar_id == 6){
                     $value->kelaspelayanan_nama = $value->kelaspelayanan_nama.' / '.$statusTitipan;
                 } else if (!empty($value->is_pasientitipan_pk)) {
                     if($value->is_pasientitipan_pk == true && $value->is_stoppasientitipan == false){
                         $statusTitipan = $value->kelas_ditagihkan_nama;
                        }
                        $value->kelaspelayanan_nama = $value->kelaspelayanan_nama.' / '.$statusTitipan;
                 } else if (empty($value->is_pasientitipan_pk)) {
                     if($value->is_pasientitipan == true && $value->is_stoppasientitipan == false){
                         $statusTitipan = $value->kelas_ditagihkan_nama;
                        }
                        $value->kelaspelayanan_nama = $value->kelaspelayanan_nama.' / '.$statusTitipan;
                 } 
                foreach ($header as $k => $v) {
                    if($v['row'] == 'carabayar_penjamin'){
                        ?>
                        <td><?=$value->carabayar_nama . ' / ' . $value->penjamin_nama?></td>
                        <?php
                    }else if($v['row'] == 'kamar_bed'){
                        ?>
                        <td><?=$value->kamarruangan_nokamar. ' - ' .$value->no_tempattidur ?></td>
                        <?php
                    }else{
                        ?>
                        <td><?=$value->$v['row']?></td>
                        <?php
                    }
                }
                ?>
            </tr>
            <?php
            $no++;
        } ?>
    </tbody>
</table>