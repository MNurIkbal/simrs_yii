<?php
    use yii\web\View;
?>

<div class="panel panel-white" style="display:none;">
    <div class="panel-heading">
        <h5 class="panel-title"><?=Yii::t('fe','Tarif karcis')?></h5>
    </div>
    <div class="panel-heading">
        <table id="tbl-karcis" class="table table-striped table-condensed table-hover table-karcis" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th width="10%">No</th>
                    <th class="karcis-title"><?=\Yii::t("fe", "Karcis");?></th>
                    <th class="harga-title"><?=\Yii::t("fe", "Harga");?></th>
                    <th><?=\Yii::t("fe", "Aksi");?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td colspan="5" class="text-center"><?=Yii::t('fe','Data tidak tersedia')?></td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <th style="text-align:right">Total:</th>
                    <th colspan="4"></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
