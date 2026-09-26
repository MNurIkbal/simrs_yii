<?php
    use yii\web\View;
    use app\components\DocoHelpers;
?>
<h3 class="text-semibold text-center">Formulir RL 3.6</h3>
<h3 class="text-semibold text-center">Kegiatan Pembedahan</h3>
    <div class="form-group">
        <div class="col-lg-12">
            <table id="rl-pembedahan" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "SPESIALISASI");?></th>
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
                        foreach ($list as $key => $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value ?></td>
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