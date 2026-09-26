<?php

    use app\components\DocoHelpers;
?>
<h3 class="text-semibold text-center">Formulir RL 3.8</h3>
<h3 class="text-semibold text-center">Kegiatan Laboratorium</h3>
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
                    foreach ($detail as $ruangan) : ?>
                        <tr>
                            <td colspan="3" class="text-center"><strong><?= $ruangan['label'] ?></strong></td>
                            <td style="display: none;"></td>
                            <td style="display: none;"></td>
                        </tr>
                        <?php
                            $noKelompok = 1;
                            $totalKegiatan = 0;
                            foreach ($ruangan['data'] as $kelompok) :
                        ?>
                            <tr>
                                <td><strong><?= $noKelompok ?></strong></td>
                                <td colspan="2"><strong><?= $kelompok['label'] ?></strong></td>
                                <td style="display: none;"></td>
                            </tr>
                        <?php
                                $noJenis = 1;
                                foreach ($kelompok['data'] as $jenis)  :
                        ?>
                                    <tr>
                                        <td><strong><?= $noKelompok.'.'.$noJenis ?></strong></td>
                                        <td colspan="2"><strong><?= $jenis['label'] ?></strong></td>
                                        <td style="display: none;"></td>
                                    </tr>
                        <?php
                                    $noTindakan = 1;
                                    foreach ($jenis['data'] as $tindakan) :
                                        $qty += $tindakan['qty'];
                                        $totalKegiatan += $tindakan['qty'];
                        ?>
                                        <tr>
                                            <td><strong><?= $noKelompok.'.'.$noJenis . '.' . $noTindakan ?></strong></td>
                                            <td><?= $tindakan['label'] ?></td>
                                            <td class="text-center"><?= $tindakan['qty'] ?></td>
                                        </tr>
                        <?php
                                        $noTindakan++;
                                    endforeach;
                                    $noJenis++;
                                endforeach;
                                $noKelompok++;
                            endforeach;
                        ?>
                            <tr>
                                <td colspan="2"><strong>Total <?= $ruangan['label'] ?></strong></td>
                                <td class="text-center"><?= $totalKegiatan ?></td>
                            </tr>
                        <?php
                            $no++;
                        endforeach; ?>
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