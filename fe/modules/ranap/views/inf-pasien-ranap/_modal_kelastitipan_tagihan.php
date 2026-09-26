<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">×</button>
    <h5 class="modal-title">Pilih Kelas Tagihan</h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <table class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%" id="tableKelasTagihan">
                <thead>
                    <tr class="bg-inverse">
                        <th width="80">No</th>
                        <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                        <th><?=\Yii::t("fe", "Kelas");?></th>
                        <th><?=\Yii::t("fe", "Ruangan");?></th>
                        <th><?=\Yii::t("fe", "Kamar");?></th>
                        <th><?=\Yii::t("fe", "Harga Akomodasi");?></th>
                        <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="8" class="text-center"><?= Yii::t('fe', 'Data tidak tersedia') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<script type="text/javascript">
    $( () => {
        
    })
</script>