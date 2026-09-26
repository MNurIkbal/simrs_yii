<?php
    use yii\web\View;
    use app\components\DocoHelpers;
?>
<h3 class="text-semibold text-center">Formulir RL 1.3</h3>
<h3 class="text-semibold text-center">FASILITAS TEMPAT TIDUR RAWAT INAP</h3>
    <div class="form-group">
        <div class="col-lg-12">
            <?php
                //foreach ($profil as $keyz => $val) :
            ?>
               <!--  <div class="row">
                    <label class="control-label col-lg-1 text-black"><?php //echo $keyz ?></label>
                    <div class="col-lg-11">
                        <?php //echo $val ?>
                    </div>
                </div> -->
              <?php
               // endforeach;
            ?>


            <!-- <h6>RL 1.3 Fasilitas Tempat Tidur Rawat Inap</h6> -->
            <table id="rl-pembedahan" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    
                    <tr class="bg-inverse">
                        <th rowspan="<?php echo count($header); ?>" width="1">No</th>
                        <th rowspan="<?php echo count($header); ?>"><?=\Yii::t("fe", "Jenis Pelayanan");?></th>
                        <th rowspan="<?php echo count($header); ?>" style="border-right: 1px solid white;"><?=\Yii::t("fe", "Jumlah Tempat Tidur");?></th>
                        <th colspan="<?php echo count($header); ?>" class="text-center"><?=\Yii::t("fe", "PERINCIAN TEMPAT TIDUR PER-KELAS");?></th>
                    </tr>
                    <tr class="bg-inverse">    
                        <?php
                            foreach ($header as $value) :
                        ?>
                            <th rowspan="2" class="text-center"><?= strtoupper($value) ?></th>
                        <?php
                            endforeach;
                        ?>
                    </tr>
                </thead>
                <tbody>
                    <!-- <tr style="background-color: #606060;color: #ffffff;">
                        <td class="text-center" ><?php echo 1 ?></td>
                        <td class="text-center" ><?php echo 2 ?></td>
                        <td class="text-center" ><?php echo 3 ?></td>
                        <?php 
                            $noHead = 3;
                            foreach ($header as $k => $value) :
                            $noHead ++;
                            ?>
                                <td class="text-center" ><?php echo $noHead; ?></td>
                        <?php endforeach; ?>
                    </tr> -->


                    <?php
                        $total = [];
                        $total_beds = 0;
                        $no = 1;
                        foreach ($list as $key => $value) :
                    ?>
                    <tr>
                        <td><?= $no ?></td>
                        <td><?= $value ?></td>
                        <?php
                            $total_bed = isset($dataBed[$key]) ? $dataBed[$key] : 0;
                            $total_beds += $total_bed;
                        ?>
                        <td class="text-center"><?= DocoHelpers::formatNumber($total_bed) ?></td>

                        <?php
                        foreach ($header as $k =>$v) :
                            $total_satuan = isset($detail[$k][$key]) ? $detail[$k][$key] : 0;
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
                        <th class="text-center"><?= DocoHelpers::formatNumber($total_beds) ?> </th>
                        <?php
                            foreach ($total as $key => $value) :
                        ?>
                            <th class="text-center"><?= DocoHelpers::formatNumber($value) ?> </th>
                        <?php
                            endforeach;
                        ?>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<script type="text/javascript">
var table;
$(document).ready(function() {
    table = $("#rl-pembedahan").DataTable({
      "language": {
        "search": "Pencarian&nbsp;:&nbsp;"
      },
      ordering : false,
      searching : false,
      paging: false,
    });
});
</script>