<?php

/**
 * @author Rizal
 * @description UI Update Pendaftaran Rajal
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use kartik\typeahead\Typeahead;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
            </div>

            <div class="panel panel-white">
                <div class="panel-toolbar clearfix">
                    <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), [
                        'class' => 'btn btn-info btn-labeled btn-xs', 
                        'id' => 'btn-save'
                    ]) ?>
                    <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>' . \Yii::t('fe', 'Kembali'), [
                        'class' => 'btn btn-labeled btn-xs btn-info', 
                        'id' => 'btn-kembali'
                    ]) ?>
                    <?= Html::button('<b><i class="fa fa-pencil"></i></b>' . \Yii::t('fe', 'Buat SEP'), [
                        'class' => 'btn btn-labeled btn-xs btn-info', 
                        'id' => 'btn-create-sep'
                    ]) ?>
                </div>
            </div>

            <div class="panel-body" style="padding:10px;">
                <!--Informasi Pasien-->
                <div class="row row-eq-height " style="margin-top:10px;">
                    <div class="col-md-12" id="informasi">
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
                                        <br>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pasien") ?></b>
                                            <br>
                                            <p>
                                                <?= isset($model->no_rekam_medik) ? $model->no_rekam_medik : '-' ?> -
                                                <?= isset($model->nama_pasien) ? $model->nama_pasien : '-' ?> -
                                                <?= isset($model->jenis_kelamin) ? $model->jenis_kelamin : '-' ?>
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Tanggal Lahir") ?></b>
                                            <p>
                                                <?= isset($model->tanggal_lahir) ? date('d-M-Y', strtotime($model->tanggal_lahir)) : '-' ?>
                                            </p>

                                        </div>
                                        <div class="col-xs-6">
                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Pendaftaran") ?></b>
                                            <p>
                                                <?= isset($model->no_pendaftaran) ? $model->no_pendaftaran : '-' ?> -
                                                <?php if($model->jenis == 'ranap') : ?>
                                                (<?= isset($model->tgl_admisi) ? date('d-M-Y H:i:s', strtotime($model->tgl_admisi)) : '-' ?>)
                                                <?php else : ?>
                                                (<?= isset($model->tgl_pendaftaran) ? date('d-M-Y H:i:s', strtotime($model->tgl_pendaftaran)) : '-' ?>)
                                                <?php endif; ?>
                                            </p>

                                            <b class="text-left control-label font-design"><?= Yii::t("fe", "Kelas pelayanan") ?></b>
                                            <p>
                                                <?= isset($model->kelaspelayanan_nama) ? $model->kelaspelayanan_nama : '-' ?> -
                                                <?= isset($model->carabayar_nama) ? $model->carabayar_nama : '-' ?> -
                                                <?= isset($model->penjamin_nama) ? $model->penjamin_nama : '-' ?>
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!--end-->
            <?php 
                $form = ActiveForm::begin([
                    'id' => 'form-update-rajal', 
                    'type' => ActiveForm::TYPE_VERTICAL,
                    'enableClientValidation'=>false,
                    'enableAjaxValidation'=>false,
                ]); 
            ?>
            
            <?= Html::activeHiddenInput($model, 'pendaftaran_id')?>
            <?= Html::activeHiddenInput($model, 'group_carabayar')?>
            <?= Html::activeHiddenInput($model, 'jenis', ['id' => 'jenis']); ?>
            <?= Html::hiddenInput('bpjsKelas', "", ['id' => 'bpjsKelas']); ?>
            <?= Html::hiddenInput('EditPendaftaranForm[allow-bpjs]', 0, ['id' => 'allow-bpjs']); ?>
            
            <fieldset class="content-group">
                <div class="row" style="margin-top: 20px;">
                    <div class="col-md-6">	
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Ubah Pendaftaran Pasien</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">

                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php if($jenis_pendaftaran == 'ranap') : ?>
                                <?php echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/form_update_ranap', [
                                    'form' => $form,
                                    'model' => $model,
                                    'penyakitList' => $penyakitList,
                                    'kelasList' => $kelasList,
                                    'dokterList' => $dokterList,
                                    'instalasi_id' => $instalasi_id,
                                    'masterWarnaTempatTidur' => $masterWarnaTempatTidur,
                                    'disabled' => $disabled,
                                    'status_periksa' => $status_periksa,
                                    'jeniskelamin_id' => $jeniskelamin_id,
                                    'class' => $class,
                                    'status_periksa' => $status_periksa,
                                    'allDokterList' => $allDokterList,
                                    'prosedurMasukList' => $prosedurMasukList,
                                ]) ?>
                                <?php elseif($jenis_pendaftaran == 'penunjang') : ?>
                                <?php echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/form_update_penunjang', [
                                    'form' => $form,
                                    'model' => $model,
                                    'penyakitList' => $penyakitList,
                                    'dokterList' => $dokterList,
                                    'disabled' => $disabled,
                                    'nomorUrutList' => $nomorUrutList,
                                    'isNomorUrut' => $isNomorUrut,
                                    'ruanganList' => $ruanganList,
                                    'rujukandariList' => $rujukandariList,
                                    'instalasiList' => $instalasiList,
                                    'allDokterList' => $allDokterList,
                                ]) ?>
                                <?php elseif($jenis_pendaftaran == 'penunjang') : ?>
                                <?php echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/form_update_penunjang', [
                                    'form' => $form,
                                    'model' => $model,
                                    'penyakitList' => $penyakitList,
                                    'dokterList' => $dokterList,
                                    'disabled' => $disabled,
                                    'nomorUrutList' => $nomorUrutList,
                                    'isNomorUrut' => $isNomorUrut,
                                    'ruanganList' => $ruanganList,
                                    'rujukandariList' => $rujukandariList,
                                    'instalasiList' => $instalasiList,
                                ]) ?>
                                <?php else : ?>
                                <?php echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/form_update_rajal', [
                                    'form' => $form,
                                    'model' => $model,
                                    'penyakitList' => $penyakitList,
                                    'dokterList' => $dokterList,
                                    'disabled' => $disabled,
                                    'nomorUrutList' => $nomorUrutList,
                                    'isNomorUrut' => $isNomorUrut,
                                    'ruanganList' => $ruanganList,
                                    'allDokterList' => $allDokterList,
                                ]) ?>
                                <?php endif; ?>

                                <?= $form->field($model, 'carabayar_id')
                                ->dropDownList($carabayarList, [
                                    'class' => 'select2 selectCarabayar',
                                    'id'=>'selectCarabayar',
                                    'prompt' => '-',
                                    'options'=> $carabayarOptions
                                ])->label('Penanggung Biaya'); ?>
                                <?= $form->field($model, 'penjamin_id')->widget(DepDrop::classname(), [
                                    'options' => [
                                        'id' => 'penjamin_id',
                                        'class' => 'form-control select2'
                                    ],
                                    'data'=>$penjaminList,
                                    'pluginOptions'=>[
                                        'depends'=>['selectCarabayar'],
                                        'initialize' => true,
                                        'loadingText' => Yii::t('fe', 'Memuat...'),
                                        'placeholder'=>'--Pilih Penjamin--',
                                        'url'=>Url::to(['/master/penjamin/list-penjamin'])
                                    ]
                                ]); ?>
                                
                                <?= $form->field($model, 'nosep', [
                                    'inputOptions' => [
                                        'id' => 'no_sep',
                                        'readonly' => !empty($model->nosep) ? true : false
                                    ],
                                    'addon' => [
                                        'append' => [
                                            [
                                                'content' =>Html::button('<i class="fa fa-search"></i> Cari No SEP', [
                                                    'class'=>'btn btn-default', 
                                                    'id' => 'cari-nosep',
                                                ]),
                                                'asButton' => true
                                            ],
                                        ],
                                    ],
                                ])->hint('<div class="text-danger err-nosep"></div>');
                                ?>

                                <?= $form->field($model, 'keterangan')->textArea([], ['rows' => '6']);
                                ?>

                                <?php if (isset($isLimitTagihan) && $isLimitTagihan) { ?>
                                    <?= $form->field($model, 'limit_tagihan', [
                                        'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-7'
                                        ],
                                        'addon' => [
                                            'prepend' => [
                                                'asButton' => false,
                                                'content' => 'Rp.'
                                            ]
                                        ]
                                    ])->textInput([
                                            'placeholder' => $model->getAttributeLabel('limit_tagihan'),
                                            'class' => 'form-control input-sm doco-number',
                                            'id' => 'limit_tagihan'
                                    ]) ?>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 detail_peserta">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Informasi Peserta</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/_form_bpjs', [
                                    'model' => $model,
                                    'nama_peserta' => $nama_peserta,
                                    'no_kartu' => $no_kartu,
                                    'jenis_pelayanan' => $jenis_pelayanan,
                                    'poli_tujuan' => $poli_tujuan,
                                    'kelas_rawat' => $kelas_rawat,
                                    'tgl_lahir' => $tgl_lahir,
                                    'nmjenispeserta' => $nmjenispeserta
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 detail_asuransi">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Informasi Asuransi</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/_form_asuransi', [
                                    'form' => $form,
                                    'modelAsuransi' => $modelAsuransi,
                                    'kelasList' => $kelasList,
                                ]) ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 detail_penanggung">
                        <div class="panel panel-bordered">
                            <div class="panel-heading">
                                <h6 class="panel-title">Informasi Penanggung Biaya</h6>
                                <div class="heading-elements">
                                    <ul class="icons-list">
                                    </ul>
                                </div>
                            </div>
                            <div class="panel-body">
                                <?php echo Yii::$app->controller->renderPartial('@app/extensions/pendaftaran/views/informasi-pasien/partial/_form_penanggung_biaya', [
                                    'form' => $form,
                                    'modelPenanggungBiaya' => $modelPenanggungBiaya,
                                    'bagianList' => $bagianList,
                                ]) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </fieldset>
            <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs("
var _penjamin_id = '".$model->penjamin_id."';
var _nosep = '".$model->nosep."';
var _noasuransi = '".$modelAsuransi->nokartuasuransi."';
var _namapemilik = '".$modelAsuransi->namapemilikasuransi."';
var _namaperusahaan = '".$modelAsuransi->namaperusahaan."';
var _nomorpokokperusahaan = '".$modelAsuransi->nomorpokokperusahaan."';
var _masaberlakukartu = '".$modelAsuransi->masaberlakukartu."';
var ruangan_id = '".$model->ruangan_id."';
var _ruangcarabayar_id = '".$modelPenanggungBiaya->ruangcarabayar_id."';
var pegawai_id = '".$model->pegawai_id."';
var _jenis_pendaftaran = '".$jenis_pendaftaran."';
var _bagian = '".json_encode($dokterList)."';

", View::POS_END);
$this->registerJs($this->render('js/form_update.js'), View::POS_END);
?>