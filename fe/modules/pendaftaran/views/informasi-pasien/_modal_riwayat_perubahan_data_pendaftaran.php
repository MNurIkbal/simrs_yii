<?php 
    /**
     * 
     * * @author : Ardi Pratama
     * * A product of PT. Citraraya Nusatama
     * * Powered by Sirs
     *
    */
    use yii\web\View;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <div class="row" style="margin-top: -20px;">
        <div class="col-md-12">
            <table id="table-perubahan-data-pendaftaran" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "Tanggal Perubahan");?></th>
                        <th><?=\Yii::t("fe", "Data Sebelum");?></th>
                        <th><?=\Yii::t("fe", "Data Sesudah");?></th>
                        <th><?=\Yii::t("fe", "User");?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="5"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
    $this->registerJs("
        var primaryKey = `$id`;
    ".$this->render('js/riwayat-perubahan-data-pendaftaran.js'), View::POS_END);
?>
