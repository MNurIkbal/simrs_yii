<?php

    use app\components\DocoHelpers;
?>
<h3 class="text-semibold text-center">Formulir RL 3.7</h3>
<h3 class="text-semibold text-center">Kegiatan Radiologi</h3>
    <div class="form-group">
        <div class="col-lg-12">
            <table id="rl-radiologi" class="table table-striped table-condensed table-hover" 
            style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "JENIS KEGIATAN");?></th>
                        <th class="text-center"><?=\Yii::t("fe", "JUMLAH");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $total = [];
                    $no = 1;
                    $parent = false;
                    $qty = 0;
                    foreach ($detail as $key => $value) : ?>
                        <tr>
                            <td colspan="3"><strong><?= $value['label'] ?></strong></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                        </tr>
                        <?php $i = 1; foreach ($value['data'] as $k => $v) : 
                            $qty = $qty + $v['qty'];
                        ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= $v['label'] ?></td>
                            <td class="text-center"><?= $v['qty'] ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2"><strong>TOTAL</strong></th>
                        <th class="text-center"><strong><?= $qty ?></strong></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
<script type="text/javascript">
var table;
$(document).ready(function() {
    table = $("#rl-radiologi").DataTable({
      "language": {
        "search": "Pencarian&nbsp;:&nbsp;"
      },
      ordering : false,
      searching : false,
      paging: false,
    });
});
</script>