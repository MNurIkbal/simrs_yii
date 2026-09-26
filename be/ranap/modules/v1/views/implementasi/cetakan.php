<style type="text/css">
    .tbl-bordered {
    border-collapse: collapse;
    }
    
    tr.strikeout td:before {
        content: " ";
        position: absolute;
        top: 50%;
        left: 0;
        border-bottom: 1px solid #111;
        width: 100%;
    }
    /*.tbl-bordered th {
        border: 1px solid black;
        padding: 5px;
    }
    .tbl-bordered td {
        border: 1px solid black;
        padding: 5px;
    }*/

</style>
<table width="100%" class="tbl-bordered" border="1">
    <thead>
        <tr>
            <th colspan="5"><?= \Yii::t("app", "Instruksi"); ?></th>
            <th colspan="6"><?= \Yii::t("app", "Implementasi"); ?></th>
        </tr>
        <tr>
            <th><?= \Yii::t("app", "No"); ?></th>
            <th><?= \Yii::t("app", "Tanggal/Pukul"); ?></th>
            <th><?= \Yii::t("app", "Instruksi Dokter"); ?></th>
            <th><?= \Yii::t("app", "Dokter"); ?></th>
            <th><?= \Yii::t("app", "Status"); ?></th>
            <th><?= \Yii::t("app", "No"); ?></th>
            <th><?= \Yii::t("app", "Tanggal/Pukul"); ?></th>
            <th><?= \Yii::t("app", "Implementasi"); ?></th>
            <th><?= \Yii::t("app", "Petugas 1"); ?></th>
            <th><?= \Yii::t("app", "Petugas 2"); ?></th>
            <th><?= \Yii::t("app", "Catatan Implementasi"); ?></th>
        </tr>
    </thead>
    <tbody>
            <?php
            $no=1;
        foreach ($data as $list_instruksi) {
            $is_implementasi = false;
            $countimplementasi = count($list_instruksi['data_implementasi']);
            $rowspan = '';
            $rowspanNum = '';
            if($countimplementasi>0){
                $is_implementasi = true;
                $rowspan = $countimplementasi+1;
                $rowspanNum = $rowspan+1;
            }else{
                $rowspanNum = 2;
            }
            ?>
            <tr>
                <td rowspan="<?=@$rowspanNum?>" style="border: 1px solid black;"><?= $no; ?></td>
                <?php 
                    $label_nama_grouping = '';
                    if($list_instruksi['grouping_tipe'] == 'TINDAKANBMHP'){
                        $label_nama_grouping = 'Tindakan & BMHP';
                    }else if($list_instruksi['grouping_tipe'] == 'RESEPTUR'){
                        $label_nama_grouping = 'Obat';
                    }else if($list_instruksi['grouping_tipe'] == 'PENUNJANG'){
                        $label_nama_grouping = 'Penunjang';
                    }
                ?>
                <td colspan="10" style="border: 1px solid black;">
                    Jenis Instruksi : <?=@$label_nama_grouping?> <br>
                    Catatan Instruksi : <?=@$list_instruksi['catatan_instruksi']?>
                </td>
            </tr>
            <tr>
                <td rowspan="<?=@$rowspan?>" style="border: 1px solid black;">
                    <table border="0" cellspacing="0">
                        <tbody>
                                <?php
                            foreach ($list_instruksi['data_instruksi'] as $instruksi) {
                                ?>
                                <tr>
                                        <?php 
                                        if($instruksi['instruksi_deleted'] == true){ ?>
                                    <td><strike><?=@$instruksi['tgl_instruksi']?></strike></td>
                                        <?php }else{ ?>
                                    <td><?=@$instruksi['tgl_instruksi']?></td>
                                        <?php }?>
                                </tr>
                                <?php 
                            } 
                                ?>
                        </tbody>
                    </table>
                </td>
                <td rowspan="<?=@$rowspan?>" style="border: 1px solid black;">
                    <table border="0" cellspacing="0" margin="0" padding="0" outline="0">
                        <tbody>
                                <?php
                            foreach ($list_instruksi['data_instruksi'] as $instruksi) {
                                ?>
                                <tr>
                                        <?php 
                                        if($instruksi['tindakan_deleted'] == true || $instruksi['instruksi_deleted'] == true){ ?>
                                    <td><strike>
                                        <?=@$instruksi['instruksi']?> <br>
                                        <?php
                                            if(isset($instruksi['paket'])){
                                                if(isset($instruksi['daftar_paket'])){
                                                    $list = json_decode($instruksi['daftar_paket'],TRUE);
                                                    echo "<ul>";
                                                    foreach ($list as $item_paket) {
                                                        echo "<li>".@$item_paket."</li>";
                                                    }
                                                    echo "</ul>";
                                                }
                                            }
                                        ?>
                                    </strike></td>
                                        <?php }else{ ?>
                                    <td>
                                        <?=@$instruksi['instruksi']?> <br>
                                        <?php
                                            if(isset($instruksi['paket'])){
                                                if(isset($instruksi['daftar_paket'])){
                                                    $list = json_decode($instruksi['daftar_paket'],TRUE);
                                                    echo "<ul>";
                                                    foreach ($list as $item_paket) {
                                                        echo "<li>".@$item_paket."</li>";
                                                    }
                                                    echo "</ul>";
                                                }
                                            }
                                        ?>
                                    </td>
                                        <?php }?>
                                </tr>
                                <?php 
                            } 
                                ?>
                        </tbody>
                    </table>
                </td>
                <td rowspan="<?=@$rowspan?>" style="border: 1px solid black;">
                    <table border="0" cellspacing="0" margin="0" padding="0" outline="0">
                        <tbody>
                                <?php
                            foreach ($list_instruksi['data_instruksi'] as $instruksi) {
                                ?>
                                <tr>
                                        <?php 
                                        if($instruksi['tindakan_deleted'] == true || $instruksi['instruksi_deleted'] == true){ ?>
                                    <td><strike><?=@$instruksi['dokter']?></strike></td>
                                        <?php }else{ ?>
                                    <td><?=@$instruksi['dokter']?></td>
                                        <?php }?>
                                </tr>
                                <?php 
                            } 
                                ?>
                        </tbody>
                    </table>
                </td>
                <td rowspan="<?=@$rowspan?>" style="border: 1px solid black;">
                    <table border="0" cellspacing="0" margin="0" padding="0" outline="0">
                        <tbody>
                                <?php
                            foreach ($list_instruksi['data_instruksi'] as $instruksi) {
                                ?>
                                <tr>
                                        <?php 
                                        if($instruksi['tindakan_deleted'] == true || $instruksi['instruksi_deleted'] == true){ ?>
                                    <td><strike>TERHAPUS</strike></td>
                                        <?php }else{ ?>
                                    <td><?=@$instruksi['status']?></td>
                                        <?php }?>
                                </tr>
                                <?php 
                            } 
                                ?>
                        </tbody>
                    </table>
                </td>
                <?php
                if($is_implementasi){
                    $no++;
                    ?>
                    <td colspan="6" style="border: 1px solid black;">&nbsp;</td>
                    <?php
                }else{
                    ?>
                    <td colspan="6" style="border: 1px solid black;">Belum Ada Implementasi</td>
                    <?php
                }
                ?>
            </tr>
            <?php
            if($is_implementasi){
                $groupDataImplementasi =[];
                foreach ($list_instruksi['data_implementasi'] as $implementasi) {
                    $groupDataImplementasi[$implementasi['instruksi_id']][] = $implementasi;
                }
                $cnImplemen = 1;
                foreach ($groupDataImplementasi as $ins_id => $list_implementasi) {
                    $hitungImplementasi = count($list_implementasi);
                    foreach ($list_implementasi as $implementasi) {
                        ?>
                        <tr>
                            <td style="border: 1px solid black;"><?=@$cnImplemen?></td>
                            
                            <?php if ($implementasi['instruksi_deleted'] == true) { ?>
                                <td style="border: 1px solid black;"><strike><?=@$implementasi['tgl_implementasi']?></strike></td>
                            <?php } else { ?>
                                <td style="border: 1px solid black;"><?=@$implementasi['tgl_implementasi']?></td>
                            <?php } ?>


                            <?php if ($implementasi['instruksi_deleted'] == true) { ?>
                                <td style="border: 1px solid black;"><strike><?=@$implementasi['implementasi']?></strike></td>
                            <?php } else { ?>
                                <td style="border: 1px solid black;"><?=@$implementasi['implementasi']?></td>
                            <?php } ?>


                            <?php if ($implementasi['instruksi_deleted'] == true) { ?>
                                <td style="border: 1px solid black;"><strike><?=@$implementasi['perawat_1']?></strike></td>
                            <?php } else { ?>
                                <td style="border: 1px solid black;"><?=@$implementasi['perawat_1']?></td>
                            <?php } ?>


                            <?php if ($implementasi['instruksi_deleted'] == true) { ?>
                                <td style="border: 1px solid black;"><strike><?=@$implementasi['perawat_2']?></strike></td>
                            <?php } else { ?>
                                <td style="border: 1px solid black;"><?=@$implementasi['perawat_2']?></td>
                            <?php } ?>


                            <?php if ($implementasi['instruksi_deleted'] == true) { ?>
                                <td style="border: 1px solid black;"><strike><?=@$implementasi['catatan_implementasi']?></strike></td>
                            <?php } else { ?>
                                <td style="border: 1px solid black;"><?=@$implementasi['catatan_implementasi']?></td>
                            <?php } ?>
                            
                        </tr>
                        <?php
                        $cnImplemen++;
                    }
                }
            }else{
                $no++;
            }
        }
             ?>
    </tbody>
</table>