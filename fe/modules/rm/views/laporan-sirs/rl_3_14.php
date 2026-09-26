<?php
    use yii\web\View;
    use app\components\DocoHelpers;
?>

<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Formulir RL 3.14'); ?></h3>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Rujukan'); ?></h3>

    <div class="form-group">
        <div class="col-lg-12">
    		<h5>Kode RS : <?= isset($profil['nokode_rumahsakit']) ? $profil['nokode_rumahsakit'] : '-'; ?></h5>
			<h5>Nama RS : <?= isset($profil['nama_rumahsakit']) ? $profil['nokode_rumahsakit'] : '-'; ?></h5>
	    <?= isset($bulan) && !empty($bulan) ? '<h5>Bulan   : '.DocoHelpers::$namaBulan[$bulan-1].', ' : '<h5>';?> Tahun   : <?=$tahun?></h5>
        	<div class="table-responsive">
	            <table id="rl-3_1" class="table table-striped table-condensed table-hover" style="width:100%">
	                <thead>
	                    <tr class="bg-inverse">
	                        <th rowspan="2" width="1">No</th>
	                        <th rowspan="2"><?=\Yii::t("fe", "Jenis Pelayanan");?></th>
                            <?php foreach ($asalRujukan as $k => $v): ?>
	                        <th rowspan="2"><?=\Yii::t("fe", $v["asalrujukan_nama"]);?></th>
                            <?php endforeach; ?>
	                    </tr>
	                </thead>
	                <tbody>
	                    <?php
	                        $no = 1;
                            $total = [];
	                        foreach ($dataRujukan as $m => $n) :
	                    ?>
	                    <tr>
	                        <td><?= $no ?></td>
	                        <td><strong><?= $n["nama"] ?></strong></td>
	                        <?php
                                foreach ($n["rujukan"] as $o => $p):
                                    $total[$o] = isset($total[$o]) ? $total[$o] : 0;
                                    if(!empty($p["data"])):
                                        $total[$o] = $total[$o] + $p["data"]["jumlah"];
                                        echo "<td><strong>" . $p["data"]["jumlah"] . "</strong></td>";
                                    else:
                                        echo "<td>0</td>";
                                    endif;
                                endforeach;
	                            $no++;
	                        ?>
	                    </tr>
                        <?php
                            endforeach;
                        ?>
                        <tr><td colspan="<?php echo 2 + count($total) ?>">&nbsp;</td></tr>
                        <tr>
                            <td colspan="2" style="text-align: center;"><strong>TOTAL</strong></td>
                            <?php
                                foreach($total as $kt => $vt):
                            ?>
                            <td style="font-size: 16px;"><strong><?= $vt ?></strong></td>
                            <?php
                                endforeach;
                            ?>
                        </tr>
	                </tbody>
	            </table>
	        </div>
        </div>
    </div>
<script type="text/javascript">
$(document).ready(function() {
});
</script>
