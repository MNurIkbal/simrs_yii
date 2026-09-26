<?php
    use yii\web\View;
    use app\components\DocoHelpers;
?>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Formulir RL 3.1'); ?></h3>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Kegiatan Pelayanan Rawat Inap'); ?></h3>

    <div class="form-group">
        <div class="col-lg-12">
    		<h5>Kode RS : <?=$profil['nokode_rumahsakit']?></h5>
			<h5>Nama RS : <?=$profil['nama_rumahsakit']?></h5>
			<h5>Tahun   : <?=$tahun?></h5>
        	<div class="table-responsive">
	            <table id="rl-3_1" class="table table-striped table-condensed table-hover" style="width:100%">
	                <thead>
	                    <tr class="bg-inverse">
	                        <th rowspan="2" width="1">No</th>
	                        <th rowspan="2"><?=\Yii::t("fe", "Jenis Pelayanan");?></th>
	                        <th rowspan="2"><?=\Yii::t("fe", "Pasien Awal Tahun");?></th>
	                        <th rowspan="2"><?=\Yii::t("fe", "Pasien Masuk");?></th>
	                        <th rowspan="2"><?=\Yii::t("fe", "Pasien Keluar Hidup");?></th>
	                        <th colspan="2"><?=\Yii::t("fe", "Pasien Keluar Mati");?></th>
	                        <th rowspan="2"><?=\Yii::t("fe", "Jumlah Lama Dirawat");?></th>
	                        <th rowspan="2"><?=\Yii::t("fe", "Pasien Akhir Tahun");?></th>
	                        <th rowspan="2"><?=\Yii::t("fe", "Jumlah Hari Perawatan");?></th>
	                        <?php
	                        	$colspanKelas = count($header);
	                        ?>
	                        <th colspan="<?=$colspanKelas?>"><?=\Yii::t("fe", "Rincian Hari Perawatan Per Kelas");?></th>
	                    </tr>
	                    <tr class="bg-inverse">
	                        <th><?=\Yii::t("fe", "< 48 jam");?></th>
	                        <th><?=\Yii::t("fe", "> 48 jam");?></th>
	                        <?php
	                            foreach ($header as $value) :
	                        ?>
	                            <th class="text-center"><?= strtoupper($value) ?></th>
	                        <?php
	                            endforeach;
	                        ?>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php
	                        $total = [];
	                        $no = 1;
	                        asort($list);
	                        $subtot = [];
	                        foreach ($list as $key => $value) :
	                    ?>
	                    <tr>
	                        <td><?= $no ?></td>
	                        <td><?= $value ?></td>
	                        	<?php 
	                        		$col_pasien_awal_tahun = isset($dataPelayanan[$key]['pasien_awal_tahun']) ? $dataPelayanan[$key]['pasien_awal_tahun'] : 0;
	                        		$subtot['pasien_awal_tahun'][] = $col_pasien_awal_tahun;
	                        	?>
	                        <td><?=$col_pasien_awal_tahun?></td>
	                        	<?php 
	                        		$col_pasien_masuk = isset($dataPelayanan[$key]['pasien_masuk']) ? $dataPelayanan[$key]['pasien_masuk'] : 0;
	                        		$subtot['pasien_masuk'][] = $col_pasien_masuk;
	                        	?>	
	                        <td><?=$col_pasien_masuk?></td>
	                        	<?php 
	                        		$col_pasien_keluar_hidup = isset($dataPelayanan[$key]['pasien_keluar_hidup']) ? $dataPelayanan[$key]['pasien_keluar_hidup'] : 0;
	                        		$subtot['pasien_keluar_hidup'][] = $col_pasien_keluar_hidup;
	                        	?>
	                        <td><?=$col_pasien_keluar_hidup?></td>
	                        	<?php 
	                        		$col_pasien_mati_under48 = isset($dataPelayanan[$key]['pasien_mati_under48']) ? $dataPelayanan[$key]['pasien_mati_under48'] : 0;
	                        		$subtot['pasien_mati_under48'][] = $col_pasien_mati_under48;
	                        	?>
	                        <td><?=$col_pasien_mati_under48?></td>
	                        	<?php
	                        	 	$col_pasien_mati_48 = isset($dataPelayanan[$key]['pasien_mati_48']) ? $dataPelayanan[$key]['pasien_mati_48'] : 0; 
	                        	 	$subtot['pasien_mati_48'][] = $col_pasien_mati_48;
	                        	 ?>
	                        <td><?=$col_pasien_mati_48?></td>
	                        	<?php 
	                        		$col_lama_dirawat = isset($dataPelayanan[$key]['lama_dirawat']) ? $dataPelayanan[$key]['lama_dirawat'] : 0;
	                        		$subtot['lama_dirawat'][] = $col_lama_dirawat;
	                        	?>
	                        <td><?=$col_lama_dirawat?></td>
	                        	<?php 
	                        		$col_pasien_akhir_tahun = isset($dataPelayanan[$key]['pasien_akhir_tahun']) ? $dataPelayanan[$key]['pasien_akhir_tahun'] : 0;
	                        		$subtot['pasien_akhir_tahun'][] = $col_pasien_akhir_tahun;
	                        	?>
	                        <td><?=$col_pasien_akhir_tahun?></td>
	                        	<?php 
	                        		$col_lama_hari_rawat = isset($dataPelayanan[$key]['lama_hari_rawat']) ? $dataPelayanan[$key]['lama_hari_rawat'] : 0;
	                        		$subtot['lama_hari_rawat'][] = $col_lama_hari_rawat;
	                        	?>
	                        <td><?=$col_lama_hari_rawat?></td>
	                        <?php
	                        foreach ($header as $k =>$v) :
	                            $total_satuan = isset($detail[$key][$k]) ? $detail[$key][$k] : 0;
	                            if (!isset($total[$k])) :
	                                $total[$k] = 0;
	                            endif;
	                            $total[$k] += $total_satuan;
	                        ?>
	                        <td class="text-center"><?= DocoHelpers::formatNumber($total_satuan) ?></td>
	                        <?php
	                            endforeach;
	                            $no++;
	                        ?>
	                    </tr>
	                        <?php
	                        endforeach;
	                        ?>
	                </tbody>
	                <tfoot>
	                    <tr>
	                        <th colspan="2" class="text-center">Total</th>
	                        <th><?=(isset($subtot['pasien_awal_tahun']) && count($subtot['pasien_awal_tahun'])>0) ?array_sum($subtot['pasien_awal_tahun']):0; ?></th>
	                        <th><?=(isset($subtot['pasien_masuk']) && count($subtot['pasien_masuk'])>0) ?array_sum($subtot['pasien_masuk']):0; ?></th>
	                        <th><?=(isset($subtot['pasien_keluar_hidup']) && count($subtot['pasien_keluar_hidup'])>0) ?array_sum($subtot['pasien_keluar_hidup']):0; ?></th>
	                        <th><?=(isset($subtot['pasien_mati_under48']) && count($subtot['pasien_mati_under48'])>0) ?array_sum($subtot['pasien_mati_under48']):0; ?></th>
	                        <th><?=(isset($subtot['pasien_mati_48']) && count($subtot['pasien_mati_48'])>0) ?array_sum($subtot['pasien_mati_48']):0; ?></th>
	                        <th><?=(isset($subtot['lama_dirawat']) && count($subtot['lama_dirawat'])>0) ?array_sum($subtot['lama_dirawat']):0; ?></th>
	                        <th><?=(isset($subtot['pasien_akhir_tahun']) && count($subtot['pasien_akhir_tahun'])>0) ?array_sum($subtot['pasien_akhir_tahun']):0; ?></th>
	                        <th><?=(isset($subtot['lama_hari_rawat']) && count($subtot['lama_hari_rawat'])>0) ?array_sum($subtot['lama_hari_rawat']):0; ?></th>
	                        <?php
	                            foreach ($total as $key_tot => $val_tot) :
	                        ?>
	                            <th class="text-center"><?= DocoHelpers::formatNumber($val_tot) ?> </th>
	                        <?php
	                            endforeach;
	                        ?>
	                    </tr>
	                </tfoot>
	            </table>
	        </div>
        </div>
    </div>
<script type="text/javascript">
var table;
$(document).ready(function() {
    // table = $("#rl-3_1").DataTable({
    //   "language": {
    //     "search": "Pencarian&nbsp;:&nbsp;"
    //   },
    //   ordering : false,
    //   searching : false,
    //   paging: false,
    // });
});
</script>