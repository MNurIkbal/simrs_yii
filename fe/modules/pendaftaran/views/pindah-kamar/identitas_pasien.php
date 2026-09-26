<?php

use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\web\View;

?>

<?= Html::hiddenInput('penjamin_id', null, ['class' => 'penjamin_id', 'readonly' => 'readonly']) ?>
<?= Html::hiddenInput('carabayar_id', null, ['class' => 'carabayar_id', 'readonly' => 'readonly']) ?>
<?= Html::hiddenInput('jeniskelamin_id', null, ['class' => 'jeniskelamin_id', 'readonly' => 'readonly']) ?>

<div class="panel panel-default">
    <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="true" aria-controls="infopasien">
        <div class="panel-heading flex-container">
            <h6 class="panel-title informasi_pasien">
                <span><b><?= Yii::t('fe', 'INFORMASI PASIEN') ?></b></span>
            </h6>
            
            <ul class="icons-list">
                <li><i id="chevron" class="fa fa-chevron-down"></i></li>
            </ul>
        </div>
    </a>

    <div class="panel-body column-info multi-collapse in" id="infopasien">
        <div class="flex-container">
            <div class="flex-info-pasien">
                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t('fe', 'PASIEN') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="no_rekam_medik"></span> - 
                    <span class="nama_pasien"></span> - 
                    <span class="jenis_kelamin"></span>
                </p>
            
                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'PENDAFTARAN') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="no_pendaftaran"></span> - 
                    <span class="tgl_pendaftaran"></span>
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'TANGGAL LAHIR') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="tanggal_lahir"></span> (<span class="umur"></span>)
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'KELAS PELAYANAN') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="kelaspelayanan_nama"></span> - 
                    <span class="carabayar_nama"></span> - 
                    <span class="penjamin_nama"></span>
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'KELAS TAGIHAN') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="kelas_ditagihkan_nama"></span> - 
                    <span class="ruangan_titipan_nama"></span> - 
                    <span class="kamar_titipan_nama"></span>
                </p>
            </div>
            <div class="flex-info-pasien">
                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t('fe', 'DOKTER DPJP') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="dokter_dpjp"></span>
                </p>
            
                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'KASUS PENYAKIT') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="jeniskasuspenyakit_nama"></span>
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'RUANGAN') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="info_pasien_ruangan_nama"></span> - 
                    <span class="info_pasien_kamarruangan_nokamar"></span> - 
                    <span class="no_tempattidur"></span>
                </p>

                <label class="text-left control-label col-sm-12 font-design">
                    <b><?= Yii::t("fe", 'Status') ?></b>
                </label>
                <p class="col-sm-12">
                    <span class="stat_ranap"></span>
                </p>
            </div>
        </div>
    </div>
</div>