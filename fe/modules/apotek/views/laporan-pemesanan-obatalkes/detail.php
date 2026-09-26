<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title"><b><?= Yii::t('fe','Detail Pemesanan') ?></b></h6>
        </div>
        <div class="panel-body">
            <table id="table-penerimaan-obat" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th>No</th>
                        <th class="text-center"><?=\Yii::t("fe", "Nama Obat Alkes");?></th>
                        <th class="text-center"><?=\Yii::t("fe", "Jumlah Pemesanan");?></th>
                        <th class="text-center"><?=\Yii::t("fe", "Satuan Besar");?></th>
                        <th class="text-center"><?=\Yii::t("fe", "Jumlah Pemesanan");?></th>
                        <th class="text-center"><?=\Yii::t("fe", "Satuan Kecil");?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                        if (!empty($detail)) :
                            $no = 1;
                            foreach($detail as $value) :
                                
                    ?>
                        <tr>
                            <td><?= $no ?></td>
                            <td><?= $value['obatalkes_namalain'] ?></td>
                            <td class="text-right"><?= $value['qty_besar'] ?></td>
                            <td><?= $value['satuan_besar'] ?></td>
                            <td class="text-right"><?= $value['jumlah_pesan'] ?></td>
                            <td><?= $value['satuan_kecil'] ?></td>
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