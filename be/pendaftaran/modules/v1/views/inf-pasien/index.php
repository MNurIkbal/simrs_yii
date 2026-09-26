<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-05-22 14:08:34
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-05-22 14:22:48
 */
$keys = array_keys($detail[0]);
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

<table width="100%" class="tbl-bordered">
    <thead  style="font-size: 13px">
        <tr class="bg-inverse">
            <th width="1">No</th>
            <?php 
            if($jenis == 'ranap'){
                // unset header
                unset($keys[16]);
                unset($keys[17]);
                unset($keys[18]);
                unset($keys[19]);
                unset($keys[20]);
            }
            foreach ($keys as $value) :
                if($jenis =='ranap'){
                    $kelasTitipan = 'Kelas_tagihan';
                    switch ($value) {
                        case 'kelaspelayanan_nama':
                            $value = $value.' / '.$kelasTitipan;
                            break;
                    }
                }
                $title = str_replace('_', ' ', $value);
                ?>
                <th><?= Yii::t('app', ucfirst($title)) ?></th>
                <?php
            endforeach;
            ?>
        </tr>
    </thead>
    <tbody  style="font-size: 13px">
        <?php 
        $no = 1;
        $total = 0;
        foreach($detail as $value):
            if($jenis == 'ranap'){
                $statusTitipan = '-';
                $is_pasientitipan_pk = isset($value['is_pasientitipan_pk']) ? $value['is_pasientitipan_pk'] : false;
                if (!empty($is_pasientitipan_pk)) {
                    if($is_pasientitipan_pk == true && $value['is_stoppasientitipan'] == false){
                        $statusTitipan = $value['kelas_ditagihkan_nama'];
                    }
                    $value['kelaspelayanan_nama'] =  $value['kelaspelayanan_nama'].' / '.$statusTitipan;
                } else if (empty($is_pasientitipan_pk)) {
                    if($value['is_pasientitipan'] == true && $value['is_stoppasientitipan'] == false){
                        $statusTitipan = $value['kelas_ditagihkan_nama'];
                    }
                    $value['kelaspelayanan_nama'] =  $value['kelaspelayanan_nama'].' / '.$statusTitipan;
                }
                $value['is_pasientitipan_pk'] = ($is_pasientitipan_pk) ? 'Ya' : 'Tidak';
            }
        ?>
        <tr>
            <td><?=$no?></td>
            <?php 
            foreach ($keys as $valuex) :
                ?>
                <td><?=$value[$valuex]?></td>
                <?php
            endforeach;
            ?>
        </tr>
        <?php 
        $no++;
        endforeach;
        ?>
    </tbody>
</table>