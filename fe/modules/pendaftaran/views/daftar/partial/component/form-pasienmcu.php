<div id="form-pasien-mcu" style="display: none;">
    <div class="form-group" id="form-pasien-mcu-content">
        <div class="row">
            <div class="col-md-12">
                <div class='my-legend-cst'>
                    <div class='legend-title-cst'>Keterangan</div>
                    <div id="info-file" class='legend-title-cst legend-source-cst'>Sumber : </div>
                    <div class='legend-scale-cst'>
                        <ul class='legend-labels-cst'>
                            <li>
                                <div id="info-lengkap" style="font-weight: bold"></div>
                                <br>
                                <span style='background:#99FFCC;'>Data Lengkap</span></li>
                            <li>
                                <div id="info-tidak-lengkap" style="font-weight: bold"></div>
                                <br>
                                <span style='background:#FF9999;'>Data Tidak Lengkap</span></li>
                            <li>
                                <div id="info-double-rm" style="font-weight: bold"></div>
                                <br>
                                <span style='background:#fde4a8;'>No Rekam Medik Salah / Duplikasi</span></li>
                            <li>
                                <div id="info-multiple-rm" style="font-weight: bold"></div>
                                <br>
                                <span style='background:#66cfff;'>Data Ditemukan > 1 No Rekam Medik</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-sm-12">
            <table id="tbl-pasien-mcu" class="table table-striped table-condensed list-mcu-pasien" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?=\Yii::t("fe", "Group");?></th>
                        <th>No</th>
                        <th><?=\Yii::t("fe", "No Asuransi");?></th>
                        <th><?=\Yii::t("fe", "Nama Pemilik Asuransi");?></th>
                        <th><?=\Yii::t("fe", "Jenis Identitas");?></th>
                        <th><?=\Yii::t("fe", "No Identitas");?></th>
                        <th><?=\Yii::t("fe", "No Rekam Medik");?></th>
                        <th><?=\Yii::t("fe", "Nama Pasien");?></th>
                        <th><?=\Yii::t("fe", "Tempat Lahir");?></th>
                        <th><?=\Yii::t("fe", "Tanggal Lahir");?></th>
                        <th><?=\Yii::t("fe", "Jenis Kelamin");?></th>
                        <th><?=\Yii::t("fe", "Golongan Darah");?></th>
                        <th><?=\Yii::t("fe", "Status Perkawinan");?></th>
                        <th><?=\Yii::t("fe", "Alamat");?></th>
                        <th><?=\Yii::t("fe", "No Telepon");?></th>
                        <th><?=\Yii::t("fe", "No Rujukan");?></th>
                        <th><?=\Yii::t("fe", "Rujukan Dari");?></th>
                        <th><?=\Yii::t("fe", "Nama Perujuk");?></th>

                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="text-center" colspan="9"><?=\Yii::t("fe", "Belum ada data yang ditambahkan.");?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
