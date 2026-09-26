<?php
use yii\helpers\Html;
use app\components\DocoHelpers;
?>
<div class="row">
    <div class="col-md-12" id="lab-row">
        <h6 class="text-bold">Pemeriksaan Diagnostik / Hasil Pemeriksaan Penunjang</h6>
        <h6 class="text-bold">Laboratorium</h6>
        <table class="table table-bordered table-hover" id="resumemedis-table-lab">
            <thead>
                <tr class="bg-inverse">
                    <th>Tanggal Pemeriksaan</th>
                    <th>Jenis Pemeriksaan</th>
                    <th>Nama Pemeriksaan</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                if(!empty($labOrder)){
                    foreach($labOrder as $key => $order) {
                        ?>
                        <tr>
                            <td><?=Html::textInput('labrow['.$key.'][tgl_tindakan]', $order['tgl_tindakan'], ['class' => 'form-control input-select-date'])?></td>
                            <td><?=Html::textInput('labrow['.$key.'][jenis]', str_replace('_', ' ', $order['jenis']), ['class' => 'form-control'])?></td>
                            <td><?=Html::textInput('labrow['.$key.'][daftartindakan_nama]',$order['daftartindakan_nama'], ['class' => 'form-control'])?></td>
                            <td>
                                <button type="button" data-target="resumemedis-table-lab" class="btn btn-danger btn-sm btn-delete-item"><i class="fa fa-trash"></i></button>
                            </td>
                        </tr>
                        <?php
                    }
                } else {
                    ?>
                    <tr class="no-data-row">
                        <td colspan="4" class="text-center">Belum Ada Data</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td><?=Html::textInput('tgl_tindakan', '', ['class' => 'form-control input-select-date', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('jenis', '', ['class' => 'form-control jenis-pemeriksaan', 'style' => 'margin-bottom: 10px'])?></td>
                    <td><?=Html::textInput('daftartindakan_nama', '', ['class' => 'form-control nama-pemeriksaan', 'style' => 'margin-bottom: 10px'])?></td>
                    <td>
                        <button type="button" style="margin-bottom: 10px" class="btn btn-sm btn-info btn-add-item" data-target="resumemedis-table-lab" data-form="lab-row"><i class="fa fa-plus"></i></button>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>