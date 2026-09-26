<?php
    use yii\widgets\ActiveForm;
    use yii\helpers\Html;
    use yii\helpers\Url;
    use app\components\DocoHelpers;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <h6 class="text-semibold text-center">FORMULIR STOK OPNAME RUANGAN</h6>
    <h6 class="text-semibold text-center"><?= strtoupper($data['ruangan_nama']) ?></h6>
    <div class="form-group">
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-4 control-label"><?=Yii::t('fe', 'Periode stok')?></label>
            <div class="col-lg-8">:&nbsp;<?= $data['periode'] ?></div>
        </div>
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-6 control-label"><?=Yii::t('fe', 'Nomer formulir stok opname')?></label>
            <div class="col-lg-6">:&nbsp;<?= $data['no_formulir'] ?></div>
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
                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                        <th><?=\Yii::t("fe", "Tanggal Kadaluarsa");?></th>
                        <th><?=\Yii::t("fe", "Stok Sistem");?></th>
                        <th><?=\Yii::t("fe", "Stok Fisik");?></th>
                        <th><?=\Yii::t("fe", "Kondisi");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        foreach ($data['data'] as $value) :
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['barang_nama'] ?></td>
                            <td><?= $value['tglkadaluarsa'] ?></td>
                            <td><?= $value['stok_sistem'] ?></td>
                            <td></td>
                            <td></td>
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
                        '/gudang/transaksi-formulir/print-pdf', 
                        'id' => DocoHelpers::encrypt($id)
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