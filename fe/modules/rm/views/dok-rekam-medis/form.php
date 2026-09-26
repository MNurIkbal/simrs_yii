<?php
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\View;
use yii\widgets\Breadcrumbs;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use app\components\DocoHelpers;
?>

<div class="modal-header bg-inverse">
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="panel">
            <div class="panel-toolbar clearfix">
            <?=DocoHelpers::generateToolbar([
                'kembali' => [
                    'type' => 'link',
                    'title' => \Yii::t('fe', 'Kembali'),
                    'icon' => 'fa fa-arrow-left',
                    'method' => 'not exist',
                    'attributes' => [
                        'data-dismiss' => 'modal',
                        'data-options' => 'click',
                        'class' => 'bg-slate data-kembali close',
                    ]
                ],
                'save'=>[
                    'attributes'=>[
                        'id' => 'simpanBtn',
                        'data-target'=>'form-insert-dokumen'
                    ]
                ],
                'custom-reset'=>[
                    'type' => 'click',
                    'title' => \Yii::t('fe', 'Ulang'),
                    'icon' => 'fa fa-refresh',
                    'attributes' => [
                        'id' => 'btn-reset',
                    ]
                ],

            ]);?>
            </div>
        </div>
    </div>
</div>
<?php
$form = ActiveForm::begin([
    'id' => 'form-insert-dokumen',
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-body">
    <?=$form
        ->field($model, 'lokasirak_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($listLokasiRak, 'lokasirak_id', 'lokasirak_nama'), [
            'id' => 'filter_lokasirak', 
            'class' => 'form-control select2 dep-to-child', 
            'prompt' => \Yii::t('fe', '-- Pilih --'),
            'data-url' =>  '/rm/dok-rekam-medis/get-subrak',
            'data-depend_id' => 'filter_subrak_form',
            'data-depend_prompt' => \Yii::t('fe', '-- Pilih --'),
            'data-storage' => 'subrak',
            'data-key' => 'subrak_id',
        ]);
    ?>
    <?=$form
        ->field($model, 'subrak_id', ['labelOptions' => ['class' => 'text-left']])
        ->dropDownList(ArrayHelper::map($selectedListLokasiSubrak, 'subrak_id', 'subrak_nama'), [
            'id' => 'filter_subrak_form', 
            'class' => 'form-control select2 dep-to-parent', 
            'prompt' => \Yii::t('fe', '-- Pilih --'),
            'data-url' =>  '/rm/dok-rekam-medis/get-lokasirak',
            'data-depend_id' => 'filter_lokasirak',
        ]);
    ?>
    <?php if(isset($id)): ?>

        <?= $form->field($model, 'no_rekam_medis', ['labelOptions' => ['class' => 'text-left']])
            ->textInput([
                'class' => 'form-control input-sm',
                'value' => isset($no_rekam_medik) ? $no_rekam_medik : 0,
                'id' => 'no_rekam_medis-1',
                'readonly'=>'readonly'
            ]); ?>

    <?php endIf;?>
    <?php if (!isset($id)) : ?>
            
        <?= $form->field($model, 'no_rekam_medis', ['labelOptions' => ['class' => 'text-left']])
            ->dropDownList([], [
                'class' => 'form-control input-sm no_rm_modal select2',
    // 'value' => isset($no_rekam_medik) ? $no_rekam_medik : 0,
                'id' => 'no_rekam_medis-1'
        ]); ?>

    <?php endif; ?>
    
    <?= Html::hiddenInput('lokasirak_id', $model->lokasirak_id, ['id' => 'lokasirak-id', 'readonly' => 'readonly']) ?>
    <?= Html::hiddenInput('subrak_id', $model->subrak_id, ['id' => 'subrak-id', 'readonly' => 'readonly']) ?>
</div>
<div class="hidden">
    <?=Html::submitButton('Simpan', ['class' => 'btn btn-success btn-sm']); ?>
    <?=Html::button('Kembali',['class' => 'btn btn-default btn-sm', 'data-dismiss' => 'modal']); ?>
</div>
<?php ActiveForm::end(); ?>
<!-- Convert php object to array -->
<?php
    // Declare variables
    $arrayLokasirak = [];
    $arraySubrak = [];

    // Cek lokasi rak
    if (!empty($listLokasiRak)) {
        // Loop
        foreach ($listLokasiRak as $key => $value) {
            // Assign array
            $arrayLokasirak[] = $value;
        }
    }

    // Cek lokasi rak
    if (!empty($listLokasiSubrak)) {
        // Loop
        foreach ($listLokasiSubrak as $key => $value) {
            // Assign array
            $arraySubrak[] = $value;
        }
    }
?>

<script type="text/javascript">
    // Assign data
    var lokasirak = <?php print_r(json_encode($arrayLokasirak)) ?>;
    var subrak = <?php print_r(json_encode($arraySubrak)) ?>;
    var no_rekam_medik = "<?= isset($no_rekam_medik) ? $no_rekam_medik : '' ?>";
    var id_rm = "<?= isset($id) ? $id : 0 ?>";

    var url_no_rm = "/rm/dok-rekam-medis/get-no-rm";
    $(".no_rm_modal").attr('readonly','readonly');
    if(id_rm == 0){
        var url_no_rm = "/rm/dok-rekam-medis/get-no-rekam-medik-pasien";
        $(".no_rm_modal").removeAttr('readonly');

    }
    
    
    // save into localStorage
    // localStorage.clear();
    localStorage.setItem("lokasirak", JSON.stringify(lokasirak));
    localStorage.setItem("subrak", JSON.stringify(subrak));

    $('#form-insert-dokumen').docoForm('submit', {
        success : function(data) {
            this.formInput[0].reset();
            $('#modal_backdrop').modal('hide');
            table.draw();
        }
    });

    // Get element by id and remove class
    var element = document.getElementById("btn-reset");
    element.classList.remove("btn-toolbar");

    // Assign lokasi rak dan subrak
    $("#btn-reset").click(function(event) {
        // Menghapus semua options pada dropdown subrak
        $("#filter_subrak_form option").remove();

        // Assign prompt untuk dimasukan ke dropdown subrak
        var data = {
            id: "",
            text: "-- Pilih --"
        };

        // Option untuk di append ke dropdown subrak
        var option = new Option(data.text, data.id, false, false);

        // Append ke dropdown subrak
        $('#filter_subrak_form').append(option);

        // Deklarasi variable
        var data = [];
        var options = [];

        // Loop
        <?php foreach ($selectedListLokasiSubrak as $key => $value): ?>
            // Assign ke variabel javascript
            data[<?php echo $key ?>] = <?php print_r(json_encode($value)) ?>;
        <?php endforeach ?>

        // Loop data
        for (var i = 0; i < data.length; ++i) {
            // Assign
            var dropdown = {
                id: data[i].subrak_id,
                text: data[i].subrak_nama
            };

            // Option untuk di append ke daftar tindakan di bmhp
            options[i] = new Option(dropdown.text, dropdown.id, false, false);
        }

        // Append ke dropdown subrak
        $('#filter_subrak_form').append(options);

        // Set value to select2
        $("#filter_subrak_form").val($("#subrak-id").val()).trigger("change.select2");

        // Set value select2
        $("#filter_lokasirak").val($("#lokasirak-id").val()).trigger("change.select2");
    });

    $(document).ready(function(){
        
        $(".no_rm_modal").select2({
            placeholder: "No Rekam Medis",
            minimumInputLength: 2,
            ajax: {
                url: url_no_rm,
                dataType: "json",
                quietMillis: 250,
                data: function (term, page) {
                    return {
                        q: term,
                        page: page
                    };
                },
                processResults: function (data) {
                  return {
                    results: data.result
                  };
                }
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { return m; },
        });
        
        // console.log("<?= isset($id) ? $id : 0 ?>");
        $(".no_rm_modal").append("<option value='"+no_rekam_medik+"'>"+no_rekam_medik+"</option>");

    });

</script>
