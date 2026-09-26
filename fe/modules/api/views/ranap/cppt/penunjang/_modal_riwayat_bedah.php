<?php
    /**
     * @author Chacha Nurholis (chacha@sirs.co.id)
     * A Product of PT Citraraya Nusatama
     * Powered by Sirs
     */

    use yii\web\View;
    use yii\helpers\Html;

    $noRm              = $noRm;
    $nama_pasien       = $dataPasien['nama_pasien'];
    $no_rekam_medik    = $dataPasien['no_rekam_medik'];
    $no_telepon_pasien = $dataPasien['no_telepon_pasien'];
    $umur              = $dataPasien['umur'];
    $jenis_kelamin     = $dataPasien['jenis_kelamin'];
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="form-horizontal">
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Nama Pasien
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?= $nama_pasien ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Nomor Rekam Medik
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?= $no_rekam_medik ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Nomor Telepon Pasien
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?= $no_telepon_pasien ?></p>
                    </div>
                </div>
                <div class="form-group">
                    <label class="control-label text-bold col-sm-3">
                        Umur / Jenis Kelamin
                    </label>
                    <div class="col-sm-8">
                        <p class="form-control-static">: <?= $umur ?> / <?= $jenis_kelamin ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <table id="table-riwayat" class="table table-striped table-condensed table-hover" style="width:100%">
                <thead class="text-center">
                    <tr class="bg-inverse">
                        <th><?= Yii::t('fe', 'No') ?></th>
                        <th><?= Yii::t('fe', 'Nomor Pemeriksaan') ?></th>
                        <th><?= Yii::t('fe', 'Tangaal Permintaan') ?></th>
                        <th><?= Yii::t('fe', 'Jenis Pemeriksaan') ?></th>
                        <th><?= Yii::t('fe', 'Nama Pemeriksaan') ?></th>
                        <th><?= Yii::t('fe', 'Status') ?></th>
                        <th><?= Yii::t('fe', 'Disetujui Oleh') ?></th>
                        <th><?= Yii::t('fe', 'Tanggal Disetujui') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Nullable -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="modal-footer">
    <?= 
        Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
            'class'        => 'btn bg-slate',
            'data-dismiss' => 'modal'
        ])
    ?>
</div>
<?php
    $dataView = [
        'noRm' => $noRm
    ];
    $this->registerJsVar("dataView", $dataView);
    $this->registerJs($this->render("js/index.js"), View::POS_END, 'js');
?>