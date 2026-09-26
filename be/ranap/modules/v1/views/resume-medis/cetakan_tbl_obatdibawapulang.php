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
<table border="1" class="tbl-bordered">
    <thead>
        <tr class="bg-inverse">
            <th><?= \Yii::t("app", "No"); ?></th>
            <th><?= \Yii::t("app", "Racikan/Non Racikan"); ?></th>
            <th><?= \Yii::t("app", "R Ke"); ?></th>
            <th><?= \Yii::t("app", "Nama Obat"); ?></th>
            <th><?= \Yii::t("app", "Satuan Kecil"); ?></th>
            <th><?= \Yii::t("app", "Signa"); ?></th>
            <th><?= \Yii::t("app", "Qty"); ?></th>
        </tr>
    </thead>
    <tbody>
            <?php
            if(count($data)){
                $count = 1;
                foreach ($data as $row_table_obat) {
                    ?>
                <tr>
                    <td><?=@$count?></td>
                    <td><?=@$row_table_obat['racikan_nama']?></td>
                    <td><?=@$row_table_obat['rke']?></td>
                    <td><?=@$row_table_obat['obatalkes_nama']?></td>
                    <td><?=@$row_table_obat['satuan_kecil']?></td>
                    <td><?=@$row_table_obat['signa_nama']?></td>
                    <td><?=@$row_table_obat['qty_reseptur']?></td>
                </tr>
                    <?php
                    $count++;
                }
            }else{
                ?>
                <tr>
                    <td colspan="7">Data Tidak Tersedia</td>
                </tr>
                <?php
            }
            ?>
            
    </tbody>
</table>