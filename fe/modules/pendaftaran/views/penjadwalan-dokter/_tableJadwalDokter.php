<?php

use yii\helpers\Url;
use yii\helpers\Html;
?>

<div class="table-responsive pre-scrollable" style="max-height:70vh; overflow:auto;">
    <table 
            class="table table-xs table-bordered"
            spam="datatable-basic table-striped table-hover dataTable no-footer" 
            id="data-jadwaldokter" 
            style="width: 100%;"
            >
        <thead>
            <tr>
                <th></th>
                <th></th>
                <th></th>
                <th></th>
                <?php foreach ($listJam as $jam): ?>
                    <th style='padding-left:3px; padding-right:3px;'>
                        <?= $jam; ?>
                    </th>
                <?php endforeach; ?>
            </tr>
        </thead>

        <tbody>
            <?php $lastRuangan = ''; ?>
            <?php foreach ($list as $ruangan=>$listDokter): ?>
                <?php $countRuangan = count($list[$ruangan]); ?>
                <?php foreach($listDokter as $namaDokter => $listHari): ?>
                    <?php $countDokter = count($namaDokter); ?>
                    <?php foreach ($listHari as $hari => $listJadwal): ?>
                    <tr>
                        <?php 
                        $rowspan = $countRuangan * $countDokter * 7;
                        ?>
                        <?php if ($lastRuangan != $ruangan): ?>
                            <td rowspan=<?= $rowspan; ?>><?= $ruangan; ?></td>
                        <?php endif;?>
                        <td style="white-space:nowrap;"><?= $namaDokter; ?></td>
                        <td><?= $hari; ?></td>
                        <td>
                            <?php
                            $url = Url::home();
                            $url .= 'master/jadwal-dokter/create';
                            $url .= '?instalasi_id=' . $listJadwal['instalasi_id'];
                            $url .= '&ruangan_id=' . $listJadwal['ruangan_id'];
                            $url .= '&pegawai_id=' . $listJadwal['pegawai_id'];
                            $url .= '&hari_id=' . $listJadwal['hari_id'];
                            echo Html::button(
                                '<i class="fa fa-plus"></i>&nbsp;',
                                [
                                    'class' => 'btn btn-default btn-xs addJadwalDokter',
                                    'action' => $url,
                                    'style' => 'margin-right:5px;',
                                    'data-toggle' => 'modal',
                                    'data-target' => '#modal_backdrop',
                                ]
                            );
                            ?>
                        </td>
                        
                        <?php 
                        $colspan = 0; 
                        $kuota = '';
                        $attr = '';
                        ?>
                        <?php foreach ($listJam as $jam): ?>
                            <?php
                            $fJam = date('H:i', strtotime($jam));
                            $classname = '';
                            $flag = false;
                            foreach ($listJadwal as $jadwal) {
                                $mulai = $jadwal['mulai'];
                                $tutup = $jadwal['tutup'];
                                $fMulai = date('H:i', strtotime($mulai));
                                $fTutup = date('H:i', strtotime($tutup));
                                $url = Url::home() . 'master/jadwal-dokter/update?id=' . $jadwal['id'];
                                if ($fJam >= $fMulai && $fJam <= $fTutup) {
                                    $flag = true;
                                    $colspan++;
                                    $attr = "
                                        class='bg-info'
                                        colspan=" . $colspan . "
                                        action='" . $url . "'
                                        data-toggle='modal'
                                        data-target='#modal_backdrop'
                                    ";
                                    $kuota = $jadwal['kuota'];
                                    if ($jam == '21.00') $flag = false;
                                    break;
                                }
                            }
                            ?>
                            <?php if($flag == false): ?>
                                <td <?= $attr; ?>><?= $kuota ? 'Kuota ' . $kuota : ''; ?></td>
                                <?php 
                                $colspan = 0; 
                                $kuota = '';
                                $attr = '';
                                ?>
                            <?php endif; ?>
                            <?php $lastRuangan = $ruangan; ?>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
            <?php endforeach; ?>
        </tbody>

    </table>
</div>

<script>
$('[data-toggle="popover"]').popover();

function update(id) {

}
</script>