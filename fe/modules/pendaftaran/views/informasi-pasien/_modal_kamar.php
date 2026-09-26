<div id="modalTempatTidur" class="modal fade in" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title">Pilih Tempat Tidur</h5>
            </div>
            <div class="modal-body">
                <div class="row">
                    <?php foreach($masterWarnaTempatTidur as $key =>$val):?>
                        <div class="col-md-2">
                            <div class="square" style="background-color:<?php echo $val['kode_warna']?>"></div>
                            <h6><?php echo $val['kettempattidur_nama']?></h6>
                        </div>
                    <?php endforeach ?>
                </div>
                <hr>
                <div class="panel-button">
                    <div class="form-group">
                        <input type="checkbox" name="kamarTitipan" id="kamarTitipanCheck" value="1">
                        <label for="kamar_titipan" style="font-weight: bold;font-size: 15px;">Kamar Titipan</label>&nbsp;&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="kamarAps" id="kamarApsCheck" value="1">
                        <label for="kamar_aps" style="font-weight: bold;font-size: 15px;">Kamar APS</label>
                    </div><br>
                    <div class="row" id="filterHeader">
                    </div>
                </div>
                <hr>
                <div class="row table-responsive">
                    <div id="tableKamarWrapper" class="table-scroll">
                        <table class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%" id="tableKamar">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="80">No</th>
                                    <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Kamar");?></th>
                                    <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                    <div id="tableKamarTitipanWrapper" class="table-scroll" style="display:none;">
                        <table class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%" id="tableKamarTitipan">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="80">No</th>
                                    <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Kamar");?></th>
                                    <th><?=\Yii::t("fe", "Kelas");?></th>
                                    <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Data tidak tersedia</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div id="tableKamarApsWrapper" class="table-scroll" style="display:none;">
                        <table class="table table-striped table-condensed table-hover table-pilih-kamar" style="width:100%" id="tableKamarAps">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="80">No</th>
                                    <th><?=\Yii::t("fe", "Jenis Kasus Penyakit");?></th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Kamar");?></th>
                                    <th><?=\Yii::t("fe", "Kelas");?></th>
                                    <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center">Data tidak tersedia</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>