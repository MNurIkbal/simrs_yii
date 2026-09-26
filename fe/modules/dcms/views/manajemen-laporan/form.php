<?php
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use yii\widgets\ActiveForm;
use yii\web\JsExpression;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'manajemenlaporan-form', 
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <?= Html::hiddenInput('is_update',$is_update,['id'=>'condition_is_update']); ?>
    <div class="form-group required">
        <label class="col-lg-3 control-label"><?=Yii::t('fe', 'Nama Report')?></label>
        <div class="col-lg-6">
            <?= $form->field($modelReport, 'title')
                ->textInput(['id'=>'report_name'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label class="col-lg-3 control-label"><?=Yii::t('fe', 'Kode Report')?></label>
        <div class="col-lg-6">
            <?= $form->field($modelReport, 'code')
                ->textInput(['id'=>'report_code'])->label(false); ?>
        </div>
    </div>
    <div class="form-group required">
        <label class="col-lg-3 control-label"><?=Yii::t('fe', 'Dokumen Tercetak')?></label>
        <div class="col-lg-6">
            <?= $form->field($modelReportConfig, 'docmapping_enabled')
                ->radioList([1 => 'Ya', 0 => 'Tidak'], [
                    'inline' => true
                ])->label(false); ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label"><?=Yii::t('fe', 'Pilih Dokumen Tercetak')?></label>
        <div class="col-lg-6">
            <?php echo $form->field($modelReport, 'docmapping_id')->dropdownList($listDokumen,[
                                            'class' => 'select2',
                                            'prompt' => 'Pilih (kode :: nama)'
                                            ])->label(false);

            ?>
        </div>
    </div>
    <div class="form-group required">
        <label class="col-lg-3 control-label"><?=Yii::t('fe', 'Render Mode')?></label>
        <div class="col-lg-6">
        <?= $form->field($modelReportConfig, 'render_mode')
                    ->dropDownList($listRenderMode,[
                        'placeholder' => 'Pilih',
                        'class' => 'select2'
                        ])->label(false); 
        ?>
        </div>
    </div>
    <div class="form-group">
        <label class="col-lg-3 control-label"><?=Yii::t('fe', 'Tampilkan pada Menu Laporan')?></label>
        <div class="col-lg-6">
        <?= $form->field($modelReportConfig, 'show_in_viewer')
                ->radioList([1 => 'Ya', 0 => 'Tidak'], [
                    'inline' => true
                ])->label(false); 
        ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
                    <?= Html::button("<i class='fa  fa-pencil-square-o' target='_self' href=''>Open Designer</i>",['id'=>'open-save-designer','class'=>'btn bg-green']); ?>
                    <?= Html::submitButton("<i class='fa fa-floppy-o'> Simpan</i>", ['class' => 'btn bg-teal','data-target'=>'ajax-form']) ?>
                    <?= Html::button("<i class='fa fa-arrow-left'> Kembali</i>",[
                                        'class' => 'btn bg-slate',
                                        'data-dismiss' => 'modal'
                                        ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>

<?php
$this->registerJs('
    
    $("#manajemenlaporan-form").docoForm("submit",{
        success : function(data) {
            $("#modal_backdrop").modal("toggle");
            tableLaporan.draw();
        }
    });

    $("#open-save-designer").click(function(){
        $.ajax({
            method:"POST",
            url:"/dcms/manajemen-laporan/save-report?id='.@$id.'",
            data: $("#manajemenlaporan-form").serialize(),
            success:function(response){
                if(response.url){
                    window.open(response.url,"_blank");
                }
                tableLaporan.draw();
                $("#modal_backdrop").modal("toggle");
            }
        });
    });

    function convertToSlug(Text) {
        return Text !== undefined ? Text.toLowerCase()
                   .replace(/[^\w ]+/g, "")
                   .replace(/ +/g, "-") : "";
      }
    
    $("#report_name").keyup(function(){
        if( $("#condition_is_update").val() == 0){
            $("#report_code").val(convertToSlug(this.value));
        }
    });
    
', View::POS_READY, 'e-index');
?>