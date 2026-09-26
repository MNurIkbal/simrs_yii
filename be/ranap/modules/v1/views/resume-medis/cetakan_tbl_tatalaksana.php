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
            <th><?= \Yii::t("app", "Nama Obat"); ?></th>
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
                </tr>
                    <?php
                    $count++;
                }
            }else{
                ?>
                <tr>
                    <td colspan="2">Data Tidak Tersedia</td>
                </tr>
                <?php
            }
            ?>
    </tbody>
</table>