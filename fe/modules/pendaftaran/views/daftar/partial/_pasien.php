<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use app\components\DocoHelpers;
use kartik\widgets\FileInput;
use app\components\DocoConstants;
?>

<style>
    .dt-pasien {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
        text-align: left !important;
    }
    .dd-pasien {
        margin-top: 2px !important;
        margin-bottom: 4px !important;
        padding-top: 2px;
    }
    .input-group-btn.dropdown-list {
        min-width:75px !important;
        text-align:left !important;
    }
    .input-group {
        width: 100%;
    }
</style>

<?php
$jenis = ['1'=>'APS', '0'=>'Rujukan'];
$form = ActiveForm::begin([
    'id' => 'form-daftar-rajal',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'enableClientValidation'=>false,
    'enableAjaxValidation'=>false,
    'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);

echo Html::activeHiddenInput($modelPasien, 'no_rekam_medik', ['id'=>'hidden-no_rekam_medik']);
echo Html::activeHiddenInput($modelPasien, 'nama_pasien', ['id'=>'hidden-nama_pasien']);

?>
<div class="row select-no-rm">
    <div class="col-md-6">
        <?php
            if($isPenunjang){
                $modelPasien->is_aps = 0;
                ?>
                <div class="form-group">
                    <label class="control-label col-sm-4"><?=Yii::t('fe', 'Jenis pendaftaran')?></label>
                    <div class="col-sm-8">
                        <?= Html::activeRadioList($modelPasien, 'is_aps', $jenis , ['inline'=>true,'type'=>'checkbox'] ) ?>
                    </div>
                </div>
                <?php
            }
            ?>
        <?php
        $isRanap = $param == 'ranap' ? 1 : 0;
        echo $form->field($modelPasien, 'no_rekam_medik', [
            'addon' => [
                'prepend' => [
                    'content'=> Html::checkbox('chk-statuspasien', true) . ' ' . Yii::t('fe', 'Pasien lamaaaaa'),
                    'options'=>[
                        'class' => $param == 'ranap' ? 'hidden' : '',
                    ]
                ]
            ]
        ])->widget(Select2::classname(), [
            'options' => [
                'id' => 'no_rekam_medik',
                'placeholder' => Yii::t('fe','No rm').' / '.Yii::t('fe', 'Nama pasien'). ' / ' .Yii::t('fe', 'Tanggal lahir')
            ],
            'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'language' => [
                    'errorLoading' => new JsExpression("function () { return 'Loading...'; }"),
                ],
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['/pendaftaran/end-point/norm']),
                    'dataType' => 'json',
                    'delay' => 500,
                    'data' => new JsExpression('
                        function(params) {
                            return {
                                q:params.term,
                                isRanap:' . $isRanap . ',
                                isAps: $("input[name=\"PasienForm[is_aps]\"]:checked").val()
                            };
                        }
                    ')
                ],
                'escapeMarkup' => new JsExpression('function (markup) { return markup; }'),
                'templateResult' => new JsExpression(
                    'function(no_rekam_medik) {
                        return no_rekam_medik.text;
                    }'
                ),
                'templateSelection' => new JsExpression(
                    'function (no_rekam_medik) {
                        $(".pendaftaran-id").val(no_rekam_medik.pendaftaran_id);
                        return no_rekam_medik.text;
                    }'
                ),
            ],
            'pluginEvents' => [
                'select2:select' => 'function(res) {
                    var data_id = $(this).val();
                    var response = res.params.data;
                    var no_rekam_medik = response.no_rekam_medik;
                    var nama_pasien = response.nama_pasien;

                    if (data_id) {
                        getInfoPasien(data_id);
                        $("#hidden-no_rekam_medik").val(no_rekam_medik);
                        $("#hidden-nama_pasien").val(nama_pasien);
                    }
                }'
            ],
        ]);
        ?>
    </div>
    <?php if (!$hidePencarianLanjutan): ?>
    <div class="col-md-6">
        <?= Html::a(Yii::t('fe', 'Pencarian Lanjutan'), [
            '/pendaftaran/daftar/pencarian-lanjutan'
        ], [
            'class' => 'btn btn-default btn-sm',
            'id' => 'btn-pencarian-lanjutan',
            'data-target' => '#modal_pencarian_lanjutan',
            'data-options' => 'link',
            'data-toggle' => 'modal',
        ]) ?>
    </div>
    <?php endif ?>
</div>

