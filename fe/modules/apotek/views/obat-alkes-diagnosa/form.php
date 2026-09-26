<?php
/*use Yii; */
use kartik\widgets\ActiveForm;
use yii\helpers\Html;
use yii\web\View;
use app\components\DocoHelpers;
use yii\helpers\Url;
use kartik\widgets\DepDrop;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\widgets\Typeahead;
?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<input type="hidden" class="state" value="<?=$state?>">
<input type="hidden" class="diagnosa_id" value="<?=$diagnosa_id?>">
<input type="hidden" class="diagnosa_nama" value="<?=$diagnosa_nama?>">
<input type="hidden" class="obatalkes_id" value="<?=$obatalkes_id?>">
<input type="hidden" class="obatalkes_nama" value="<?=$obatalkes_nama?>">
<hr>
<div class="modal-body">
    <?php 
    $form = ActiveForm::begin([
            'id' => 'menu-kasus-penyakit', 
            'enableAjaxValidation'=>false, 
            'enableClientValidation'=>false,
            'options' => [
                    'class' => 'form-horizontal', 
                    'enableAjaxValidation' => true,
                    'role' => 'form'
                ],
            ]); 
    ?>
    <div class="form-group required">
        <label for="inputPassword" class="col-md-3 control-label"><?= Yii::t('fe','Diagnosa Pasien'); ?></label>
        <div class="col-md-8" style="margin-top: 5px">
            <?php 
            $template = '<div><p class="repo-language">{{value}}</p></div>';
            echo $form->field($model,'diagnosa_id')->dropDownList([], ['prompt'=>'-- pilih --', 'class'=>'selectDiagnosa'])->label(false);
            ?>
        </div>
    </div>
    <div class="form-group required">
        <label for="inputPassword" class="col-md-3 control-label"><?= Yii::t('fe','Nama Obat'); ?></label>
        <div class="col-md-8" style="margin-top: 5px">
            <?php 
            $template = '<div><p class="repo-language">{{value}}</p></div>';
            echo $form->field($model,'obatalkes_id')->dropDownList([], ['prompt'=>'-- pilih --', 'class'=>'selectObat'])->label(false);
            ?>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
            <?= Html::submitButton('<i class="fa fa-floppy-o"></i>&nbsp;Simpan', 
                    [
                        'class' => 'btn bg-teal btn-md'
                    ]) 
            ?>
            <?= Html::button('<i class="fa fa-arrow-left"></i>&nbsp;Kembali',[
                                'class' => 'btn bg-slate btn-md',
                                'data-dismiss' => 'modal'
                                ]); ?>
    </div>

<?php ActiveForm::end(); ?>
</div>
<?php 

$this->registerJs("
    $(document).ready(function(){
        var state = $('.state').val();

        if(state != ''){
            var diagnosa_id = $('.diagnosa_id').val();
            var diagnosa_nama = $('.diagnosa_nama').val();

            var obatalkes_id = $('.obatalkes_id').val();
            var obatalkes_nama = $('.obatalkes_nama').val();

            var diagnosaOptions = new Option(diagnosa_nama,diagnosa_id, true, true);
            var obatOptions = new Option(obatalkes_nama,obatalkes_id, true, true);
            
            $('.selectDiagnosa').append(diagnosaOptions).trigger('change');
            $('.selectObat').append(obatOptions).trigger('change');
        }
        $('.selectDiagnosa').select2({
            placeholder: '&nbsp;',
            minimumInputLength: 3,
            ajax: {
                url: '".Url::to(['/apotek/obat-alkes-diagnosa/list-diagnosa'])."',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });

        $('.selectObat').select2({
            placeholder: '&nbsp;',
            minimumInputLength: 3,
            ajax: {
                url: '".Url::to(['/apotek/obat-alkes-diagnosa/list-obat-alkes'])."',
                dataType: 'json',
                quietMillis: 250,
                data: function(term, page){
                    return{
                        q: term,
                        page: page
                    }
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: 'bigdrop',
            escapeMarkup: function (m) { return m; },
        });
    })
    
    ",VIEW::POS_END, 'js-form');

?>
<script type="text/javascript">
    $(function(){
        $("#menu-kasus-penyakit").submit(function(event){
            event.preventDefault();
            $(this).docoForm('submit',{
                success : function(data) {
                    $('#modal_backdrop').modal('toggle');
                    table.draw();
                }
            });
        });
    });

</script>