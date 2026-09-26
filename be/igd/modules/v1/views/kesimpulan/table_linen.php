<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2019-02-06 17:21:58
 * @Last Modified by:   Rizqi Fitrianto
 * @Last Modified time: 2019-02-06 18:08:23
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
    if(count($linen) > 0){
        ?>
        <h3><b>Linen</b></h3>
        <table class="tbl tbl-bordered" style="width: 100%">
            <thead>
                <tr>
                    <th width="1">No</th>
                    <th>Nama Linen</th>
                    <th>Qty</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $no = 1;
                foreach ($linen as $key => $value) {
                    ?>
                    <tr>
                        <td><?=$no?></td>
                        <td><?=$value['nama_alat']?></td>
                        <td class="text-center"><?=$value['qty']?></td>
                    </tr>
                    <?php
                    $no++;
                }
                ?>
            </tbody>
        </table>
        <?php
    }
    ?>
</div>