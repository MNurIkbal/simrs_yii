<?php

/**
 * @Author: afil
 * @Date:   2018-01-19 16:12:29
 * @Last Modified by:   afil
 * @Last Modified time: 2018-01-22 12:00:33
 * @Description: 
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <table class="table datatable-basic table-striped table-hover dataTable no-footer table-framed view-persalinan">
        <thead>
            <tr class="bg-inverse">
                <th>No</th>
                <th><?=Yii::t('fe', 'Ruangan')?></th>
                <th><?=Yii::t('fe', 'Tanggal tindakan')?></th>
                <th><?=Yii::t('fe', 'Kategori')?></th>
                <th><?=Yii::t('fe', 'Nama tindakan')?></th>
                <th><?=Yii::t('fe', 'Jumlah')?></th>
                <th><?=Yii::t('fe', 'Keterangan')?></th>
            </tr>
        </thead>
        <tbody> 
            <tr>
                <td>1</td>
                <td>Jantung</td>
                <td><?=date("d M Y H:i:s")?></td>
                <td>Non kategori</td>
                <td>ID Card Pasien</td>
                <td>1</td>
                <td>1000000</td>
            </tr>
        </tbody>
    </table>
</div>
<div class="modal-footer">
    <?=Html::button(\Yii::t('fe', 'Kembali'),['class' => 'btn btn-default btn-sm', 'data-dismiss' => 'modal']); ?>
</div>

<script type="text/javascript">
    $(".view-persalinan").docoTabel({
        filter: false,
        scrollX: true,
    });
</script>