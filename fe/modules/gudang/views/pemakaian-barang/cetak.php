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
    <h6 class="text-semibold text-center">PEMAKAIAN BARANG</h6>
    <h6 class="text-semibold text-center">RUANGAN <?= strtoupper($ruangan_name) ?></h6>
    <div class="form-group">
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-6 control-label"><?=Yii::t('fe', 'Nomor pemakaian')?></label>
            <div class="col-lg-6">:<?= $noPemakaian ?></div>
        </div>
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-6 control-label"><?=Yii::t('fe', 'Tanggal pemakaian')?></label>
            <div class="col-lg-6">:<?= date('d M Y',strtotime($tglPemakaian)) ?></div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-12">
            <table id="table-list-cache" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                        <th><?=\Yii::t("fe", "Qty Input");?></th>
                        <th><?=\Yii::t("fe", "Satuan Input");?></th>
                        <th><?=\Yii::t("fe", "Qty Konversi");?></th>
                        <th><?=\Yii::t("fe", "Satuan Kecil");?></th>
                        <th><?=\Yii::t("fe", "Keterangan");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['text'] ?></td>
                            <td><?= $value['jumlah_input'] != 0 ? $value['jumlah_input'] : $value['permintaan'] ?></td>
                            <td><?= $value['satuanbesar_nama'] != "" ? $value['satuanbesar_nama'] : $value['satuankecil_nama'] ?></td>
                            <td><?= $value['permintaan'] ?></td>
                            <td><?= $value['satuankecil_nama'] ?></td>
                            <td><?= $value['keterangan'] ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
            <?= Html::a('<i class="fa fa-file-pdf-o"></i>&nbsp;Print',Url::to(
                    [
                        '/gudang/informasi-pemakaian-barang/print-detail',
                        'id' => $id_parent,
                        'nopemakaian' => $noPemakaian
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