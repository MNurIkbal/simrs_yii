<table id="tabel-reseptur" cellpadding="5" class="tabel" border="1" cellspacing="0">
    <thead>
        <tr class="bg-inverse">
            <th>No</th>
            <th><?=Yii::t('app', 'Nama Pemeriksaan')?></th>
            <th><?=Yii::t('app', 'Hasil')?></th>
            <th><?=Yii::t('app', 'Satuan')?></th>
            <th><?=Yii::t('app', 'Nilai Rujukan')?></th>
            <th><?=Yii::t('app', 'Keterangan')?></th>
            <th><?=Yii::t('app', 'Petugas')?></th>
        </tr>
    </thead>
    <tbody>
    	<?php 
    		$rowNum=1;
    		foreach ($data_laboratorium as $grup_name => $grup_jenis) {
                $countGrup = 1;
                $sumGrup = count($grup_jenis) + 1;
                foreach ($grup_jenis as $detail_laboratorium) {
                    if($countGrup == 1){
        ?>
        <tr>
            <td rowspan="<?=$sumGrup?>"><?=$rowNum?></td>
            <td colspan="6">Jenis Pemeriksaan : <?=@$grup_name?></td>
        </tr>
        <?php
                    }
    	?>
        <tr>
            <td><?=@$detail_laboratorium['daftartindakan_nama']?></td>
            <td><?=@$detail_laboratorium['jumlah']?></td>
            <td><?=@$detail_laboratorium['satuanlab_nama']?></td>
            <td>-</td>
            <td></td>
            <td><?=@$detail_laboratorium['pegawai_lab']?></td>
        </tr>

        <?php
                    
                $countGrup++;
                }
        	$rowNum++;
    		}
        ?>
    </tbody>
</table>