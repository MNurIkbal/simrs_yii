<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <h6 class="text-semibold text-center">PEMAKAIAN OBAT ALKES</h6>
    <h6 class="text-semibold text-center">RUANGAN <?= strtoupper($data['ruangan_nama']) ?></h6>
    <div class="form-group">
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-6 control-label"><?=Yii::t('fe', 'Nomor pemakaian')?></label>
            <div class="col-lg-6">:&nbsp;<?= $data['nopemakaian_obat'] ?></div>
        </div>
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-6 control-label"><?=Yii::t('fe', 'Tanggal pemakaian')?></label>
            <div class="col-lg-6">:&nbsp;<?= date('d M Y',strtotime($data['tglpemakaianobat'])) ?></div>
        </div>
    </div>
    <hr>
    <div class="form-group">
        <div class="col-lg-12">
            <table id="pemakaian-print" 
            class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "Kode obat alkes");?></th>
                        <th><?=\Yii::t("fe", "Nama obat alkes");?></th>
                        <th><?=\Yii::t("fe", "Qty");?></th>
                        <th><?=\Yii::t("fe", "Satuan Besar");?></th>
                        <th><?=\Yii::t("fe", "Qty");?></th>
                        <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="9">
                            <?=\Yii::t("fe", "Data tidak ditemukan.");?>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
            <?= Html::a('<i class="fa fa-file-pdf-o"></i>&nbsp;Print',Url::to(
                    [
                        '/apotek/informasi-pemakaian-obatalkes/cetak-pdf', 
                        'id' => $id
                    ]), 
                    [
                        'class' => 'btn bg-info btn-md',
                        'target' => '_blank'
                    ]) 
            ?>
            <?= Html::button('<i class="fa fa-arrow-left"></i>&nbsp;Kembali',[
                                'class' => 'btn bg-slate btn-md',
                                'data-dismiss' => 'modal'
                                ]); ?>
    </div>

</div>
<script type="text/javascript">
    var tab;
    $(function(){
        tab = $("#pemakaian-print").docoTabel({
            filter: false,
            displayLength: 10,
            processing: true,
            sorting: [[2, "asc"]], 
            serverSide: true,
            ajax: baseUrl+"apotek/informasi-pemakaian-obatalkes/get-list-item-before?id='<?= $parentId ?>'",
            columns: [
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {
                    title: "<?= (\Yii::t("fe", "Kode obat alkes")) ?>",
                    data: "obatalkes_kode",
                    orderable: false
                },
                {
                    title: "<?= (\Yii::t("fe", "Nama obat alkes")) ?>", 
                    data: "obatalkes_nama",
                    orderable: false
                },
                {
                    title: "<?= (\Yii::t("fe", "Qty")) ?>", 
                    data: "jumlah_input",
                    orderable: false
                },
                {
                    title: "<?= (\Yii::t("fe", "Satuan Besar")) ?>",
                    data: "satuanbesar_nama",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                },
               {
                    title: "<?= (\Yii::t("fe", "Qty")) ?>", 
                    data: "qty_satuanpakai",
                    orderable: false
                },
                {
                    title: "<?= (\Yii::t("fe", "Satuan Kecil")) ?>",
                    data: "satuankecil_nama",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
        });
    })
</script>