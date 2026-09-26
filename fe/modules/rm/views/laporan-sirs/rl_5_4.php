<?php
    use yii\web\View;
    use app\components\DocoHelpers;
?>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Formulir RL 5.4'); ?></h3>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Daftar 10 Besar Penyakit Rawat Jalan'); ?></h3>

    <div class="form-group">
        <div class="col-lg-12">
    		<h5>Kode RS : <?=@$profil['nokode_rumahsakit']?></h5>
			<h5>Nama RS : <?=@$profil['nama_rumahsakit']?></h5>
			<h5>Bulan   : <?=@$textBulan?></h5>
			<h5>Tahun   : <?=@$tahun?></h5>
        	<div class="table-responsive">
	            <table id="rl-5_4" class="table table-striped table-condensed table-hover" style="width:100%">
	                <thead>
	                    <tr class="bg-inverse">
	                    	<th rowspan="2"><?=\Yii::t("fe", "No. Urut");?></th>
	                    	<th rowspan="2"><?=\Yii::t("fe", "KODE ICD 10");?></th>
	                    	<th rowspan="2"><?=\Yii::t("fe", "DESKRIPSI");?></th>
	                    	<th colspan="2"><?=\Yii::t("fe", "KASUS BARU MENURUT JENIS KELAMIN");?></th>
	                    	<th rowspan="2"><?=\Yii::t("fe", "Jumlah Kasus Baru");?></th>
	                    	<th rowspan="2"><?=\Yii::t("fe", "Jumlah Kunjungan");?></th>
	                    </tr>
	                    <tr class="bg-inverse">
	                    	<th><?=\Yii::t("fe", "Laki-Laki");?></th>
	                    	<th><?=\Yii::t("fe", "Perempuan");?></th>
	                    </tr>
	                </thead>
	                <tbody>
	                	<?php
	                		$no_urut = 1;
	                		foreach($datarl_5_4 as $content){ 
	                	?>
	                	<tr>
	                		<td><?=$no_urut?></td>
	                		<td><?=@$content['kodeicd']?></td>
	                		<td><?=@$content['deskripsi']?></td>
	                		<td><?=@$content['kasusbaru_laki']?></td>
	                		<td><?=@$content['kasusbaru_perempuan']?></td>
	                		<td><?=@$content['jumlahkasusbaru']?></td>
	                		<td><?=@$content['jumlahkunjungan']?></td>
	                	</tr>
	                	<?php 
	                			$no_urut++;
	                		}
	                	?>
	                </tbody>
	            </table>
    		</div>
    	</div>
    </div>

<script type="text/javascript">
var table;
$(document).ready(function() {
    table = $("#rl-5_4").DataTable({
      "language": {
        "search": "Pencarian&nbsp;:&nbsp;"
      },
      ordering : false,
      searching : false,
      paging: false,
    });
});
</script>