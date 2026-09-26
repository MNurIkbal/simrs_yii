<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-03-27 17:31:57
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-03-27 17:37:30
 */
?>
<style type="text/css">
    .text-center{
        text-align: center;
    }
    .head-title{
        margin-bottom: -5px;
    }
    .tbl{
        border-collapse: collapse;
    }
    .tbl-no-border th{
        border: 0px;
        padding: 5px;
    }
    .tbl-no-border td{
        border: 0px;
        padding: 5px;
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
<div>
    <?php 
    if(count($data) > 0){
        ?>
        <h3><b>Pelayanan Jenazah</b></h3>
        <h4><b>Kondisi Pasien</b></h4>

        <table class="tbl tbl-no-" style="width: 100%">
            <tbody>
                <tr>
                    <td>Kondisi Pasien</td>
                    <td>:</td>
                    <td colspan="4"><?=@$datajenazah['kondisipasien']['kondisi']?></td>
                </tr>
                <tr>
                    <td>Nama Penanggung Jawab</td>
                    <td>:</td>
                    <td><?=@$datajenazah['kondisipasien']['nama_pj']?></td>
                    <td>Jenis Kelamin</td>
                    <td>:</td>
                    <td><?=@$datajenazah['kondisipasien']['jenis_kelamin_pj']?></td>
                </tr>
                <tr>
                    <td>Umur</td>
                    <td>:</td>
                    <td><?=@$datajenazah['kondisipasien']['nama_pj']?></td>
                    <td>No. Telp / Hp</td>
                    <td>:</td>
                    <td><?=@$datajenazah['kondisipasien']['jenis_kelamin_pj']?></td>
                </tr>
                <tr>
                    <td>Hubungan Keluarga</td>
                    <td>:</td>
                    <td><?=@$datajenazah['kondisipasien']['penanggungjawab_nama']?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>Alamat</td>
                    <td>:</td>
                    <td><?=@$datajenazah['kondisipasien']['alamat']?></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
            </tbody>
        </table>
        <?php
    }
    ?>
</div>