<?php

use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['/pendaftaran/daftar']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon") ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $title ?></b></h3>
                        <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])) ?>
                    </div>
                </div>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>

            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Cari Pasien'),
                        'icon' => 'fa fa-search',
                        'method' => 'not exist',
                        'attributes' => [
                            'id' => 'btn-cari',
                            'data-options' => 'click',
                            'class' => 'bg-teal btn btn-info btn-labeled btn-xs '.$hidden,
                        ]
                    ],
                    'reset' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Muat Ulang'),
                        'icon' => 'fa fa-refresh',
                        'method' => 'not exist',
                        'attributes' => [
                            'id' => 'btn-reset',
                            'data-options' => 'click',
                            'class' => 'bg-teal btn btn-info btn-labeled btn-xs',
                        ]
                    ],
                    'save' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-floppy-o',
                        'method' => 'not exist',
                        'attributes' => [
                            'id' => 'btn-simpan',
                            'data-options' => 'click',
                            'class' => 'bg-teal btn btn-info btn-labeled btn-xs',
                        ]
                    ]
                ]) ?>
            </div>

            <div class="panel-body">
                <div class="row" style="margin-bottom: 15px;" <?= $hidden ?>>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= Html::label(Yii::t('fe', 'No Pendaftaran'), 'no_pendaftaran', [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::textInput('no_pendaftaran', null, [
                                'id' => 'no_pendaftaran',
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <?= Html::label(Yii::t('fe', 'No Rekam Medik'), 'no_rekam_medik', [
                                'class' => 'control-label'
                            ]) ?>
                            <?= Html::textInput('no_rekam_medik', null, [
                                'id' => 'no_rekam_medik',
                                'class' => 'form-control'
                            ]) ?>
                        </div>
                    </div>
                </div>
                <div class="row identitas_pasien" hidden>

                    <?php if($hidden == ''):  ?>
                    <div class="col-md-12">
                        <hr>
                    </div>
                    <?php endif ?>
                    <div class="col-sm-12">
                        <?= Yii::$app->controller->renderPartial('identitas_pasien') ?>
                    </div>
                </div>
                <div class="row pindah_kamar_form" hidden>
                    <div class="col-sm-12">
                        <?= Yii::$app->controller->renderPartial('pindah_kamar_form', [
                            'model' => $model,
                            'dataMaster' => $dataMaster
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modalTempatTidur" class="modal fade in" data-backdrop="static">
    <div class="modal-dialog" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header bg-inverse">
                <button type="button" class="close" data-dismiss="modal">×</button>
                <h5 class="modal-title"><?= Yii::t('fe', 'Pilih Tempat Tidur') ?></h5>
            </div>
            <div class="modal-body">
                <div class="panel-button">
                    <!-- <div class="form-group"> -->
                        <!-- <input type="checkbox" name="kamarTitipan" id="kamarTitipanCheck" value="1"> -->
                        <!-- <label for="kamar_titipan"><?= Yii::t('fe', 'Kamar Titipan') ?></label> -->
                    <!-- </div> -->
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
                                    <th><?=\Yii::t("fe", "Kelas");?></th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Kamar");?></th>
                                    <th><?=\Yii::t("fe", "Harga Akomodasi");?></th>
                                    <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td colspan="5" class="text-center"><?= Yii::t('fe', 'Data tidak tersedia') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->registerJs("

    var nomor_pendaftaran = '".$no_pendaftaran."';
    var nomor_rm = '".$no_rm."';

    var input_date = $('#tanggal_lahir').pickadate({
        editable: true,
        format: 'dd-mm-yyyy',
        formatSubmit: 'dd-mm-yyyy',
        selectMonths: true,
        selectYears: true,
        min: [1900, 01, 01],
        max: true,
        onClose: function () {
            $('.datepicker').focus();
        }
    });
    var picker_date = input_date.pickadate('picker');
    $('.inline-datepicker').on('click', function () {
        if (picker_date.get('open')) {
            picker_date.close();
        } else {
            picker_date.open();
        }
        event.stopPropagation();
    }); 
   
    ", View::POS_END) ?>
