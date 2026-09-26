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
        'readonly' => true
    ])->label(Yii::t('fe', 'Tanggal Admisi'));
?>
<?php endif; ?>



<?= $form->field($model, 'jeniskasuspenyakit_id')->dropDownList($penyakitList, [
    'id'=>'jeniskasuspenyakit_id', 
    'class' => 'form-control select2',
    'prompt'=>'-- Pilih --',
    'disabled' => true
    // 'disabled' => $disabled
]); ?>

<?=Html::activeHiddenInput($model, 'jeniskasuspenyakit_id')?>

<?= $form->field($model, 'kelaspelayanan_id', [
        // 'addon' => [
        //     'append' => [
        //         'content'=>Html::button(Yii::t('fe','Kamar'), [
        //             'id'=>'btnCariKamar',
        //             'class' => 'btn btn-default btn-xs',
        //             'disabled' => $disabled
        //         ]),
        //         'asButton'=>true
        //     ]
        // ]
    ])->dropDownList($kelasList, [
        'class' => 'selectKp select2',
        'id'=>'kelaspelayanan_id',
        'prompt' => Yii::t('fe', '--Pilih--'),
        'disabled' => true
    ]);
?>

<?=Html::activeHiddenInput($model, 'kelaspelayanan_id')?>

<?= $form->field($model, 'kamarruangan_nokamar', [
    'addon' => [
        'append' => [
            'content' => '<span id="ruanganLabelValue" style="color: green;font-weight: bold;"></span>'
        ]
    ],
    'inputOptions'=>['id'=>'nokamar', 'readonly' => true],
    
])->label(Yii::t('fe', 'No Tempat Tidur')); ?>
<div id="error_EditPendaftaranFormkamartempattidur_id"></div>

<?= $form->field($model, 'pegawai_id')
->dropDownList($dokterList, [
        'id' => 'pegawai_id',
        'class' => 'form-control select2',
        'prompt' => Yii::t('fe', '--Pilih--')
    ]);
?>
<?php echo $this->render(
    '_modal_kamar', [
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

$this->registerJs($this->render('js/datatable-kamar.js'));
?>