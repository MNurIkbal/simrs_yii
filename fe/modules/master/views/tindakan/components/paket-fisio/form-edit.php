<?php
use yii\web\View;
use kartik\widgets\ActiveForm;
use app\components\DocoHelpers;
?>
<style>
    .divider-vertical {
        height: 100px;
        border-left: 1px solid gray;
        float: left;
        opacity: 0.5;
        margin: 0 15px;
    }
</style>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'simpan' => [
                        'title' => 'Simpan',
                        'icon' => 'fa fa-save',
                        'attributes' => [
                            'class' => 'spa',
                            'action' => '/master/tindakan/paket-fisio-update',
                            'data-options' => 'click',
                            'form-id' => 'paket-fisio-form-edit',
                            'data-render' => 'paket-fisio-index',
                            'data-tab' => 'tab-paket-fisio',
                            'data-target' => '#view-paket-fisio',
                            'id' => 'btn-save'
                        ]
                    ],
                    'kembali' => [
                        'title' => 'Kembali',
                        'icon' => 'fa fa-arrow-left',
                        'attributes' => [
                            'class' => 'spa',
                            'data-options' => 'click',
                            'data-render' => 'paket-fisio-index',
                            'data-tab' => 'tab-paket-fisio',
                            'data-target' => '#view-paket-fisio',
                        ]
                    ],
                ], '#table-tindakan-ruangan') ?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12">
                        <h3><strong><?= $title ?></strong></h3>
                    </div>
                </div>
                <?php $form = ActiveForm::begin([
                    'id' => 'paket-fisio-form-edit',
                    'enableAjaxValidation' => false,
                    'enableClientValidation' => false,
                    'type' => ActiveForm::TYPE_HORIZONTAL,
                    'method' => 'PUT',
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'role' => 'form',
                        'enctype' => 'multipart/form-data'
                    ]
                ]) ?>
                <?= $form->field($model, 'parent_id')->hiddenInput()->label(false) ?>
                <?= $form->field($model, 'list_tindakan')->hiddenInput(['id' => 'list_tindakan'])->label(false) ?>
                <?= $form->field($model, 'kode_paket', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm kode_unique', 'id' => 'input-kode-paket-fis', 'readonly' => true]) ?>
                <?= $form->field($model, 'nama_paket', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm', 'id' => 'input-nama-paket-fis', 'readonly' => true]) ?>
                <?= $form->field($model, 'namalainya_paket', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm','placeholder' => 'Jika dikosongkan mengambil Nama Paket']) ?>
                <?= $form->field($model, 'frekuensi', ['labelOptions' => ['class' => 'text-left'], 'addon' => ['append' => ['content' => '<span class="text-bold text-left">Kali</span>']]])->textInput(['class' => 'form-control input-sm', 'id' => 'jumlah-frekuensi', 'type' => 'number', 'min' => 1, 'max' => 10]) ?>
                <?= $form->field($model, 'jumlah', ['labelOptions' => ['class' => 'text-left']])->textInput(['class' => 'form-control input-sm', 'id' => 'jumlah-pilihan', 'type' => 'number', 'min' => 1]) ?>
                <?= $form->field($model, 'is_active', ['labelOptions' => ['class' => 'text-left']])->checkbox(['class' => 'pull-left', 'label' => 'Aktif'])->label("Status") ?>
                <?= $form->field($model, 'catatan', ['labelOptions' => ['class' => 'text-left']])->textArea(['class' => 'form-control input-sm']) ?>
                <hr />
                <div class="form-group" style="margin-bottom:30px;">
                    <label class="text-left col-sm-3 has-star">Tambah Tindakan</label>
                    <div class="col-sm-4 auto-tindakan">
                        <select id="auto-tindakan"></select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12 tabel-tindakan" style="margin-top:30px;">
                        <table id="tabel-tampung-tindakan" class="table table-striped table-condensed table-hover" style="width:100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="1%">No</th>
                                    <th>Tindakan</th>
                                    <th>Kelompok</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php
$phpVars = [
    'listDetailTindakanPaket' => $listDetailTindakanPaket
];
$this->registerJsVar('phpVars', $phpVars);
$this->registerJs($this->render('js/form-edit.js'), View::POS_END);
?>