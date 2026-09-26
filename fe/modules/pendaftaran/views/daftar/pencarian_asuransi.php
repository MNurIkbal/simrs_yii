<?php

/**
 * @Author: Sigit
 * @Date:   2019-01-17 10:17:42
 */

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
?>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= Yii::t('fe', 'Pencarian Data Peserta') ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12 mb-10">
            <form id="form-cek-pasien">
                <div class="d-flex justify-content-between" style="display: flex; justify-content: space-between; align-items: center">
                    <div style="display: flex;">
                        <div style="margin-right: 10px;">
                            <b>NIK <span class="text-danger">*</span></b>
                            <?= Html::textInput('nik_pasien', null, ['class' => 'form-control input-sm', 'placeholder' => 'Masukan NIK..', 'id' => 'nik_pasien', 'required' => true]) ?>
                            <span class="text-danger nik-validate"></span>
                        </div>
                        <div style="margin-right: 10px;">
                            <b>Tanggal lahir <span class="text-danger">*</span></b>
                            <input type="date" class="form-control input-sm" id="tanggal_lahir_pasien">
                            <span class="text-danger birthdate-validate"></span>
                        </div>
                    </div>
                    <div>
                        <div style="display: flex;">
                            <button type="button" class="btn btn-primary btn-xs btn-labeled" data-dismiss="modal"><b><i class="fa fa-arrow-left"></i></b> Kembali</button>
                            <button type="button" class="btn btn-primary btn-xs btn-labeled" id="btn-cari-pasien-asuransi"><b><i class="fa fa-search"></i></b> Cari</button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-md-12">
            <table id="tb-pencarian-lanjutan" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th><?= Yii::t("fe", "No Asuransi") ?></th>
                        <th><?= Yii::t("fe", "Nama Peserta") ?></th>
                        <th><?= Yii::t("fe", "No.Pegawai/Perusahaan") ?></th>
                        <th><?= Yii::t("fe", "Tanggal Lahir / Jenis Kelamin") ?></th>
                        <th><?= Yii::t("fe", "Aksi") ?></th>
                    </tr>
                </thead>
                <tbody class="data-asuransi"></tbody>
            </table>
        </div>
    </div>
</div>
<?php $this->registerJs($this->render('js/pencarian_asuransi.js'), View::POS_END);?>