<div class='panel panel-flat inf-pasien' style='display: none;'>
    <div class="panel-heading">
        <h6 class="panel-title">
            <i class="fa fa-user-circle"></i><span class='inf-pasien-title-nama'><strong>Tn. Nama Pasien</strong></span>
            <small class='inf-pasien-title-norm'>No. Rekam Medis : 0000001</small>
            <?= Html::hiddenInput('antrian_id', '', ['class'=>'antrian-id']); ?>
            <?= Html::hiddenInput('pasien_id', '', ['class'=>'pasien-id']); ?>
            <?= Html::hiddenInput('norm', '', ['id'=>'norm']); ?>
            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
        </h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse" class="collapse-pasien"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar inf-pasien-toolbar clearfix">
        <div class="row">
            <div class="col-md-6">
                <?php
                // echo DocoHelpers::generateToolbar([
                //     'update_pasien' => [
                //         'type' => 'button',
                //         'title' => \Yii::t('fe', 'Ubah'),
                //         'icon' => 'fa fa-pencil',
                //         'attributes' => [
                //             'class' => 'btn-pasien-ubah',
                //             'data-options'=>'click',
                //             'id'=>'tombol-ubah-pasien'
                //         ]
                //     ],
                //     // 'detail',
                // ]);
                ?>
            </div>
            <div class="col-md-6 text-right">
                <?=DocoHelpers::generateToolbar([
                    'Batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batal'),
                        'icon' => 'fa fa-close',
                        'attributes' => [
                            'class' => 'btn-pasien-inf-batal',
                            'data-options'=>'click',
                        ]
                    ],
                ])?>
            </div>
        </div>
    </div>

    <div class="panel-body inf-pasien-body">
        <div class='col-md-4 col-sm-12'>
            <dl class="dl-horizontal">
                <dt class="dt-pasien"><?= Yii::t('fe', 'Jenis identitas'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-jenisidentitas'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'No identitas'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-noidentitas'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Nama depan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-namadepan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Nama pasien'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-namapasien'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Nama panggilan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-namapanggilan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Tempat lahir'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-tempatlahir'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Tanggal lahir'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-tanggallahir'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Umur'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-umur'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Jenis kelamin'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-jeniskelamin'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Golongan darah'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-golongandarah'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Status perkawinan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-statusperkawinan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Nama ibu'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-namaibu'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Nama ayah'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-namaayah'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Anak ke'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-anakke'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Jumlah bersaudara'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-jumlahbersaudara'>lorem ipsum</dd>
            </dl>
        </div>
        <div class='col-md-4 col-sm-12'>
            <dl class="dl-horizontal">
                <dt class="dt-pasien"><?= Yii::t('fe', 'Alamat pasien'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-alamatpasien'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'RT / RW'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-rtrw'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Kelurahan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-kelurahan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Kecamatan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-kecamatan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Kota'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-kota'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Propinsi'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-propinsi'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'No telepon'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-notelepon'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'No mobile'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-nomobile'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Alamat email'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-alamatemail'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Warga negara'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-warganegara'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Pendidikan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-pendidikan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Pekerjaan'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-pekerjaan'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Suku'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-suku'>lorem ipsum</dd>
                <dt class="dt-pasien"><?= Yii::t('fe', 'Agama'); ?></dt>
                <dd class="dd-pasien" id='inf-pasien-agama'>lorem ipsum</dd>
            </dl>
        </div>
        <!-- photo here -->
        <div class='col-md-4 col-sm-12'>
            <!-- image -->
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="thumbnail no-padding">
                        <div class="thumb" id="pasang_image">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class='panel panel-flat form-data-pasien' style='display: none;'>
    <div class="panel-heading">
        <h6 class="panel-title"><span class='inf-pasien-title-nama'><strong>Tn. Nama Pasien</strong></span>
            <small class='inf-pasien-title-norm'>No. Rekam Medis : 0000001</small>
            <a class="heading-elements-toggle"><i class="icon-more"></i></a>
        </h6>
        <div class="heading-elements">
            <ul class="icons-list">
                <li><a data-action="collapse"></a></li>
            </ul>
        </div>
    </div>
    <div class="panel-toolbar clearfix">
        <div class="row">
            <div class="col-md-1">
                <?=DocoHelpers::generateToolbar([
                    'update' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Simpan'),
                        'icon' => 'fa fa-pencil',
                        'attributes' => [
                            'class' => 'btn-pasien-simpan-ubah',
                            'data-options'=>'click',
                        ]
                    ],
                    // 'insert' => [
                    //     'type' => 'button',
                    //     'title' => \Yii::t('fe', 'Simpan'),
                    //     'icon' => 'fa fa-plus',
                    //     'attributes' => [
                    //         'class' => 'btn-pasien-simpan-tambah',
                    //         'data-options'=>'click',
                    //         'style'=>'display:none;'
                    //     ],
                    // ],
                    'reset2' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Muat ulang'),
                        'icon' => 'fa fa-refresh',
                        'attributes' => [
                            'class' => 'btn-pasien-reset',
                            'data-options'=>'click',
                            'style'=>'display:none;'
                        ],
                    ],
                ]);?>
            </div>
            <div class="col-md-5">

            </div>
            <div class="col-md-6 text-right">
                <?=DocoHelpers::generateToolbar([
                    'batal' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Batal'),
                        'icon' => 'fa fa-close',
                        'attributes' => [
                            'class' => 'btn-pasien-batal',
                            'data-options'=>'click',
                        ]
                    ],
                ])?>
            </div>
        </div>
        <?php //echo Html::resetINput('reset', ['class'=>'btn']);?>
    </div>
    <div class="panel-body">
        <div class="col-md-5">
            <?= $form->field($modelPasien, 'jenisidentitas')
                ->dropDownList(
                    ArrayHelper::map($listResponses['jenis_identitas'], 'lookup_id', 'lookup_value'),
                    ['id'=>'frm-pasien-jenisidentitas', 'class'=>'select2 select2pasien', 'prompt'=>'— PILIH —'])
                ->label(Yii::t('fe', 'Jenis Identitas'));
            ?>

            <?= $form->field($modelPasien, 'no_identitas_pasien', [
                'inputOptions'=>['id'=>'frm-pasien-no_identitas_pasien'],
            ]); ?>

            <?= $form->field($modelPasien, 'nama_pasien', [
                'template' => '
                    {label}
                    <div class="col-sm-8">
                        <div class="input-group">
                            <span class="input-group-btn dropdown-list">
                                '.Html::dropdownList('PasienForm[namadepan]', '', ArrayHelper::map($listResponses['nama_depan'], 'lookup_id', 'lookup_value'), ['id'=>'frm-pasien-namadepan', 'class'=>'select2 select2pasien']).'
                            </span>
                            {input}
                        </div>
                        {error}{hint}
                    </div>'
            ])->textInput(['id' => 'frm-pasien-nama_pasien']) ?>

            <?= $form->field($modelPasien, 'nama_bin', [
                'inputOptions' => ['id' => 'frm-pasien-nama_bin']
                ]) ?>
            <?= $form->field($modelPasien, 'tempat_lahir', [
                'inputOptions' => ['id' => 'frm-pasien-tempat_lahir']
                ]) ?>
            <?= $form->field($modelPasien, 'tanggal_lahir', [
                'addon' => [
                    'append' => [
                        ['content' => '<i id="btn_addon_tgllahir" class="fa fa-calendar "></i>'],
                    ],
                ] ])->textInput(['class' => '', 'id'=>'frm-pasien-tanggal_lahir','data-mask'=>'99-99-9999']) ?>
            <?= $form->field($modelPasien, 'umur', [
                'inputOptions'=>['id' => 'frm-pasien-umur', 'readonly'=>true]]); ?>
            <?= $form->field($modelPasien, 'jeniskelamin')
                ->radioList(
                    ArrayHelper::map($listResponses['jenis_kelamin'], 'lookup_id', 'lookup_value'),
                    ['inline'=>true, 'id'=>'frm-pasien-jeniskelamin']
                )
                ->label(Yii::t('fe', 'Jenis Kelamin'));
            ?>
            <?= $form->field($modelPasien, 'golongandarah')
                ->radioList(
                    ArrayHelper::map($listResponses['golongan_darah'], 'lookup_id', 'lookup_value'),
                    [ 'id'=>'frm-pasien-golongandarah']
                )
                ->label(Yii::t('fe', 'Golongan Darah'));
            ?>
            <?= $form->field($modelPasien, 'statusperkawinan')
                ->dropDownList(
                    ArrayHelper::map($listResponses['status_perkawinan'], 'lookup_id', 'lookup_value'),
                    ['id'=>'frm-pasien-statusperkawinan', 'class'=>'select2  select2pasien', 'prompt'=>'— PILIH —']
                )
                ->label(Yii::t('fe', 'Status Perkawinan'))
            ?>

            <?= $form->field($modelPasien, 'nama_ibu', [
                'inputOptions' => ['id' => 'frm-pasien-nama_ibu']
                ]) ?>

            <?= $form->field($modelPasien, 'nama_ayah', [
                'inputOptions' => ['id' => 'frm-pasien-nama_ayah']
                ]) ?>
            <?= $form->field($modelPasien, 'anakke', [
                'inputOptions' => ['id' => 'frm-pasien-anakke']
                ])
                ->label(Yii::t('fe', 'Anak Ke')) ?>
            <?= $form->field($modelPasien, 'jumlah_bersaudara', [
                'inputOptions' => ['id' => 'frm-pasien-jumlah_bersaudara']
                ]) ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($modelPasien, 'alamat_pasien')->textArea(['id' => 'frm-pasien-alamat_pasien'] ); ?>

            <?= $form->field($modelPasien, 'rt', [
                'inputOptions' => ['id' => 'frm-pasien-rt']
                ]); ?>
            <?= $form->field($modelPasien, 'rw', [
                'inputOptions' => ['id' => 'frm-pasien-rw']
                ]); ?>

            <?= $form->field($modelPasien, 'propinsi_id')
                ->dropDownList(
                    ArrayHelper::map($listMasters['propinsi'], 'propinsi_id', 'propinsi_nama'),
                    [
                        'id'=>'frm-pasien-propinsi_id',
                        'class'=>'select2 select2pasien',
                        'prompt'=>'— PILIH —',
                        'options'=>$optionsProv
                    ]
                )
                ->label(Yii::t('fe', 'Propinsi'));
            ?>
            <?= $form->field($modelPasien, 'kabupaten_id')->widget(DepDrop::classname(), [
                'options'=>['id'=>'frm-pasien-kabupaten_id','class'=>'select2 select2pasien',],
                'pluginOptions'=>[
                    'depends'=>['frm-pasien-propinsi_id', 'frm-pasien-jenisidentitas', 'frm-pasien-no_identitas_pasien'],
                    'placeholder'=>'-- PILIH --',
                    'url'=>Url::to(['/pendaftaran/end-point/list-kabupaten'])
                ],
                'pluginEvents'=>[
                    "depdrop:afterChange"=>"function(event, id, value) {
                        if ($('#frm-pasien-no_telepon_pasien').is(':focus')) {
                            $('#frm-pasien-kabupaten_id').focus();
                        }
                    }",
                ]
            ])->label(Yii::t('fe', 'Kabupaten')); ?>
            <?= $form->field($modelPasien, 'kecamatan_id')->widget(DepDrop::classname(), [
                'options'=>['id'=>'frm-pasien-kecamatan_id', 'class'=>'select2 select2pasien',],
                'pluginOptions'=>[
                    'depends'=>['frm-pasien-kabupaten_id', 'frm-pasien-jenisidentitas', 'frm-pasien-no_identitas_pasien'],
                    'placeholder'=>'-- PILIH --',
                    'url'=>Url::to(['/pendaftaran/end-point/list-kecamatan'])
                ],
                'pluginEvents'=>[
                    "depdrop:afterChange"=>"function(event, id, value) {
                        if ($('#frm-pasien-kabupaten_id').is(':focus') || $('#frm-pasien-no_telepon_pasien').is(':focus')) {
                            $('#frm-pasien-kecamatan_id').focus();
                        }
                    }",
                ]
            ])->label(Yii::t('fe', 'Kecamatan')); ?>
            <?= $form->field($modelPasien, 'kelurahan_id')->widget(DepDrop::classname(), [
                'options'=>['id'=>'frm-pasien-kelurahan_id', 'class'=>'select2 select2pasien',],
                'pluginOptions'=>[
                    'depends'=>['frm-pasien-kecamatan_id'],
                    'placeholder'=>'-- PILIH --',
                    'url'=>Url::to(['/pendaftaran/end-point/list-kelurahan'])
                ],
                'pluginEvents'=>[
                    "depdrop:afterChange"=>"function(event, id, value) {
                        if ($('#frm-pasien-kecamatan_id').is(':focus') || $('#frm-pasien-no_telepon_pasien').is(':focus')) {
                            $('#frm-pasien-kelurahan_id').focus();
                        }
                    }",
                ]
            ])->label(Yii::t('fe', 'Kelurahan')); ?>
            <?= $form->field($modelPasien, 'no_telepon_pasien', [
                'inputOptions' => ['id' => 'frm-pasien-no_telepon_pasien']
                ]); ?>
            <?= $form->field($modelPasien, 'no_mobile_pasien', [
                'inputOptions' => ['id' => 'frm-pasien-no_mobile_pasien']
                ]); ?>
            <?= $form->field($modelPasien, 'alamatemail', [
                'inputOptions' => ['id' => 'frm-pasien-alamatemail']
                ])->label(Yii::t('fe', 'Alamat Email')); ?>
            <?= $form->field($modelPasien, 'pendidikan_id')
                ->dropDownList(
                    ArrayHelper::map($listMasters['pendidikan'], 'pendidikan_id', 'pendidikan_nama'),
                    ['id'=>'frm-pasien-pendidikan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                )->label(Yii::t('fe', 'Pendidikan'))
            ?>
            <?= $form->field($modelPasien, 'pekerjaan_id')
                ->dropDownList(
                    ArrayHelper::map($listMasters['pekerjaan'], 'pekerjaan_id', 'pekerjaan_nama'),
                    ['id'=>'frm-pasien-pekerjaan_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                )->label(Yii::t('fe', 'Pekerjaan'))
            ?>
            <?= $form->field($modelPasien, 'suku_id')
                ->dropDownList(
                    ArrayHelper::map($listMasters['suku'], 'suku_id', 'suku_nama'),
                    ['id'=>'frm-pasien-suku_id', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                )
                ->label(Yii::t('fe', 'Suku'));
            ?>
            <?php $modelPasien->warga_negara = '308'; ?>
            <?= $form->field($modelPasien, 'warga_negara')
                ->dropDownList(
                    ArrayHelper::map($listResponses['warga_negara'], 'lookup_id', 'lookup_value'),
                    [
                        'id'=>'frm-pasien-warga_negara',
                        'class'=>'select2 select2pasien',
                        'prompt'=>'— PILIH —'
                    ]
                )
            ?>
            <?= $form->field($modelPasien, 'agama')
                ->dropDownList(
                    ArrayHelper::map($listResponses['agama'], 'lookup_id', 'lookup_value'),
                    ['id'=>'frm-pasien-agama', 'class'=>'select2 select2pasien','prompt'=>'— PILIH —']
                )
            ?>
            <?php
            // echo $form->field($modelPasien, 'statusrekammedis', [
            //     'inputOptions' => ['id' => 'frm-pasien-statusrekammedis', 'index'=>'-1']
            //     ])->hiddenInput()->label(false);
                ?>
        </div>

        <!-- image -->
        <div class="col-md-2">
            <div class="row">
                <div class="col-md-6 col-md-offset-3">
                    <div class="thumbnail no-padding">
                        <div class="thumb">
                            <div id="pasang_image2">
                            </div>
                            <?php
                            //echo '<label class="control-label">Upload Document</label>';
                            echo FileInput::widget([
                                'name' => 'photopasien',
                                'pluginOptions' => [
                                    'showCaption' => false,
                                    'showRemove' => false,
                                    'showUpload' => false,
                                    'browseClass' => 'btn btn-primary btn-block',
                                    'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                                    'browseLabel' =>  'Select Photo'
                                ],
                                'options' => ['accept' => 'image/*', 'id'=>'file_input'],
                            ]);

                            // echo $form->field($modelPasien, 'photopasien', ['horizontalCssClasses' => [
                            //     'label' => 'text-left control-label col-sm-12',
                            //     'wrapper' => 'col-md-12',
                            //     ]])->widget(FileInput::classname(), [
                            //         'pluginOptions' => [
                            //             'showCaption' => false,
                            //             'showRemove' => false,
                            //             'showUpload' => false,
                            //             'browseClass' => 'btn btn-primary btn-block',
                            //             'browseIcon' => '<i class="glyphicon glyphicon-camera"></i> ',
                            //             'browseLabel' =>  'Select Photo'
                            //         ],
                            //         'options' => ['accept' => 'image/*', 'id'=>'file_input'],
                            //     ])->label(false);
                            ?>
                            <?php
                            echo $form->field($modelPasien, 'photopasien', [
                                'inputOptions' => ['id' => 'frm-pasien-photopasien']
                            ])->hiddenInput()->label(false);
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>



<?php
$this->registerJs('
    '.$this->render('../js/pasien.js'));
?>
