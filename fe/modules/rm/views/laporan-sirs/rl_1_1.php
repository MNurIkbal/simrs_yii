<?php
    use yii\web\View;
    use app\components\DocoHelpers;
?>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Formulir RL 1.1'); ?></h3>
<h3 class="text-semibold text-center"><?= Yii::t('fe', 'Data Dasar Rumah Sakit'); ?></h3>
<hr>
<h4>
    Tanggal : <?= date('d F Y'); ?>
</h4>
    <div class="form-group">
        <div class="col-lg-12">
            <table id="rl-datars" class="table table-striped table-condensed table-hover" style="width:100%">
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($response as $key => $value) :
                    ?>
                        <tr>
                            <td width='1'>
                                <?php
                                if (!ctype_digit($key[0])) {
                                    echo '<strong>' . $no++ . '</strong>'; 
                                }
                                ?>
                            </td>
                            <td width='400px'>
                                <?php
                                if (!ctype_digit($key[0])) {
                                    echo '<strong>' . $key . '</strong>'; 
                                } else {
                                    echo $key; 
                                }
                                ?>
                            </td>
                            <td><?= $value; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<script type="text/javascript">
var table;
$(document).ready(function() {
    table = $("#rl-datars").DataTable({
        "language": {
            "search": "Pencarian&nbsp;:&nbsp;"
        },
        "columnDefs": [
            { "width": "40px", "targets": 0 },
        ],
        ordering : false,
        searching : false,
        paging: false,
    });
});
</script>