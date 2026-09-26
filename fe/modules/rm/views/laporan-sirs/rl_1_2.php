<?php
use yii\web\View;
use app\components\DocoHelpers;
?>
<h3 class="text-semibold text-center">Formulir RL 1.2</h3>
<h3 class="text-semibold text-center">INDIKATOR PELAYANAN RUMAH SAKIT</h3>
<div class="form-group">
    <div class="col-lg-12">
        <table id="rl_1_2" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th>Tahun</th>
                    <th>BOR</th>
                    <th>LOS</th>
                    <th>BTO</th>
                    <th>TOI</th>
                    <th>NDR</th>
                    <th>GDR</th>
                    <th>Rata-rata Kunjungan/Hari</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $item=$response["data"];
                foreach($item as $content):
                    ?>
                    <tr>
                        <td><?= $content['tahun'] ?></td>
                        <td><?= $content['bor'] ?></td>
                        <td><?= $content['avlos'] ?></td>
                        <td><?= $content['bto'] ?></td>
                        <td><?= $content['toi'] ?></td>
                        <td><?= $content['ndr'] ?></td>
                        <td><?= $content['gdr'] ?></td>
                        <td><?= $content['avg_kunjungan'] ?></td>
                    </tr>
                    <?php 
                endforeach;
                ?>  
            </tbody>
            
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