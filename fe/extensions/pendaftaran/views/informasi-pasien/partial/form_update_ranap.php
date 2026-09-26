<?php 
use yii\helpers\Html;
use kartik\widgets\DepDrop;
use yii\helpers\Url;
use yii\web\View;
?>

<?= Html::activeHiddenInput($model, 'no_rekam_medik')?>
<?= Html::activeHiddenInput($model, 'pasienadmisi_id')?>
<?= Html::activeHiddenInput($model, 'nama_pasien')?>
<?= Html::activeHiddenInput($model, 'no_pendaftaran')?>
<?= Html::activeHiddenInput($model, 'ruangan_id', ['id'=> 'ruanganIdHidden'])?>
<?= Html::activeHiddenInput($model, 'kamarruangan_id', ['id' => 'kamarruangan_id'])?>
<?= Html::hiddenInput('pasientitipan', 0, ['id' => 'pasienTitipanValue']); ?>
<?= Html::hiddenInput('pasienaps', 0, ['id' => 'pasienApsValue']); ?>
<?= Html::hiddenInput('kelaspelayanan_selected', "", ['id' => 'kelaspelayanan_selected']); ?>
<?= Html::hiddenInput('jeniskelamin_id', $jeniskelamin_id, ['id' => 'jeniskelamin_id']); ?>
<?= Html::activeHiddenInput($model, 'kamartempattidur_id', ['id' => 'kamartempattidur_id'])?>

<?php if($status_periksa == 441) : ?>
<?= $form->field($model, 'tgl_admisi')->staticInput()
->label(Yii::t('fe', 'Tanggal Admisi'));
?>
<?php else : ?>
<?php $model->tgl_admisi = date('d-m-Y H:i', strtotime($model->tgl_admisi)) ?>
<?= $form->field($model, 'tgl_admisi', [
        'addon' => [
            'append' => [
                'content' => '<i class="fa fa-calendar"></i>'
            ]
        ]
    ])->textInput([
        'placeholder' => 'Tanggal Admisi',
        'class' => 'form-control input-sm',
        'autocomplete' => "off",
    ])->label(Yii::t('fe', 'Tanggal Admisi'));
?>
<?php endif; ?>


<div style="display:none">
<?= $form->field($model, 'jeniskasuspenyakit_id')->dropDownList($penyakitList, [
    'id'=>'jeniskasuspenyakit_id', 
    'class' => 'form-control select2',
    'prompt'=>'-- Pilih --',
    'disabled' => $disabled
]); ?>
</div>

<?= $form->field($model, 'hakkelas_id')
->dropDownList($kelasList, [
        'id' => 'hakkelas_id',
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ]);
?>

<?= $form->field($model, 'kelaspermintaan_id')
->dropDownList($kelasList, [
        'id' => 'kelaspermintaan_id',
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ]);
?>

<?= $form->field($model, 'kelaspelayanan_id', [
        'addon' => [
            'append' => [
                'content'=>Html::button(Yii::t('fe','Kamar'), [
                    'id'=>'btnCariKamar',
                    'class' => 'btn btn-default btn-xs',
                    'disabled' => $disabled
                ]),
                'asButton'=>true
            ]
        ]
    ])->dropDownList($kelasList, [
        'class' => 'selectKp select2',
        'id'=>'kelaspelayanan_id',
        'prompt' => Yii::t('fe', '--Pilih--'),
        'disabled' => $disabled
    ])->label('Kelas Perawatan');
?>

<?= $form->field($model, 'kamarruangan_nokamar', [
    'addon' => [
        'append' => [
            'content' => '<span id="ruanganLabelValue" style="color: green;font-weight: bold;"></span>'
        ]
    ],
    'inputOptions'=>['id'=>'nokamar', 'readonly' => true],
    
])->label(Yii::t('fe', 'No Tempat Tidur')); ?>
<div id="error_EditPendaftaranFormkamartempattidur_id"></div>

<?= $form->field($model, 'dokterpengirim_id')
->dropDownList($allDokterList, [
        'id' => 'dokterpengirim_id',
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ]);
?>

<?= $form->field($model, 'pegawai_id')
->dropDownList($allDokterList, [
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ])->label('Dokter DPJP');
?>

