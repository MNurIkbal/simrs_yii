<table id="tabel-reseptur" border=1 style="width:100%;border-collapse: collapse">
    <thead>
        <tr class="bg-inverse">
            <th>No</th>
            <th><?=Yii::t('app', 'Racikan / non racikan')?></th>
            <th><?=Yii::t('app', 'R ke-')?></th>
            <th><?=Yii::t('app', 'Nama obat')?></th>
            <th><?=Yii::t('app', 'Satuan kecil')?></th>
            <th><?=Yii::t('app', 'Signa')?></th>
            <th><?=Yii::t('app', 'Qty')?></th>
        </tr>
    </thead>
    <tbody>
    	<?php 
    		$rowNum=1;
    		foreach ($data_resep as $detail_resep) {
    	?>
        <tr>
            <td><?=$rowNum?></td>
            <td><?=$detail_resep['racikan_nama']?></td>
            <td><?=$detail_resep['rke']?></td>
            <td><?=$detail_resep['obatalkes_nama']?></td>
            <td><?=$detail_resep['satuan_kecil']?></td>
            <td><?=isset($detail_resep['signa']) && !empty($detail_resep['signa']) ? json_decode($detail_resep['signa'])->text : $detail_resep['signa_nama']?></td>
            <td><?=$detail_resep['qty_reseptur']?></td>
        </tr>

        <?php 
        	$rowNum++;
    		}
        ?>
    </tbody>
</table>