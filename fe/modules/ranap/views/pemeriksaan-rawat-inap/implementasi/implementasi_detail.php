<?php
use app\components\DocoHelpers;
?>

<div class="panel panel-default">
    <div class="panel-heading">
        <h6 class="panel-title">Detail Implementasi</h6>
    </div>
    <div class="panel-body">
      <div class="row">
            <div class="col-sm-12">
                <table class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th width="5%">No</th>
                            <th width="10%">Tanggal / Pukul</th>
                            <th width="15%">Implementasi</th>
                            <th width="15%">Petugas 1</th>
                            <th width="15%">petugas 2</th>
                            <th width="20%">Catatan Implementasi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if(!empty($listDetail)) :?>
                        
                        <?php foreach ($listDetail as $k => $v) : ?>
                            <tr>
                                <td><?= $k + 1?></td>
                                <td><?= isset($v['tgl_implementasi']) ? DocoHelpers::convDateTime($v['tgl_implementasi'], false, true) : '-' ?></td>
                                <td><?= isset($v['implementasi']) ? $v['implementasi'] : '-' ?></td>
                                <td><?= isset($v['perawat_1']) ? $v['perawat_1'] : '-' ?></td>
                                <td><?= isset($v['perawat_2']) ? $v['perawat_2'] : '-' ?></td>
                                <td><?= isset($v['catatan_implementasi']) ? $v['catatan_implementasi'] : '-' ?></td>
                            </tr>
                        <?php endforeach ?>
                        <?php else : ?>
                            <tr>
                                 <td>Tidak Ada Data</td>
                            </tr>
                        <?php endif?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>