<?php if (!empty($model->dokterkonsul_id)): ?>
    <?php foreach ($model->dokterkonsul_id as $key => $value): ?>
        <div class="row dokter-konsul">
            <div class="col-md-5">
                <?= 
                $form->field($model, 'dokterkonsul_id[]')
                ->dropDownList($allDokterList, [
                        'class' => 'form-control select2',
                        'prompt' => '--Pilih--',
                        'value' => $value,
                        'placeholder' => Yii::t('fe','Dokter Konsul')
                    ]);
                ?>
            </div>
            <?php if ($key == 0): ?>
                <div class="col-sm-1">
                    <div class="form-group highlight-addon has-size-sm">
                        <label class="control-label"><b style="color:white;float:right;">Aksi</b></label>
                        <?= Html::button('+', [
                            'class' => 'btn btn-success tambah-dokter-konsul'
                        ]) ?>
                    </div>
                    <div class="help-block"></div>
                </div>
            <?php else : ?>
                <div class="col-sm-1">
                    <div class="form-group highlight-addon has-size-sm">
                        <label class="control-label"><b style="color:white;float:right;">Aksi</b></label>
                        <?= Html::button('x', [
                            'class' => 'btn btn-danger hapus-dokter-konsul'
                        ]) ?>
                    </div>
                    <div class="help-block"></div>
                </div>
            <?php endif ?>
        </div>
    <?php endforeach ?>
<?php else : ?>
    <div class="row dokter-konsul">
        <div class="col-md-5">
            <?= 
            $form->field($model, 'dokterkonsul_id[]')
            ->dropDownList($allDokterList, [
                    'class' => 'form-control select2',
                    'prompt' => '--Pilih--',
                    'placeholder' => Yii::t('fe','Dokter Konsul')
                ]);
            ?>
        </div>
        <div class="col-sm-1">
            <div class="form-group highlight-addon has-size-sm">
                <label class="control-label"><b style="color:white;float:right;">Aksi</b></label>
                <?= Html::button('+', [
                    'class' => 'btn btn-success tambah-dokter-konsul'
                ]) ?>
            </div>
            <div class="help-block"></div>
        </div>
    </div>
<?php endif ?>

<?= $form->field($model, 'prosedurmasuk_id')
->dropDownList($prosedurMasukList, [
        'id' => 'prosedurmasuk_id',
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ]);
?>

<?= $form->field($model, 'diagnosa_awal')->textArea([], ['rows' => '2']); ?>

<?php echo $this->render(
    '@app/modules/pendaftaran/views/informasi-pasien/_modal_kamar', [
        'masterWarnaTempatTidur' => $masterWarnaTempatTidur
    ]
); ?>

<?php 
$this->registerJs("
const columns = [
        { title: 'No', data: 'rowNum', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Jenis Kasus Penyakit')."', data: 'jeniskasuspenyakit_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Ruangan')."', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Kamar')."', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'No Tempat Tidur')."', data: 'datakamar', searchable: false, orderable: false },
    ]
    const columnKamarTitipan = [
        { title: 'No', data: 'rowNum', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Jenis Kasus Penyakit')."', data: 'jeniskasuspenyakit_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Ruangan')."', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Kamar')."', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Kelas')."', data: 'kelaspelayanan_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'No Tempat Tidur')."', data: 'datakamar', searchable: false, orderable: false },
    ]
    const columnKamarAps = [
        { title: 'No', data: 'rowNum', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Jenis Kasus Penyakit')."', data: 'jeniskasuspenyakit_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Ruangan')."', data: 'ruangan_nama', searchable: false, orderable: false},
        {title: '".Yii::t('fe', 'Kamar')."', data: 'kamarruangan_nokamar', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'Kelas')."', data: 'kelaspelayanan_nama', searchable: false, orderable: false },
        {title: '".Yii::t('fe', 'No Tempat Tidur')."', data: 'datakamar', searchable: false, orderable: false },
    ]
"); 
$this->registerJs('
const dataPenyakit = ' . json_encode($penyakitList) .  '
const dataKasus = ' . json_encode($kelasList) .  '
const instalasiId = ' . $instalasi_id .  '', View::POS_END);

$this->registerJs($this->render('@app/extensions/pendaftaran/views/informasi-pasien/js/datatable-kamar.js'));
?>