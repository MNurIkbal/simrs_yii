<table id="tabel-reseptur" border=1 style="width:100%;">
    <thead>
        <tr class="bg-inverse">
            <th>No</th>
            <th><?=Yii::t('app', 'Tanggal Pemeriksaan')?></th>
            <th><?=Yii::t('app', 'Nama Pemeriksaan')?></th>
            <th><?=Yii::t('app', 'Hasil Pemeriksaan')?></th>
            <th><?=Yii::t('app', 'Satuan Hasil')?></th>
            <th><?=Yii::t('app', 'Nilai Rujukan')?></th>
        </tr>
    </thead>
    <tbody>
    	<?php 
    		$rowNum=1;
    		foreach ($data_radiologi as $detail_radiologi) {
    	?>
        <tr>
            <td><?=$rowNum?></td>
            <td><?=@$detail_radiologi['tglmasukpenunjang']?></td>
            <td><?=@$detail_radiologi['daftartindakan_nama']?></td>
            <td><?=@$detail_radiologi['jumlah']?></td>
            <td><?=@$detail_radiologi['satuanlab_nama']?></td>
            <td>-</td>
        </tr>

        <?php 
        	$rowNum++;
    		}
        ?>
    </tbody>
</table>