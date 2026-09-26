<?php 
    /**
     * 
     * * @author : bacengjs (Bambang.Hermawan@sirs.co.id)
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
            <table id="table-perubahan-data-pasien" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "Tanggal Update");?></th>
                        <th><?=\Yii::t("fe", "Nama pegawai");?></th>
                        <th><?=\Yii::t("fe", "Alasan");?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="4"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php 
    $this->registerJs("
        var id = `$pasien_id`;
    ".$this->render('../js/riwayat-perubahan-data-pasien.js'), View::POS_END);
?>
