<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-07-25 11:30:48
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-03 17:30:15
 */

use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use dosamigos\ckeditor\CKEditor;
?>

<?php 
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
]); 
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="form-group required">
        <label class="control-label col-sm-4"><?=$model->attributeLabels()['nama_expertise']?></label>
        <div class="col-sm-8">
            <?=Html::activeTextInput($model, 'nama_expertise', ['class'=>'form-control'])?>
        </div>
    </div>
    <br>
    <div class="form-group required">
        <label class="control-label col-sm-4"><?=$model->attributeLabels()['pemeriksaanrad_id']?></label>
        <div class="col-sm-8">
            <?=Html::activeDropdownList($model, 'pemeriksaanrad_id', $options, ['class'=>'form-control select-pemeriksaan'])?>
        </div>
    </div>
    <div class="form-group">
        <label class="control-label col-sm-4"><?=$model->attributeLabels()['pegawai_id']?></label>
        <div class="col-sm-8">
            <?=Html::activeDropdownList($model, 'pegawai_id', $optionsDokter, ['class'=>'form-control select-dokter'])?>
        </div>
    </div>
    <?= $form->field($model, 'hasil_expertise',[
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ],
            ])->widget(CKEditor::className(), [
                'options' => ['rows' => 20],
                'preset' => 'custom',
                'clientOptions'=>[
                    'toolbarGroups'=>[
                        ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
                        ['name' => 'colors'],
                    ]
                ]
            ]) ?>
    <?= $form->field($model, 'kesan',[
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ],
            ])->widget(CKEditor::className(), [
                'options' => ['rows' => 20],
                'preset' => 'custom',
                'clientOptions'=>[
                    'toolbarGroups'=>[
                        ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
                        ['name' => 'colors'],
                    ]
                ]
            ]) ?>
    <?= $form->field($model, 'kesimpulan',[
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-4',
                    'wrapper' => 'col-md-8'
                ],
            ])->widget(CKEditor::className(), [
                'options' => ['rows' => 20],
                'preset' => 'custom',
                'clientOptions'=>[
                    'toolbarGroups'=>[
                        ['name' => 'basicstyles', 'groups' => ['basicstyles', 'cleanup']],
                        ['name' => 'colors'],
                    ]
                ]
            ]) ?>
</div>
<div class="modal-footer">
    <?=Html::submitButton(\Yii::t('fe', '<i class="fa fa-floppy-o"></i> Simpan'), ['class' => 'btn btn bg-teal btn-sm']); ?>
    <?=Html::button(\Yii::t('fe', '<i class="fa fa-arrow-left"></i> Batal'),['class' => 'btn bg-slate btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<?php 
$url = Url::to(["get-pemeriksaan-rad"]);
$this->registerJs('
    $(".select-pemeriksaan").on("focus", function (e) {
      if (e.originalEvent) {
        $(this).siblings("select").select2("open");
      } 
    });
    $("#ajax-form").docoForm("submit",{
        success : function(data) {
            $(".data-reset").click()
            if (data.status == 201)
                this.formInput[0].reset();
            $("#modal_backdrop").modal("toggle");
            table.draw();
        }
    });
    $(document).ready(function(){
        $(".select-pemeriksaan").select2({
            placeholder: "",
            minimumInputLength: 3,
            ajax: {
                url: "'.$url.'",
                dataType: "json",
                quietMillis: 250,
                processResults: function (data) {
                    return {
                        results: data.result
                    };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });

        $(".select-dokter").docoPaginationSelec2(
            // dapat disesuaikan dengan kebutuhan data / customize
            config = {
                placeholder : "-- Pilih Dokter --",      // custom placeholder (optional) default null
                _api : "/igd/master-api/list-all-new-dokter",   // get data
            }
        )
    })
');
?>

