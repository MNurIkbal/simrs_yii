<?php
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
            <th>Status Pasien</th>
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
                <td><?=$value->no_pendaftaran. ' <br> '.$value->no_rekam_medik. ' - '. (($value->namadepan) ? $value->namadepan . " " : '') .$value->nama_pasien?></td>
                <td><?=$value->status_pasien?></td>
                <?php 
                foreach ($header as $k => $v) {
                    if($v['row'] == 'carabayar_penjamin'){
                        ?>
                        <td><?=$value->carabayar_nama . ' / ' . $value->penjamin_nama?></td>
                        <?php
                    }else if($v['row'] == 'status_pulang'){
                        if($value->carakeluar_nama != '' && $value->kondisikeluar_nama){
                            $statusPulang = $value->carakeluar_nama . ' / ' . $value->kondisikeluar_nama;
                        }elseif(($value->carakeluar_nama != '') || ($value->kondisikeluar_nama != '')){
                            if($value->carakeluar_nama != ''){
                                $statusPulang = $value['carakeluar_nama'];
                            }else{
                                $statusPulang = $value['kondisikeluar_nama'];
                            }
                        }
                        else{
                            $statusPulang = '';
                        } 
                        ?>
                        <td><?=$statusPulang?></td>
                        <?php
                    } else if ($v['row'] == 'no_identitas_pasien' && $v['title'] == 'NIK') {
                        ?>
                        <td><?= ($value['jenisidentitas'] == 'KTP') ? $value->$v['row'] : '-';?></td>
                        <?php
                    } else {
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