<?php

use yii\helpers\Html;

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-06-26 16:08:34
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-09-13 15:05:05
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
    }
    .badge {
        padding: 2px 6px 1px 6px;
        font-size: 10px;
        letter-spacing: 0.1px;
        vertical-align: baseline;
        background-color: transparent;
        border: 1px solid transparent;
        border-radius: 100px;
    }
    .bg-warning-400 {
        background-color: #FF7043;
        border-color: #FF7043;
        color: #fff;
    }
</style>
<div class="row">
    <div class="col-md-6">
        <table class="tbl-bordered" style="font: 12px">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=Yii::t('app', 'Tanggal periksa')?></th>
                    <th><?=Yii::t('app', 'Bagian tubuh')?></th>
                    <th><?=Yii::t('app', 'Bagian tubuh detail')?></th>
                    <th><?=Yii::t('app', 'Keterangan')?></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $no = 0;
                if(count($dataAnatomi) > 0):
                  foreach ($dataAnatomi as $key => $value):
                    $no++;
                ?>
                    <tr>
                        <td><?=$no?></td>
                        <td><?=$value['created_date']?></td>
                        <td><?=$value['bagian']?></td>
                        <td><?=$value['bagianDetail']?></td>
                        <td><?=$value['catatan_tubuh']?></td>
                    </tr>
                <?php
                  endforeach;
                  else:
                ?>
                    <tr>
                        <td colspan="5">Tidak ada data</td>
                    </tr>
                <?php
                  endif;
                ?>
                <!-- table data -->
            </tbody>
        </table>
    </div>
</div>
