<?php

/**
 * @author Naufal Ziyad L
 * @Date 29/01/2018
**/

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;

$this->title = $title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Pendaftaran'), 'url' => ['index']];
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
                      <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
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
                <?php
                if (isset($modelPemesananKamar->nokamar)) {
                    $btn_simpan = [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Ubah'),
                        'icon' => 'fa fa-floppy-o',
                        'method' => 'not exist',
                        'attributes' => [
                            // 'id' => empty($modelPemesananKamar) ? 'btn-pesan-kamar' : 'btn-ubah-pesan-kamar',
                            'id' => 'btn-ubah-pesan-kamar',
                            'data-options' => 'click',
                            'class' => 'bg-teal data-simpan',
                        ]
                    ];
                } else {
                    $btn_simpan = [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-floppy-o',
                        'method' => 'not exist',
                        'attributes' => [
                            // 'id' => empty($modelPemesananKamar) ? 'btn-pesan-kamar' : 'btn-ubah-pesan-kamar',
                            'id' => 'btn-pesan-kamar',
                            'data-options' => 'click',
                            'class' => 'bg-teal data-simpan',
                        ]
                    ];
                }
                ?>
                <?=DocoHelpers::generateToolbar([
                        'pesan-kamar' => $btn_simpan,
                        'cetak' => [
                            'type' => 'button',
                            'title' => \Yii::t('fe', 'Print'),
                            'icon' => 'fa fa-print',
                            'method' => 'not exist',
                            'attributes' => [
                                'data-options'=>'click',
                                'data-target'=> Url::to(['cetak-pemesanan']). '?id=',
                                'disabled'=>'true',
                            ]
                        ],
                        'custom-reset'=>[
                            'type' => 'click',
                            'title' => \Yii::t('fe', 'Muat Ulang'),
                            'icon' => 'fa fa-refresh',
                            'attributes' => [
                                'id' => 'btn-reset',
                            ]
                        ],
                    ]);?>
            </div>

            <div class="panel-body">
            <?php
                $form = ActiveForm::begin([
                    'id'=>'pemesanan-kamar-form',
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'formConfig' => ['showErrors' => true,'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL, 'enableAjaxValidation' => false,'enableClientValidation' => true],
                ]);
            ?>
                <?= Html::hiddenInput('bookingkamar_id', isset($bookingkamar_id) ? $bookingkamar_id : null, ['id' => 'bookingkamar-id', 'readonly' => 'readonly']) ?>
                <?= Html::hiddenInput('norm', isset($modelPemesananKamar->no_rekam_medik) ? $modelPemesananKamar->no_rekam_medik : null, ['id' => 'norm', 'readonly' => 'readonly']) ?>
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h6 class="panel-title"><b><?=Yii::t('fe','Data Pasien')?></b></h6>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                    echo $form->field($modelPemesananKamar, 'pasien_id')->hiddenInput()->label(false);
                                ?>
                                <?= $form->field($modelPemesananKamar, 'no_rekam_medik')
                                    ->dropDownList(
                                        [],
                                        ['class'=>'select2  ddl_no_rekam_medik', 'prompt'=>Yii::t('fe', 'Ketik No Rekam Medik')]);
                                ?>
                                <?= $form->field($modelPemesananKamar, 'jenisidentitas')
                                    ->dropDownList(
                                        $ddlIdentitas,
                                        ['class'=>'select2', 'prompt'=>'— PILIH —'])
                                    ->label(Yii::t('fe', 'No Identitas Pasien'));
                                ?>
                                <?= $form->field($modelPemesananKamar, 'no_identitas_pasien')->textInput(['placeholder'=>'No Identitas Pasien'])->label(''); ?>
                                <?= $form->field($modelPemesananKamar, 'nama_pasien')->textInput(['placeholder'=>'Nama Lengkap Pasien']);?>
                                <?= $form->field($modelPemesananKamar, 'nama_bin')->textInput(['placeholder'=>'Alias'])->label('') ?>
                                <?= $form->field($modelPemesananKamar, 'tempat_lahir')->textInput(['placeholder'=>'Kota/Kabupaten Kelahiran']) ?>
                                <?= $form->field($modelPemesananKamar, 'tanggal_lahir', [
                                    'addon' => [
                                        'append' => [
                                            ['content' => '<i class="fa fa-calendar "></i>'],
                                        ],
                                    ] ])->textInput(['class' => 'pickadate-w-month','placeholder'=>'dd mm,yyyy']) ?>
                                <?= $form->field($modelPemesananKamar, 'umur')->textInput(['placeholder'=>'Otomatis Terhitung','readonly'=>true]); ?>
                            </div>
                            <div class="col-md-6">
                                <?= $form->field($modelPemesananKamar, 'jeniskelamin')
                                    ->radioList(
                                        $dllJenisKelamin,
                                        [
                                            'inline' => true,
                                            'itemOptions' => [
                                                'class' => 'jk'
                                            ]
                                        ]
                                    )
                                    ->label(Yii::t('fe', 'Jenis Kelamin'));
                                ?>
                                <?= $form->field($modelPemesananKamar, 'alamat_pasien')->textArea(['placeholder'=>'Alamat Lengkap Pasien']); ?>
                                <?= $form->field($modelPemesananKamar, 'no_telepon_pasien')->textInput(['placeholder'=>'No. Tlp/HP yang bisa dihubungi']) ?>
                                <?= $form->field($modelPemesananKamar, 'pekerjaan_id')
                                    ->dropDownList(
                                        $ddlPekerjaan,
                                        ['class'=>'select2', 'prompt'=>'— PILIH —']
                                    );
                                ?>
                                <?= $form->field($modelPemesananKamar, 'agama')
                                    ->dropDownList(
                                        $ddlAgama,
                                        ['class'=>'select2', 'prompt'=>'— PILIH —']
                                    );
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-white">
                    <div class="panel-heading">
                        <h6 class="panel-title"><b>Data Pemesanan</b></h6>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <?php
                                    if (!empty($id)) {
                                        echo $form->field($modelPemesananKamar, 'bookingkamar_no')->textInput([
                                                'class' => 'bookingkamar_no',
                                                'readonly' => true
                                            ]);
                                    }
                                ?>
                                <?php
                                    echo $form->field($modelPemesananKamar, 'tgl_transaksibooking', [
                                    'addon' => [
                                        'append' => [
                                            ['content' => '<i class="fa fa-calendar "></i>'],
                                        ],
                                    ] ])->textInput([
                                        'class' => 'datetime-trx',
                                        'autocomplete' => "off",
                                        'readonly' => true]);
                                ?>
                                <?php
                                    echo $form->field($modelPemesananKamar, 'tgl_rawatinap', [
                                    'addon' => [
                                        'append' => [
                                            ['content' => '<i class="fa fa-calendar "></i>'],
                                        ],
                                    ] ])->textInput([
                                        'class' => 'datetime',
                                        'autocomplete' => "off",
                                        'readonly' => true]);
                                ?>
                                <?php
                                echo $form->field($modelPemesananKamar, 'jeniskasuspenyakit_id')->dropDownList(
                                        $ddlJenisKasusPenyakit,
                                        [
                                            'prompt'=> \Yii::t('fe','Pilih'),
                                            'class' => 'select2 ddlJenisKasusPenyakit',
                                            'id' => 'jeniskasuspenyakit_id'
                                        ]
                                    );
                                ?>
                                <?php
                                echo $form->field($modelPemesananKamar, 'kelaspelayanan_id')->dropDownList(
                                       $ddlKelasPelayanan,
                                        [
                                            'prompt'=> \Yii::t('fe','Pilih'),
                                            'class' => 'form-control select2 ddl_kelaspelayanan',
                                            'id' => 'select2_list_kelaspelayanan'
                                        ]
                                    );
                                ?>
                                <?php
                                    echo $form->field($modelPemesananKamar, 'ruangan_id',['addon' => ['append' => [
                                        'content'=>Html::button(Yii::t('fe','Kamar'), [
                                            'id'=>'cari_kamar',
                                            'class' => 'btn btn-default',
                                            'data-width'=>"90%",
                                            'data-target'=>'#modal_backdrop',
                                            'href'=>Url::to(['end-point/pilih-tempat-tidur'])
                                        ]),
                                        'asButton'=>true
                                        ]] ])->widget(DepDrop::classname(), [
                                            'options'=>['class' => 'select2 autoListRuangan', 'id'=>'ruangan_id'],
                                            'pluginOptions'=>[
                                                'initialize'=>true,
                                                'depends'=>['jeniskasuspenyakit_id'],
                                                'placeholder'=>'-- PILIH --',
                                                'url'=>Url::to(['/pendaftaran/pemesanan-kamar/get-list-ruangan']),
                                                'params'=>['bookingkamar-id']
                                            ]
                                    ]);
                                ?>
                                <?php

                                    echo $form->field($modelPemesananKamar, 'nokamar')->textInput(['id'=>'nokamar','readonly'=>'true','disabled'=>true]);
                                ?>


                                <div class="form-group" style="margin-bottom: 0 !important;">
                                    <label class="control-label col-sm-4"></label>
                                    <div class="col-sm-8">
                                    <?php
                                        echo $form->field($modelPemesananKamar, 'kamartempattidur_id', [
                                            'inputOptions'=>[
                                                'id'=>'kamartempattidur_id'
                                            ]
                                        ])->hiddenInput()->label(false);
                                        echo $form->field($modelPemesananKamar, 'kamarruangan_id', [
                                            'inputOptions'=>[
                                                'id'=>'kamarruangan_id'
                                            ]
                                        ])->hiddenInput()->label(false);
                                        echo $form->field($modelPemesananKamar, 'kamarruangan_jenis', [
                                            'inputOptions'=>[
                                                'id' => 'kamarruangan_jenis'
                                            ]
                                        ])->hiddenInput()->label(false);
                                        echo $form->field($modelPemesananKamar, 'jenis_kelamin_booking', [
                                            'inputOptions'=>[
                                                'id' => 'jenis_kelamin_booking'
                                            ]
                                        ])->hiddenInput()->label(false);
                                    ?>
                                    </div>
                                </div>
                                <?php

                                    echo $form->field($modelPemesananKamar, 'nama_pemesan')->textInput(['placeholder'=>'Nama Pemesan']);
                                ?>
                                <?php
                                    echo $form->field($modelPemesananKamar, 'keterangan_booking')->textArea([
                                            'id' => 'keterangan_booking',
                                            'placeholder' => \Yii::t('fe','Isi Keterangan'),
                                            'rows' => 4,
                                            'style' => 'height: auto !important;'
                                        ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>

<?php

    $this->registerJs($this->render('pesan_kamar.js'));
?>
