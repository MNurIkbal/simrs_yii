<?php 
use yii\web\View;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
?>

<div class="col-md-12">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h6 class="panel-title"><b>Tindakan</b></h6>
        </div>
        <div class="panel-body">
            <?php
                $form = ActiveForm::begin([
                    'id' => 'tindakan-form',
                    'action' => $endPoint . "/order-obat-alkes/simpan?id={$id}&pelayananId={$pelayananId}",
                    'enableAjaxValidation'=>false,
                    'enableClientValidation'=>false,
                    'type' => ActiveForm::TYPE_VERTICAL,
                    'formConfig' => [
                        'labelSpan' => 3,
                        'deviceSize' => ActiveForm::SIZE_SMALL
                    ],
                    'options' => [
                        'skip-confirm' => "true"
                    ]
                ]);
            ?>
            <?= Html::hiddenInput('TindakanForm[harga_tariftindakan]', '',['id' => 'harga_tariftindakan']); ?>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'tanggal_tindakan')->staticInput(); ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'tindakan_id', [
                        'horizontalCssClasses' => [
                        'label' => 'text-left control-label col-sm-2',
                        'wrapper' => 'col-md-4'
                        ]
                    ])->dropDownList([],[
                        'class' => 'select2',
                        'id' => 'tindakan_id',
                        'data-kelas' => isset($info_pasien['kelaspelayanan_id']) ? $info_pasien['kelaspelayanan_id'] : null,
                        'data-penjamin' => isset($info_pasien['penjamin_id']) ? $info_pasien['penjamin_id'] : null,
                        'prompt' => Yii::t('fe','--Pilih Tindakan--')
                        ]);
                    ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'qty', [
                        'horizontalCssClasses' => [
                            'label' => 'text-left control-label col-sm-2',
                            'wrapper' => 'col-md-2'
                        ]
                        ])->textInput([
                        'placeholder' => $model->getAttributeLabel('Jumlah'),
                        'class' => 'form-control input-sm text-right doco-number',
                        'autocomplete' => "off",
                    ]); ?>
                    </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'jumlah_tarif', [
                        'addon' => ['prepend' => ['content' => 'Rp.']],
                        'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2',
                                'wrapper' => 'col-md-2'
                            ]
                        ])->textInput([
                            'class' => 'form-control input-sm text-right',
                            'autocomplete' => "off",
                            'readonly' => true
                    ]); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'petugas_satu',[
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2',
                                'wrapper' => 'col-md-3'
                            ]
                        ])->dropDownList(ArrayHelper::map($list_pegawai, 'pegawai_id', 'nama_pegawai'),[
                            'class' => 'select2',
                            'prompt' => Yii::t('fe','--Pilih--')
                        ]);
                    ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'petugas_dua',[
                            'horizontalCssClasses' => [
                                'label' => 'text-left control-label col-sm-2',
                                'wrapper' => 'col-md-3'
                            ]
                        ])->dropDownList(ArrayHelper::map($list_pegawai, 'pegawai_id', 'nama_pegawai'),[
                            'class' => 'select2',
                            'prompt' => Yii::t('fe','--Pilih--')
                        ]);
                    ?>
                </div>
                <div class="col-md-3" style="margin-top: 20px;">
                    <?= Html::button('<b><i class="fa fa-save"></i></b>' . \Yii::t('fe', 'Simpan'), [
                        'class' => 'btn btn-labeled btn-xs btn-info',
                        'id' => 'btn-add-tindakan'
                    ]) ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?><br>
            <table id="tmp-tindakan" class="table table-condensed" style="width:100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1" class="text-center">No</th>
                        <th>Tanggal Tindakan</th>
                        <th>Nama Tindakan</th>
                        <th>Petugas 1</th>
                        <th>Petugas 2</th>
                        <th>Jumlah</th>
                        <th>Jumlah Tarif</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <tr class="empty-row">
                        <td colspan="8" class="text-center">Data belum tersedia</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div><br>

<?php
$this->registerJs($this->render('js/tindakan.js'), VIEW::POS_END);
?>