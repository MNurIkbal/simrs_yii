<?php 

use yii\helpers\Html;
use yii\web\View;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
?>

<div class="col-md-9" id="informasi">
    <?php if ($is_ranap && !empty($konfirmasi_match) ): ?>
        <div class="panel panel-default panel-bordered">
            <div class="panel-heading">
                <h6 class="panel-title"><?= Yii::t('fe', 'Status Konfirmasi Pulang Ranap') ?></h6>
                <div class="heading-elements">
                    <ul class="icons-list"></ul>
                </div>
            </div>
            <div class="panel-body">
                <div class="row">
                        <?php foreach ($konfirmasi_match as $value) {
                            $instalasi_nama = isset($value['instalasi_nama']) ? $value['instalasi_nama'] : '';
                              echo "<div class='col-xs-2'>".$instalasi_nama;
                              if($value['is_konfirmasi']){
                                $confirm = "</br></br><span><b class='text-left control-label font-design' >Sudah Konfirmasi</b></span>";
                                } else $confirm = "</br></br><span style='color:red'><b>Belum Terkonfirmasi</b></span>";
                              echo $confirm;
                              echo "</div>";                            
                            }
                        ?>
                </div>
                </br>
            </div>               
        </div>               
    <?php endif; ?>
    <div class="panel panel-default">
        <a id="info-heading" data-toggle="collapse" href="#infopasien" role="button" aria-expanded="false" aria-controls="infopasien" >
            <div class="panel-heading flex-container">
                <h6 class="panel-title"><?= Yii::t('fe', 'Informasi Pasien') ?></h6>
                <p class="p-data" id="data-pasien">
                    <?= isset($model->no_rekam_medik) ? $model->no_rekam_medik : '-' ?> -
                    <b class="font" ><?= isset($model->nama_pasien) ? $model->nama_pasien : '-' ?></b>
                </p>
                <ul class="icons-list">
                    <li><i id="chevron" class="fa fa-chevron-down"></i></li>
                </ul>
            </div>
        </a>

        <div class="panel-body collapse multi-collapse info-card" id="infopasien">
            <div class="col-xs-2">
                <div class="border-img">
                    <?php
                    $filename = isset($model->photopasien) ? !empty($model->photopasien) ? '/media/img/pasien/'.$model->photopasien: '/media/img/icon-app/default.jpg' : '/media/img/icon-app/default.jpg';
                    ?>
                    <?=Html::img($filename, [ 'style'=>'width: 100%;height: auto;max-width: 114px;', 'class'=>'img-responsive'])?>
                </div>
            </div>

            <div class="col-xs-9">
                <div class="row">
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Nama Pasien") ?></b>
                        <br>
                        <p>
                            <?= isset($model->nama_pasien) ? $model->nama_pasien : '-' ?>
                        </p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Alamat Pasien") ?></b>
                        <br>
                        <p>
                            <?= isset($model->alamat_pasien) ? $model->alamat_pasien : '-' ?>
                        </p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Lahir") ?></b>

                        <br>
                        <p>
                            <?= isset($model->tanggal_lahir) ? date('d-M-Y', strtotime($model->tanggal_lahir)) : '-' ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "No Rekam Medik") ?></b>
                        <br>
                        <p>
                            <?= isset($model->no_rekam_medik) ? $model->no_rekam_medik : '-' ?>
                        </p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Pendaftaran") ?></b>

                        <br>
                        <p>
                            <?= isset($model->tgl_pendaftaran) ? date('d-M-Y', strtotime($model->tgl_pendaftaran)) : '-' ?>
                        </p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "No Pendaftaran") ?></b>
                        <br>
                        <p>
                            <?= isset($model->no_pendaftaran) ? $model->no_pendaftaran : '-' ?>
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Instalasi Akhir") ?></b>
                        <br>
                        <p>
                            <?= isset($model->instalasi_nama) ? $model->instalasi_nama : '-' ?>
                        </p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Ruangan Akhir") ?></b>
                        <br>
                        <p>
                            <?= !empty($model->ruangan_nama) ? $model->ruangan_nama : null ?>
                        </p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Cara Bayar") ?></b>
                        <br>
                        <p>
                            <?= $labelCaraBayar ?> 
                        </p>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Penjamin") ?></b>

                        <br>
                        <p>
                            <?= isset($model->penjamin_nama) ? $model->penjamin_nama : '-' ?>
                        </p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas Pelayanan") ?></b>

                        <br>
                        <p>
                            <?= isset($model->kelaspelayanan_nama) ? $model->kelaspelayanan_nama : '-' ?>
                        </p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Keluar") ?></b>

                        <br>
                        <p>
                            <?= isset($model->tglpasienpulang) ? date('d-M-Y', strtotime($model->tglpasienpulang)) : '-' ?>
                        </p>
                    </div>
                    <div class="col-xs-4">
                        <b class="text-left control-label font-design"><?= Yii::t("fe", "Hak Kelas") ?></b>

                        <br>
                        <p>
                            <?= isset($model->hak_kelas) ? 'Kelas ' .$model->hak_kelas : '-' ?>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--Detail Transaksi-->
    <?= Yii::$app->controller->renderPartial('_detail_transaksi', [
        'model' => $model,
        'form' => $form,
        'caraBayar' => $caraBayar,
        'penjamin' => $penjamin,
        'disabled' => $disabled,
        'isKarcis' => $isKarcis,
        'jenisAntrian' => $jenisAntrian,
        'id' => $id,
        'adm_persen' => $adm_persen,
        'biaya_adm_maksimal' => $biaya_adm_maksimal,
        'konfigSistem' => $konfigSistem,
        'listPenjamin' => $listPenjamin,
        'totalTagihan' => $totalTagihan,
        'total_admin' => $total_admin
    ]) ?>

    <!--Catatan-->
    <?= Yii::$app->controller->renderPartial('_catatan', [
        'model' => $model,
        'form' => $form,
    ]) ?>
</div>

<?php
// Ini untuk Init data di js
$_cara_bayar = !empty($model->carabayar_nama) ? $model->carabayar_nama : '-';
$_penjamin = !empty($model->penjamin_nama) ? $model->penjamin_nama : '-';

$this->registerJs('
getcarabayar = "'.$_cara_bayar.'";
getpenjamin = "'.$_penjamin.'";
', View::POS_END);
?>