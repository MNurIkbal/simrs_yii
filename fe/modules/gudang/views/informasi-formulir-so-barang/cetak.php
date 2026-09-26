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
    <h6 class="text-semibold text-center">RUANGAN <?= $header['ruangan_nama'] ?></h6>
    <div class="form-group">
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-6 control-label"><?=Yii::t('fe', 'Tanggal stok opname')?></label>
            <div class="col-lg-6">:<?= date('d-M-Y',strtotime($header['tglstokopname'])) ?></div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-6 control-label"><?=Yii::t('fe', 'Nomer stok opname')?></label>
            <div class="col-lg-6">:<?= $header['nostokopname'] ?></div>
        </div>
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-6 control-label"><?=Yii::t('fe', 'Nomer formulir stok opname')?></label>
            <div class="col-lg-6">:<?= $header['noformulir'] ?></div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-6 control-label"><?=Yii::t('fe', 'Jenis stok opname')?></label>
            <div class="col-lg-6">:<?= $header['jenis_so'] ?></div>
        </div>
        <div class="col-lg-6">
            <label for="inputPassword" class="col-lg-6 control-label">&nbsp;</label>
            <div class="col-lg-6">&nbsp;</div>
        </div>
    </div>
    <hr>
    <div class="form-group">
        <div class="col-lg-12">
            <table id="table-list-cache" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1">No</th>
                        <th><?=\Yii::t("fe", "Nama Barang");?></th>
                        <th><?=\Yii::t("fe", "Kondisi");?></th>
                        <th><?=\Yii::t("fe", "Stok Sistem");?></th>
                        <th><?=\Yii::t("fe", "Stok Fisik");?></th>
                        <th><?=\Yii::t("fe", "Selisih");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        $no = 1;
                        $totalSs = $totalSf = $totalSeS = 0;
                        foreach ($detail as $value):
                            $totalSs += $value['volume_sistem'];
                            $totalSf += $value['volume_fisik'];
                            $totalSeS += $value['jmlselisihstok'];
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['barang_nama'] ?></td>
                            <td><?= $value['kondisibarang'] ?></td>
                            <td><?= $value['volume_sistem'] ?></td>
                            <td><?= $value['volume_fisik'] ?></td>
                            <td><?= $value['jmlselisihstok'] ?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                    <tr>
                        <th colspan="5" class="text-right">Total Stok Sistem</th>
                        <th class="text-right"><?= $totalSs ?></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-right">Total Stok Fisik</th>
                        <th class="text-right"><?= $totalSf ?></th>
                    </tr>
                    <tr>
                        <th colspan="5" class="text-right">Total Selisih Stok</th>
                        <th class="text-right"><?= $totalSeS ?></th>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
            <?= Html::a('<i class="fa fa-file-pdf-o"></i>&nbsp;Print',Url::to(
                    [
                        '/gudang/informasi-formulir-so-barang/export-pdf', 
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