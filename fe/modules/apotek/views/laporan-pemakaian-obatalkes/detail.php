<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title"><b><?= Yii::t('fe','Detail Pemakaian') ?></b></h6>
        </div>
        <div class="panel-body">
            <table id="table-penerimaan-obat" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th class="text-center"><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                        <th class="text-center"><?=\Yii::t("fe", "Qty Pemakaian");?></th>
                        <th class="text-center"><?=\Yii::t("fe", "Qty Konversi");?></th>
                        <th class="text-center"><?=\Yii::t("fe", "Keterangan");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($detail)) :
                            $no = 1;
                            foreach($detail as $value) :
                                $qty_besar = $value['jumlah_input'] . ' ' . $value['satuanbesar_nama'];
                                $qty_kecil = $value['qty_satuanpakai'] . ' ' . $value['satuankecil_nama'];
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td class="text-center"><?= $value['obatalkes_nama'] ?></td>
                            <td class="text-center"><?= $qty_besar ?></td>
                            <td class="text-center"><?= $qty_kecil ?></td>
                            <td class="text-center"><?= $value['keterangan_pemakaianobat'] ?></td>
                        </tr>
                    <?php
                            $no++;
                            endforeach;
                        else :
                    ?>
                        <tr>
                            <td colspan="5" class="text-center">Data Kosong</td>
                        </tr>
                    <?php
                        endif;
                     ?>
                </tbody>
            </table>
        </div>
    </div>
</div>