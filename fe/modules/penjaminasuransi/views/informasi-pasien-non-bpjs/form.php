<?php

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;

use kartik\datetime\DateTimePicker;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style>
    .datepicker>div{
        display:block;
    }
</style>

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
                                <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias", $this->title); ?></b></h3>
                                <?= Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs'])); ?>
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
                <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'),
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'koreksi-pasien'
                    ]);
                ?>
                <?php
                    $idSkd = isset($skd['suratketdokter_id']) ? DocoHelpers::encrypt($skd['suratketdokter_id']) : null;
                ?>
                <?= Html::button('<b><i class="fa fa-print"></i></b>'.Yii::t('fe', ' Cetak'),
                    [
                        'class' => 'btn btn-info btn-labeled btn-xs',
                        'id' => 'cetak-skd',
                        'data-url' => '/penjamin-asuransi/informasi-pasien-non-bpjs/cetak-skd?id='.$idSkd,
                        'disabled' => !empty($skd) ? false : true
                    ]);
                ?>
                <?= DocoHelpers::generateToolbar([
                    'back',
                ]) ?>
            </div>
            <div class="panel-body">
               <!-- pannel detail pasien -->
               <div class="col-md-12">
                    <?=
                        $this->render('partial/info-pasien', [
                            'data' => $data
                        ])
                    ?>
               </div>
               <!-- pannel detail pasien -->
               <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe', 'Surat Keterangan Dokter'); ?></b></h6>
                        </div>
                        <div class="panel-body">
                            <?php
                                $form = ActiveForm::begin([
                                    'id' => 'ajax-form',
                                    'action' => "/penjamin-asuransi/informasi-pasien-non-bpjs/save?id={$id}&admisi=$admisi",
                                    'enableAjaxValidation'=>false,
                                    'enableClientValidation'=>false,
                                    'type' => ActiveForm::TYPE_HORIZONTAL,
                                    'formConfig' => [
                                        'labelSpan' => 3,
                                        'deviceSize' => ActiveForm::SIZE_SMALL
                                    ],
                                ]);
                            ?>
                            <div class="row">
                                <div class="form-group required">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('tgl_gejala') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-5">
                                            <?php $model->tgl_gejala = !empty($model->tgl_gejala)
                                                    ? date('d-M-Y',strtotime($model->tgl_gejala)) : date('d-M-Y'); ?>
                                            <?= $form->field($model, 'tgl_gejala',[
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->widget(DatePicker::classname(), [
                                                'name' => 'date_12',
                                                'value' => date('dd-M-yyyy'),
                                                'readonly' => true,
                                                'language' => 'en',
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-M-yyyy',
                                                    'endDate' => "0d",
                                                ]
                                            ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group required">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('tgl_konsul') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-5">
                                            <?php $model->tgl_konsul =  !empty($model->tgl_konsul)
                                            ? date('d-M-Y',strtotime($model->tgl_konsul)) : date('d-M-Y'); ?>
                                            <?= $form->field($model, 'tgl_konsul',[
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->widget(DatePicker::classname(), [
                                                'name' => 'date_12',
                                                'value' => date('dd-M-yyyy'),
                                                'readonly' => true,
                                                'language' => 'en',
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-M-yyyy',
                                                    'endDate' => "0d",
                                                ]
                                            ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('gejala_penyakit') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-5">
                                            <?= $form->field($model, 'gejala_penyakit', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('diag_utama') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-5">
                                        <?php
                                            $defaultUtama = $diagUtama = [];
                                            if (isset($detail['Utama'])) {
                                                $key = $detail['Utama']['id'];
                                                $value = $detail['Utama']['text'];
                                                $diagUtama = $detail['Utama'];
                                                $defaultUtama[$key] = $value;
                                            }

                                            if (!empty($model->diag_utama)) {
                                                $diagUtama = json_decode($model->diag_utama,true);
                                                $key = isset($diagUtama['id']) ? $diagUtama['id'] : null;
                                                $value = isset($diagUtama['text']) ? $diagUtama['text'] : null;
                                                $defaultUtama[$key] = $value;
                                            }
                                        ?>
                                        <div class="col-md-12">
                                            <?= Html::dropDownList('SuratKeteranganDokterForm[diag_utama]', null, $defaultUtama, [
                                                'class' => 'select2-skd',
                                                'data-url' => '/penjamin-asuransi/informasi-pasien-non-bpjs/get-icd?type=ICD X',
                                                'id' => 'diag_utama'
                                            ]) ?>
                                        </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('diag_tambahan') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-5">
                                            <div class="col-md-12">
                                                <?= Html::dropDownList('SuratKeteranganDokterForm[diag_tambahan]', null, [], [
                                                    'class' => 'select2-skd',
                                                    'data-url' => '/penjamin-asuransi/informasi-pasien-non-bpjs/get-icd?type=ICD X',
                                                    'id' => 'diag_tambahan'
                                                ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="col-md-5 list-diagnosa">
                                        <?php
                                            $detailTambah = [];
                                            $listDiagnosa = [];
                                            if (isset($detail['Peyerta']) || !empty($model->diag_tambahan)) :
                                                $detailTambah = !empty($model->diag_tambahan)
                                                    ? json_decode($model->diag_tambahan,true) : $detail['Peyerta'];
                                                foreach ($detailTambah as $value) {
                                                    if (isset($value['id'])) :
                                                        $listDiagnosa[$value['id']] = $value;
                                        ?>
                                          <div class="col-md-12 div-parent" style="margin-top : 7px;">
                                                <div class="col-md-11">
                                                    <li><b><?= $value['text'] ?></b></li>
                                                </div>
                                                <div class="col-md-1 text-right">
                                                    <?=
                                                        Html::button(
                                                            "<i class='fa fa-trash'></i>",[
                                                                'style' => 'margin-right:5px',
                                                                'class' => 'btn btn-danger btn-xs delete ',
                                                                'style' => 'margin-right:5px; padding-left:10px !important;',
                                                                'data-id' => $value['id'],
                                                            ]
                                                        )
                                                    ?>
                                                </div>
                                            </div>
                                        <?php
                                                    endif;
                                                }
                                            endif;
                                        ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('faktor_penyebab') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-5">
                                            <?= $form->field($model, 'faktor_penyebab', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('tgl_diagnosa') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-5">
                                            <?php
                                                $model->tgl_diagnosa = !empty($model->tgl_diagnosa)
                                                ? date('d-M-Y',strtotime($model->tgl_diagnosa)) : date('d-M-Y');
                                                $data['tgl_pendaftaran'] = !empty($data['tgl_pendaftaran'])
                                                ? $data['tgl_pendaftaran'] : date('Y-m-d');
                                                $pendaftaran = date('Y-m-d', strtotime($data['tgl_pendaftaran']));
                                                $datetime1 = new DateTime($pendaftaran);
                                                $datetime2 = new DateTime(date('Y-m-d'));
                                                $interval = $datetime1->diff($datetime2);
                                                $interval = $interval->format('%ad');
                                            ?>
                                            <?= $form->field($model, 'tgl_diagnosa',[
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->widget(DatePicker::classname(), [
                                                'name' => 'date_12',
                                                'value' => date('dd-M-yyyy'),
                                                'readonly' => true,
                                                'language' => 'en',
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-M-yyyy',
                                                    'endDate'=> '0d',
                                                    'todayHighlight' => true
                                                ]
                                            ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('terapi_tindakan') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'terapi_tindakan', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                    'id' => 'terapi_tindakan'
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group jenis_operasi">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b>Jenis Operasi</b>
                                    </label>
                                    <div class="col-md-9">
                                        <?php
                                            $jenis_operasi = DocoConstants::$jenis_operasi;
                                            foreach ($jenis_operasi as $key => $value) :
                                        ?>
                                            <div class="col-md-1">
                                                <?= $form->field($model, 'jenis_operasi',[
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                    ])->radio([
                                                        'label' => $value,
                                                        'value' => $key,
                                                        'uncheck' => null,
                                                        'class' => 'styled action-checked'
                                                    ]) ?>
                                            </div>
                                        <?php
                                            endforeach;
                                        ?>
                                    </div>
                                    <div class="col-sm-3" style="margin-left:31px;">
                                    </div>
                                    <div id="error_SuratKeteranganDokterFormjenis_operasi" class="col-sm-offset-3"></div>
                                </div>

                                <div class="form-group jenis_operasi">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('dokbedah_id') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <div class="col-md-9">
                                            <?= Html::dropDownList('SuratKeteranganDokterForm[dokbedah_id]',
                                                $model->dokbedah_id
                                                , $defaultDokter, [
                                                'class' => 'form-control select2-skd',
                                                'data-url' => '/penjamin-asuransi/informasi-pasien-non-bpjs/get-dokter'
                                            ]) ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('hasil_penunjang') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'hasil_penunjang', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required">
                                    <label class="control-label text-left control-label col-sm-3 ">
                                        <b><?= $model->getAttributeLabel('sebebdiagnosa_id') ?></b>
                                    </label>
                                    <?php
                                        $group = [];
                                        $no = 1;
                                        if (!empty($model->sebebdiagnosa_id)) :
                                            $sebabDiagnosa = json_decode($model->sebebdiagnosa_id,true);
                                            $model->sebebdiagnosa_id = [];
                                            if (is_array($sebabDiagnosa)) :
                                                foreach ($sebabDiagnosa as $value) :
                                                    if (!isset($model->sebebdiagnosa_id[$value])) :
                                                        $model->sebebdiagnosa_id[$value] = $value;
                                                    endif;
                                                endforeach;
                                            endif;
                                        endif;
                                        foreach ($sebab as $value) :
                                            $count = isset($group[$no]) ? count($group[$no]) : 0;
                                            if ($count != 0 && !($count%2)) :
                                                $no++;
                                            endif;
                                            $group[$no][] = $value;
                                        endforeach;

                                        foreach ($group as $key => $value) :
                                            if ($key != 1) :
                                    ?>
                                        <div class="col-md-3"></div>
                                    <?php
                                            endif;
                                    ?>
                                        <div class="col-md-9">
                                    <?php
                                            foreach ($value as $k => $v) :
                                                $valCheck = $v['sebabdiagnosa_id'];
                                    ?>
                                            <div class="text-left col-md-3">
                                                <?= $form->field($model, 'sebebdiagnosa_id['. $valCheck .']',[
                                                            'options' => [
                                                                        'tag' => false,
                                                                    ],
                                                    ])->checkbox([
                                                        'label' => $v['sebabdiagnosa_nama'],
                                                        'value' => $valCheck,
                                                        'checked' => 'checked',
                                                        'class' => 'styled action-checked'
                                                    ])->label(false);
                                                ?>
                                            </div>
                                    <?php
                                            endforeach;
                                    ?>
                                        </div>
                                    <?php
                                        endforeach;
                                    ?>
                                </div>

                                <div class="form-group">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('is_kecelakaaan') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'is_kecelakaaan', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->checkbox([
                                                    'label' => 'Ya',
                                                    'value' => 1,
                                                    'checked' => true,
                                                    'class' => 'styled action-checked',
                                                    'id' => 'is_kecelakaaan'
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required is_kecelakaaan">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('tgl_kecelakaan') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-5">
                                            <?php $model->tgl_kecelakaan = !empty($model->tgl_kecelakaan)
                                            ? date('d-M-Y',strtotime($model->tgl_kecelakaan)) : date('d-M-Y'); ?>
                                            <?= $form->field($model, 'tgl_kecelakaan',[
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->widget(DatePicker::classname(), [
                                                'name' => 'date_12',
                                                'value' => date('dd-M-yyyy'),
                                                'readonly' => true,
                                                'language' => 'en',
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-M-yyyy',
                                                    'endDate' => "0d",
                                                ]
                                            ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required is_kecelakaaan">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('sebab_kecelakaan') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'sebab_kecelakaan', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('is_diag_sama') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-1">
                                            <?php $model->is_diag_sama = isset($model->is_diag_sama)
                                                    ? $model->is_diag_sama ? $model->is_diag_sama : 0 : null; ?>
                                            <?= $form->field($model, 'is_diag_sama',[
                                                'options' => [
                                                            'tag' => false,
                                                        ],
                                                ])->radio([
                                                    'label' => 'Ya',
                                                    'value' => 1,
                                                    'uncheck' => null,
                                                    'class' => 'styled action-checked is_diag',
                                                    'id' => 'is_diag_sama'
                                                ]) ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?= $form->field($model, 'is_diag_sama',[
                                                'options' => [
                                                            'tag' => false,
                                                        ],
                                                ])->radio([
                                                    'label' => 'Tidak',
                                                    'value' => 0,
                                                    'uncheck' => null,
                                                    'class' => 'styled action-checked is_diag',
                                                    'id' => 'is_diag_sama'
                                                ]) ?>
                                        </div>
                                    </div>
                                    <div id="error_SuratKeteranganDokterFormis_diag_sama"></div>
                                </div>

                                <div class="form-group required is_diag_sama">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('tgl_diag_sama') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-5">
                                            <?php $model->tgl_diag_sama = !empty($model->tgl_diag_sama)
                                            ? date('d-M-Y',strtotime($model->tgl_diag_sama)) : date('d-M-Y'); ?>
                                            <?= $form->field($model, 'tgl_diag_sama',[
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->widget(DatePicker::classname(), [
                                                'name' => 'date_12',
                                                'value' => date('dd-M-yyyy'),
                                                'readonly' => true,
                                                'language' => 'en',
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-M-yyyy',
                                                    'endDate' => "0d",
                                                ]
                                            ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group required is_diag_sama">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('diag_sama') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'diag_sama', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group required is_diag_sama">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('nama_rs') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'nama_rs', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group required is_diag_sama">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('nama_dokter_rs') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'nama_dokter_rs', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group required">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('is_konsultasi') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-1">
                                            <?php $model->is_konsultasi = isset($model->is_konsultasi)
                                                    ? $model->is_konsultasi ? $model->is_konsultasi : 0 : null; ?>
                                            <?= $form->field($model, 'is_konsultasi',[
                                                'options' => [
                                                            'tag' => false,
                                                        ],
                                                ])->radio([
                                                    'label' => 'Ya',
                                                    'value' => 1,
                                                    'uncheck' => null,
                                                    'class' => 'styled action-checked is_konsul',
                                                    'id' => 'is_konsultasi'
                                                ]) ?>
                                        </div>
                                        <div class="col-md-1">
                                            <?= $form->field($model, 'is_konsultasi',[
                                                'options' => [
                                                            'tag' => false,
                                                        ],
                                                ])->radio([
                                                    'label' => 'Tidak',
                                                    'value' => 0,
                                                    'uncheck' => null,
                                                    'class' => 'styled action-checked is_konsul',
                                                    'id' => 'is_konsultasi'
                                                ]) ?>
                                        </div>
                                    </div>
                                    <div id="error_SuratKeteranganDokterFormis_konsultasi"></div>
                                </div>

                                <div class="form-group required is_konsultasi">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('tgl_konsultasi') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-5">
                                            <?php $model->tgl_konsultasi = !empty($model->tgl_konsultasi)
                                            ? date('d-M-Y',strtotime($model->tgl_konsultasi)) : date('d-M-Y'); ?>
                                            <?= $form->field($model, 'tgl_konsultasi',[
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->widget(DatePicker::classname(), [
                                                'name' => 'date_12',
                                                'value' => date('dd-M-yyyy'),
                                                'readonly' => true,
                                                'language' => 'en',
                                                'pluginOptions' => [
                                                    'autoclose' => true,
                                                    'format' => 'dd-M-yyyy',
                                                    'endDate' => "0d",
                                                ]
                                            ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group required is_konsultasi">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('diag_konsultasi') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'diag_konsultasi', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group required is_konsultasi">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('nam_rs_konsul') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'nam_rs_konsul', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group required is_konsultasi">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('nama_dr_konsul') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'nama_dr_konsul', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('is_rujukan') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'is_rujukan', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->checkbox([
                                                    'label' => 'Ya',
                                                    'value' => 1,
                                                    'checked' => true,
                                                    'class' => 'styled action-checked',
                                                    'id' => 'is_rujukan'
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required is_rujukan">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('dokter_rujukan') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'dokter_rujukan', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group required is_rujukan">
                                    <label class="control-label text-left control-label col-sm-3">
                                        <b><?= $model->getAttributeLabel('alamat') ?></b>
                                    </label>
                                    <div class="col-md-9">
                                        <div class="col-md-9">
                                            <?= $form->field($model, 'alamat', [
                                                    'options' => [
                                                                'tag' => false,
                                                            ],
                                                ])->textInput([
                                                    'class' => 'form-control input-sm',
                                                    'autocomplete' => "off",
                                                ])->label(false); ?>
                                        </div>
                                    </div>
                                </div>


                            </div>
                        <?php ActiveForm::end(); ?>
                    </div>
               </div>
            </div>
        </div>
    </div>
</div>
<?php
$this->registerJs("
    var _infoPasien = ". json_encode($data) .";
    var _listDiagnosa = ". json_encode($listDiagnosa) .";
    var _diagUtama = ". json_encode($diagUtama) .";

    $(document).on('click','#cetak-skd', function (event) {
        event.preventDefault();
        window.open($(this).attr('data-url'), '_blank');
    });

    $(document).on('keyup', '#terapi_tindakan', function (event) {
        if ($(this).val()) {
             $('.jenis_operasi').addClass('required');
        } else {
            $('.jenis_operasi').removeClass('required');
        }
    })

    $(document).on('click', '.delete', function (event) {
        event.preventDefault();
        var _divParent = $(this).closest('div.div-parent');
        var _idParent = $(this).attr('data-id');
        _divParent.remove();
        delete _listDiagnosa[_idParent];
    });

    $(document).on('change','#diag_utama', function(e) {
        var _data = $(this).select2('data')[0];
        if ($(this).val()) {
            var _id = _data.id;
            var _kode = _data.kode;
            var _text = _data.text;
            _diagUtama = {
                id : _id,
                kode : _kode,
                text : _text
            };
        }
    });

    $(document).on('change','#diag_tambahan', function(e) {
        var _data = $(this).select2('data')[0];
        if ($(this).val()) {
            if (typeof _listDiagnosa[_data.id] != 'undefined') {
                docoNotification('error','Proses Gagal!', 'Diagnosa tambahan sudah ada');
                return false;
            }
            var _id = _data.id;
            var _kode = _data.kode;
            var _text = _data.text;
            _listDiagnosa[_id] = {
                id : _id,
                kode : _kode,
                text : _text
            };
            var _html = '<div class=\"col-md-12 div-parent\" style=\"margin-top : 7px;\">';
                    _html += '<div class=\"col-md-11\">';
                        _html += '<li><b>'+ _text +'</b></li>';
                    _html += '</div>';
                    _html += '<div class=\"col-md-1 text-right\">';
                        _html += '<button type=\"button\" class=\"btn btn-danger btn-xs delete\" action=\"\" style=\"margin-right:5px; padding-left:10px !important;\" data-id=\"'+ _data.id +'\">';
                            _html += '<i class=\"fa fa-trash\"></i>';
                        _html += '</button>';
                    _html += '</div>';
                _html += '</div>';
            $('.list-diagnosa').append(_html);
            $(this).val('').trigger('change');
        }
    });

    var _hideValidation = function () {
        $('div').removeClass('has-error');
        $('span.help-block.error').remove();
        $('div.help-block.error').remove();
    }
    $('#koreksi-pasien').on('click', function (event) {
        event.preventDefault();
        var _data = $('#ajax-form').serializeArray();

        _data.push({
            name : 'list_diagnosa',
            value : JSON.stringify(_listDiagnosa)
        });

        _data.push({
            name : 'diagnosa_utama',
            value : JSON.stringify(_diagUtama)
        });

        _data.push({
            name : 'instalasi_id',
            value : _infoPasien.instalasi_id
        });

        _data.push({
            name : 'pasien_id',
            value : _infoPasien.pasien_id
        });

        $('#ajax-form').docoForm('submit',{
            data : _data,
            success : function (data) {
                var _idParent = data.response.id_parent;
                $('#cetak-skd').prop('disabled',false);
                $('#cetak-skd').attr('data-url','/penjamin-asuransi/informasi-pasien-non-bpjs/cetak-skd?id='+_idParent);
                (new PNotify({
                    title: '&nbsp;Proses Berhasil !',
                    text: 'Data Berhasil disimpan, apakah Anda ingin melakukan cetak?',
                    addclass: 'alert alert-success alert-arrow-right alert-styled-right',
                    type: 'success',
                    buttons: {
                        closer: false,
                        sticker: false
                    },
                    hide: false,
                    confirm: {
                        confirm: true,
                        buttons: [
                            {
                                text: 'Ya',
                                addClass: 'btn btn-xs btn-success',
                            },
                            {
                                text: 'Tidak',
                                addClass: 'btn btn-xs btn-danger',
                            }
                        ]
                    },
                    history: {
                        history: false
                    }
                })).get().on('pnotify.confirm', function() {
                    // Print
                    window.open('/penjamin-asuransi/informasi-pasien-non-bpjs/cetak-skd?id='+_idParent);
                }).on('pnotify.cancel', function() {

                });
            }
        });
        $('#ajax-form').trigger('submit');
    })
    var _diagSama = function () {
        var _check = $('#is_diag_sama:checked').val();
        if (_check == 1) {
            $('.is_diag_sama').show();
        } else {
            _hideValidation();
            $('.is_diag_sama').hide();
        }
    }

    var _isKonsul = function () {
        var _check = $('#is_konsultasi:checked').val();
        if (_check == 1) {
            $('.is_konsultasi').show();
        } else {
            _hideValidation();
            $('.is_konsultasi').hide();
        }
    }

    var _isKecelakaan = function () {
        var _check = $('#is_kecelakaaan:checked').val();
        if (_check == 1) {
            $('.is_kecelakaaan').show();
        } else {
            _hideValidation();
            $('.is_kecelakaaan').hide();
        }
    }

    var _isRujukan = function () {
        var _check = $('#is_rujukan:checked').val();
        if (_check == 1) {
            $('.is_rujukan').show();
        } else {
            _hideValidation();
            $('.is_rujukan').hide();
        }
    }

    var _initSelect2 = function () {
        $.each($('.select2-skd'), function () {
            var _url = $(this).attr('data-url');
            $(this).select2({
                placeholder: 'Pilih',
                minimumInputLength: 3,
                ajax : {
                    url: _url,
                    dataType: 'json',
                    quietMillis: 250,
                    data: function (params) {
                      var query = {
                        search: params,
                      }
                      return params;
                    },
                    processResults: function (data) {
                      return {
                        results: data.result
                      };
                    },
                    dropdownCssClass: 'bigdrop',
                    escapeMarkup: function (m) { return m; },
                },
            });
        });
    }

    $('.is_diag').on('click', _diagSama);
    $('.is_konsul').on('click', _isKonsul);
    $('#is_kecelakaaan').on('click', _isKecelakaan);
    $('#is_rujukan').on('click', _isRujukan);
    $(function() {
        _diagSama();
        _isKonsul();
        _isKecelakaan();
        _isRujukan();
        _initSelect2();
        $('#terapi_tindakan').trigger('keyup');
        $('.styled, .multiselect-container input').uniform({
            radioClass: 'choice'
        });
        $('div.col-sm-offset-3').removeClass('col-sm-offset-3');
    })

", VIEW::POS_END, 'js-kunings');