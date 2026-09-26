<table border="1" style='border-collapse: collapse;' width="100%">
    <thead>
        <tr class="bg-inverse">
            <th><?= \Yii::t("app", "No"); ?></th>
            <th><?= \Yii::t("app", "Tanggal Periksa"); ?></th>
            <th><?= \Yii::t("app", "Bagian Tubuh"); ?></th>
            <th><?= \Yii::t("app", "Bagian Tubuh Detail"); ?></th>
            <th><?= \Yii::t("app", "Keterangan"); ?></th>
        </tr>
    </thead>
    <tbody>
            <?php
            $rownum = 1;
        foreach ($data_anatomi as $row_anatomi) {
            ?>
        <tr>
            <td><?=@$rownum?></td>
            <td><?=date('d F Y H:i:s', strtotime($row_anatomi['created_date']))?></td>
            <td><?=@$row_anatomi['bagian']?></td>
            <td><?=@$row_anatomi['bagianDetail']?></td>
            <td><?=@$row_anatomi['catatan_tubuh']?></td>
        </tr>
            <?php
            $rownum++;
        }
            ?>
    </tbody>
</table>