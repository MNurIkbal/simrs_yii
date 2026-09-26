<?php
    use yii\helpers\Html;
    use yii\web\View;
    use yii\helpers\Url;
    use kartik\form\ActiveForm;
    use kartik\file\FileInput;
    use kartik\widgets\Select2;
    use kartik\widgets\DepDrop;
    use app\components\DocoHelpers;
?>
<style>
div.AnyTime-pkr {
    z-index:9999;
    margin-top: -150px;
    margin-left: -120px;
}
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>

<?php 
$form = ActiveForm::begin([
        'id' => 'inf-pembebasan-tarif-form',
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'options' => ['enctype'=>'multipart/form-data'],
        'formConfig' => ['showErrors' => true,'labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
    ]); 
?>

<div class="panel panel-white">
    <div class="panel-toolbar clearfix">
        <?php 
            echo DocoHelpers::generateToolbar([
                'save' => [
                    'attributes' => [
                        'form_id' => 'inf-pembebasan-tarif-form',
                    ]
                ],
                'custom-reset' => [
                    'type'=>'button',
                    'title' => Yii::t('fe', 'Muat ulang'),
                    'icon' => 'fa fa-refresh',
                    'attributes' => [
                       'class'=>'reset-pembebasantarif',
                       'data-options'=>'click',
                    ],
                ],
            ]);
        ?>
    </div>
</div>

<div class="modal-body">
    <?=Html::activeHiddenInput($model, 'pendaftaran_id'); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">
                <?= $form->field($model, 'tgl_pembebasantarif',['labelSpan' => 3])
                        ->textInput([
                            'class' => 'form-control input-sm date', 
                        ]); 
                ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'total_tagihan', ['labelSpan' => 3])
                        ->textInput([
                            'class'=>'form-control',
                            'readonly'=>'true'
                        ]); 
                ?>
            </div>
        </div>
    </div>
    <?= $form->field($model, 'total_pembebasantarif',['labelSpan' => 3])
            ->textInput([
                'class'=>'form-control docoNumberOnly'
            ]); 
    ?>
    <?= $form->field($model, 'catatan',['labelSpan' => 3])
            ->textArea([
                'class'=>'form-control'
            ]); 
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">
                <span><?=Yii::t('fe', 'Mengetahui');?></span>
                <?= $form->field($model, 'jabatanmengetahui_id')->dropDownList(
                        $ddlJabatan,
                        [
                            'class' => 'select2 autoListJabatan pembebasantarif-reset',
                            'id' => 'select2_list_jabatan_mengetahui',
                            'prompt' => Yii::t('fe', '-- Pilih jabatan --')
                        ]
                    ); 
                ?>
            </div>
            <div class="col-md-6">
                <span><?=Yii::t('fe', 'Menyetujui');?></span>
                <?= $form->field($model, 'jabatanmenyetujui_id')->dropDownList(
                        $ddlJabatan,
                        [
                            'class' => 'select2 autoListJabatan pembebasantarif-reset',
                            'id' => 'select2_list_jabatan_menyetujui',
                            'prompt' => Yii::t('fe', '-- Pilih jabatan --')
                        ]
                    ); 
                ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-6">
                <?= $form->field($model, 'pegawaimengetahui_id')->widget(DepDrop::classname(), [
                        'options'=>[
                            'id' => 'pegawai_id_mengetahui',
                            'class' => 'form-control pembebasantarif-reset'
                        ],
                        'pluginOptions'=>[
                            'depends'=>['select2_list_jabatan_mengetahui'],
                            'placeholder'=> \Yii::t('fe', '--Pilih pegawai--'),
                            'url'=>Url::to(['/rajal/allow/list-pegawai'])
                        ]
                    ]); 
                ?>
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'pegawaimenyetujui_id')->widget(DepDrop::classname(), [
                        'options'=>[
                            'id' => 'pegawai_id_menyetujui',
                            'class' => 'form-control pembebasantarif-reset'
                        ],
                        'pluginOptions'=>[
                            'depends'=>['select2_list_jabatan_menyetujui'],
                            'placeholder'=> \Yii::t('fe', '--Pilih pegawai--'),
                            'url'=>Url::to(['/rajal/allow/list-pegawai'])
                        ]
                    ]); 
                ?>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<?php
$this->registerJs("
    $('#inf-pembebasan-tarif-form').docoForm('submit',{
        success : function(data) {
            $('#modal_backdrop').modal('toggle');
            table.draw();
        }
    });
    $('.date').AnyTime_noPicker(); 
    $('.date').AnyTime_picker({
        format: '%d %M %Y %H:%i:%s',
        monthNames : ['January','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember'],
        monthAbbreviations : [ 'Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nop','Des' ],
        labelDayOfMonth : 'Tanggal',
        labelYear : 'Tahun',
        labelMonth : 'Bulan',
        labelHour : 'Jam',
        labelMinutes: 'Menit',
        labelSecond: 'Detik',
    });

    $(document).on('click', '.reset-pembebasantarif', function (e) {
        $('#pembebasantarifform-total_pembebasantarif').val(null);
        $('#pembebasantarifform-catatan').val(null);
        $('.pembebasantarif-reset').val(null).trigger('change');
    });
", View::POS_END, 'b-index');
?>