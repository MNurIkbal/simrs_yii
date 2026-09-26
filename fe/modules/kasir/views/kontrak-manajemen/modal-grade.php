<?php

use app\components\DocoHelpers;
use kartik\widgets\ActiveForm;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\web\JsExpression;
use yii\web\View;
use yii\widgets\Breadcrumbs;

?>

<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">

<?php $forms = ActiveForm::begin([
        'id' => 'forms', 
        'action' => "/kasir/kontrak-manajemen/create",
        'enableAjaxValidation'=>false, 
        'enableClientValidation'=>false,
        'type' => ActiveForm::TYPE_HORIZONTAL,
        'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL] 
    ]); 
    ?>
    <div class="row">
        <div class="col-md-11">
        <?= $forms->field($model, 'grade')->dropDownList($grade_map,[
	            'class' => 'form-control select2 selectGrade',
                'prompt' => Yii::t('fe', '—Pilih Grade atau Ketik Grade Baru —'),
	            'id' => 'grade_id',
	        ])->label(Yii::t('fe', 'Grade')); ?>
        </div>                                
    </div>

    <?php ActiveForm::end(); ?>
    <div class='row'></div></br>
    <div class="row">
        <div class="col-md-12">
        <table id="tabelgrade" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th>No</th>
                    <th><?=\Yii::t("fe", "LOB");?></th>
                    <th><?=\Yii::t("fe", "Tipe Diskon");?></th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="text-center" colspan="9"><?=\Yii::t("fe", "Data tidak ditemukan.");?></td>
                </tr>
            </tbody>
        </table>
        </div>
    </div>
    <hr>
    <div class="modal-footer">
            <?= Html::submitButton('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', ' Simpan'), [
                'class' => 'btn bg-teal',
                'id' => 'btn-tambah-grade',
            ]) ?>
            <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>'.Yii::t('fe', ' Kembali'),[
                'class' => 'btn bg-slate',
                'data-dismiss' => 'modal'
            ]) ?>
    </div>
</div>

<script type="text/javascript">  
    var flag_click = true;
    $(document).ready(function(){
        $('#grade_id').select2({
            tags : false,
            escapeMarkup: function (markup) { return markup; },
            "language": {
                    "noResults": function(term){
                   return "<a id='add_map_grade' style='float:right; color:blue;'><u>Tambah Grade Baru</u></a>";
                }    
            },
        });
        $('#btn-delete-mapping-grade').hide();
        $('#btn-tambah-mapping-grade').hide();
        table = $("#tabelgrade").docoTabel({
            orderable: false,
            className: "select-checkbox",
            displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            paging: false,
            sorting: [[1, "desc"]],
            ajax: baseUrl+"kasir/kontrak-manajemen/get-list-lob",
            columns: [
                {   
                    title: 'No',
                    data: 'primary_key',
                    searchable: false,
                    orderable: false,
                    defaultContent: "",
                },
                {
                    title: "LOB", 
                    data: "lookup_name",
                    searchable:false,
                    orderable: false
                },
                {
                    title: "Diskon",
                    data: "diskon",
                    searchable: false,
                    orderable: false,
                    class: "text-center"
                }
            ],
        });
       $('#tabelgrade_filter').hide();
       $(".chk_pilihan, .multiselect-container input").uniform({
                    radioClass: 'choice'
        });
                
     
    });

    // $(document).on("click", ".chk_pilihan", function(){
    //     $(this).checked(true);
    // });

    $(document).on('click','#add_map_grade', function(event){
            if($('.select2-search__field').val() !== null && $('.select2-search__field').val() !== undefined && flag_click == true){
                flag_click = false;
                grade_selected = $('.select2-search__field').val();
                penjamin_selected = $('#penjamin_id option:selected').val();
                    var resGrade = {
                        grade_value : grade_selected,
                        penjamin_value : penjamin_selected,                
                    };
                    $('#grade_id').select2('close');    
                $().docoForm("click", {
                url: "/kasir/kontrak-manajemen/create-map-grade",
                data: resGrade,
                skipConfirm: true,
                success: function (data) {
                    docoNotification("success", i18next.t("Berhasil"), i18next.t("Nama Grade berhasil ditambahkan"));  
                    var option = new Option(data['result']['grade']);                                  
                    $("#grade_id").append(option);            
                    $('#grade_id option:last').val(data['result']['penjamingrade_id']);
                    $('#grade_id option:last').attr('selected', 'selected');
                    flag_click = true;

                    }
                });
            };                             
    });

    $("#btn-tambah-grade").on("click", function (event) {
        var diskon_id = [];
        var diskon_name = [];
        var data_grades = [];

        var message_error = '';
        var grade_name = $("#grade_id option:selected").val();      
        var data_grade = table.rows({selected: true}).data();

        $('#tabelgrade tbody tr ').each((index,element) => {
            $(element).find('.diskongrade option:selected').each(function(){
                if ($(this).attr('value') != ''){
                    diskon_id.push($(this).attr('value'));
                        diskon_name.push($(this).text());                    
                }             
            })
        });
        var i = 0; select_diskon = 0; check_lob = true;
        $('#tabelgrade tbody tr ').each((index,element) => {
            
            if ($(element).find('.diskongrade option:selected').attr('value') !== ''){
                select_diskon++;
                chk_diskon = true;                
                data_grades.push({
                    tipediskon_id : $(element).find('.diskongrade option:selected').attr('value'),
                    tipediskon_nama : $(element).find('.diskongrade option:selected').text(),
                    grade : $('#grade_id option:selected').text(),
                    penjamingrade_id : $('#grade_id option:selected').val(),
                    lookup_id : $(element).find('.chk_pilihan').attr('name'),
                    lookup_name : $(element).find('.chk_pilihan').attr('data-id'),
                    lookup_value :  $(element).find('.chk_pilihan').attr('data-id'),
                    lookup_type: "LOB",
                });            
            } else chk_diskon = false;
            chk_pilihan = $(element).find('.chk_pilihan').is(':checked');
            if(chk_diskon != chk_pilihan){
            check_lob = false;
            if(chk_pilihan == false){
                message_error = "Ada pilihan LOB yang belum di checklist. Silahkan cek input LOB.";
            } else if(chk_diskon == false){
                message_error = "Ada Tipe Diskon yang belum terpilih. Silahkan cek pilihan Tipe Diskon.";
            }

            }
            i++;
            
        });
        if(check_lob == false){
            docoNotification("warning", i18next.t("Perhatian"), i18next.t(message_error));
            return false;
        }
        else if (select_diskon == 0){
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Pilihan LOB belum di checklist. Silahkan cek input LOB."));
            return false;
        } else if (!grade_name || grade_name.length == 0) {
            docoNotification("warning", i18next.t("Perhatian"), i18next.t("Nama Grade perlu diisi"));
            return false;
        } else {
            var resData1 = {
                data_grader: data_grades,
            };

            $().docoForm("click", {
               url: "/kasir/kontrak-manajemen/set-cache-grade",
               data: resData1,
               skipConfirm: true,
               success: function (data) {
                tableGradePenjamin.draw();
                $('#modal_backdrop').modal('hide');
               }
            });
        }

    });
        
</script>
