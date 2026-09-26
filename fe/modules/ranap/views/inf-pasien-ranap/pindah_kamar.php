<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Rawat inap'), 'url' => ['/ranap/dashboard']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                 <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= $title ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'pindah-kamar' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-floppy-o',
                        'method' => 'not exist',
                        'attributes' => [
                            'id' => 'btn-pindah-kamar',
                            'data-options' => 'click',
                            'class' => 'bg-teal data-simpan',
                        ]
                    ],
                    // 'custom-reset'=>[
                    //     'type' => 'click',
                    //     'title' => \Yii::t('fe', 'Muat Ulang'),
                    //     'icon' => 'fa fa-refresh',
                    //     'attributes' => [
                    //         'id' => 'btn-reset',
                    //     ]
                    // ],
                    'back',
                ]);
                ?>
            </div>

            <div class="panel-body">
                <!-- identitas pasien start -->
                <?=Yii::$app->controller->renderPartial('/pemeriksaan-rawat-inap/_pasien_identitas', [
                    'data_pasien' => $data_pasien
                ]);?>
                <!-- identitas pasien end -->

                <div class="row">
                    <div class="col-md-12">
                        <hr>
                    </div>
                </div>

                <!-- form pindah kamar start -->
                <div class="row">
                    <div class="col-md-12">
                        <?php echo Yii::$app->controller->renderPartial('_form_pindah_kamar', [
                            'data_pasien'=>$data_pasien,
                            'data_master'=>$data_master,
                            'model'=>$model
                        ]);?>
                    </div>
                </div>
                <!-- form pindah kamar end -->
            </div>
        </div>
    </div>
</div>

<div id="modalTempatTidur" class="modal fade in" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title">Pilih Tempat Tidur</h5>
            </div>
            <div class="modal-body">
                <div class="row" id="row-warna-kettempattidur">
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
                        <label for="kamar_titipan">Kamar Titipan</label>
                    </div>
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
                                    <th><?=\Yii::t("fe", "Kelas");?></th>
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
                </div>
            </div>
        </div>
    </div>
</div>