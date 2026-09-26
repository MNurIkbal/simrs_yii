<?php
// Author : Naufal Ziyad L
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\web\JsExpression;
// use yii\widgets\ActiveForm;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\DatePicker;
use kartik\widgets\ActiveForm;

$title = Yii::t('fe', 'Tambah Group Margin');
?>
<style>
    .datepicker>div{
        display:block;
    }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<div class="modal-body">
    <?php $form = ActiveForm::begin([
            'id' => 'form',
            'type' => ActiveForm::TYPE_HORIZONTAL,
            'enableAjaxValidation' => false,
            'enableClientValidation' => false,
            'validateOnSubmit' => false,
            'formConfig' => [
                'labelSpan' => 4,
                'deviceSize' => ActiveForm::SIZE_MEDIUM
            ],
            'options' => [
                'class' => 'form-horizontal',
                'role' => 'form',
            ]
        ]);
        echo Html::hiddenInput('KonfigMarginForm[konfigmargin_id]', $id, ['class' => 'konfigmargin_id']);
    ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'groupmargin_nama', [
            'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-10'
                ]
            ])->textInput([
                'class' => 'form-control',
            ])->label(Yii::t('fe', 'Group Margin')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'groupmargin_kode', [
            'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-10'
                ]
            ])->textInput([
                'class' => 'form-control',
            ])->label(Yii::t('fe', 'Kode')); ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= $form->field($model, 'is_discount', [
                'horizontalCssClasses' => [
                    'label' => 'text-left control-label col-sm-2',
                    'wrapper' => 'col-md-10'
                ]
            ])->radioList(['1'=>'Ya', '0'=>'Tidak'],[
                'inline' => true,
                'separator' => ' &nbsp;&nbsp;'
            ])?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <hr>
    <div class="modal-footer">
        <?= Html::button("<b><i class='fa fa-floppy-o'></i></b>&nbsp;Simpan", [
            'class' => 'btn btn-info btn-labeled btn-xs',
            'id' => 'btn-submit-mh'
        ]) ?>
        <?= Html::button("<b><i class='fa fa-arrow-left'></i></b>&nbsp;Kembali",[
            'class' => 'btn btn-info btn-labeled btn-xs',
            'data-dismiss' => 'modal'
        ]); ?>
    </div>

</div>
<?php
$datas['row-0'] = [
        'harga_min' => 0,
        'harga_max' => 0,
        'margin' => 0,
        'is_deleted' => false
    ];
$key = 0;
if (!empty($data)) :
    foreach ($data as $key => $value) :
        $datas["row-{$key}"] = [
            'harga_min' => $value['harga_min'],
            'harga_max' => $value['harga_max'],
            'margin' => str_replace('.', ',', number_format($value['margin'],2)),
            'is_deleted' => !empty($key) ? true : false
        ];
    endforeach;
endif;

$dataJson = json_encode($datas);
?>
<script type="text/javascript">

    $('#btn-submit-mh').on('click', function (event) {
        event.preventDefault();
        var _save = true;
        $.each(_jsonData, function(key, val) {
            if (val.harga_min > val.harga_max) {
                docoNotification("error", "Proses Gagal !", "Harga Max tidak boleh kecil dari Nilai Min.");
                $("button[data-id="+ key +"]").closest('tr').css('background','#ff000047');
                 _save = false;
            }
        });
        if (_save) {
            var _form = $("#form").serializeArray();
            $(this).docoForm('click', {
                url: $('#form').attr('action'),
                data: _form,
                success : function(data) {
                    $("#modal_backdrop").modal("toggle");
                    tableTemp.draw();
                    table.draw();
                }
            });
        }
    });

    $(document).ready(function($) {
        $(".input-group-addon.kv-date-remove").remove();
    });
    var tableTemp;
    var _jsonData = <?= $dataJson ?>;
    var id = "<?= $id ?>";
    var counter = <?= empty($key) ? 1 : ($key + 1) ?>;
    var _jsTemp = {};
    var _disabled = false;
    $(function(){
        tableTemp = $('#temp').DataTable({
            data: {},
            filter: true,
            ordering : false,
            paging: false,
            sorting: [[2, "asc"]],
            searching: false,
            info: false,
            columns: [
                {data: 'no'},
                {data: 'harga_min', className : 'text-right'},
                {data: 'harga_max', className : 'text-right'},
                {data: 'margin', className : 'text-right'},
                {data: 'action', className : 'text-center'},
            ],
            fnRowCallback : function (nRow, aData, index) {
                $('td:eq(0)',nRow).html(index + 1);
                return nRow;
            }
        });
        _generateRow(_jsonData);
    });

    /*$(".btn-simpan").on("click", function(event){
        event.preventDefault();
        var _save = true;
        $.each(_jsonData, function(key, val) {
            if (val.harga_min > val.harga_max) {
                docoNotification("error", "Proses Gagal !", "Harga Max tidak boleh kecil dari Nilai Min.");
                $("button[data-id="+ key +"]").closest('tr').css('background','#ff000047');
                _save = false;
            }
        });
        if (_save) {
            var _form = $("#form").serializeArray();
            _form.push({
                'name': 'detail_margin',
                'value': JSON.stringify(_jsonData)
            });
            $(this).docoForm('click', {
                url: $('#form').attr('action'),
                data: _form,
                success : function(data) {
                    $("#modal_backdrop").modal("toggle");
                    tableTemp.draw();
                    table.draw();
                }
            });
        }
    });
*/
    var _generateRow = function (data) {
        var _befValueMax = 0;
        var _befValueMin = 0;
        var _befValueMargin = 0;
        var _count = Object.keys(_jsonData).length;
        var no = 1;
        _jsTemp = {};
        tableTemp.clear().draw();
        $.each(data, function(key, val) {
            _befValueMax = val.harga_max;
            _befValueMargin = val.margin;
            var _readOnly = no < _count ? 'readonly' : '';
            var harga_min = '<input type="text" class="text-right form-control doco-number harga_min" readonly value="'+docoHelper.convertToRupiah(_befValueMin) +'">';

            var harga_max = '<input type="text" class="text-right form-control doco-number harga_max" '+ _readOnly +' value="'+docoHelper.convertToRupiah( _befValueMax) +'">';

            var margin = '<input id="margin-persen-'+key+'" type="text" class="text-right form-control margin" data-val="'+
            _befValueMargin+'" value="'+
            _befValueMargin+'" onchange="persenMargin( this );">';

            if (val.is_deleted) {
                var action = '<button type="button" class="btn btn-danger btn-sm btn-deletes" data-id="'+key+'"><i class="fa fa-trash"></i></button>';
            } else {
                var action = '<button class="btn btn-success addrow btn-sm" data-id="'+key+'"><i class="fa fa-plus"></i></button>';
            }

            tableTemp.row.add({
                'no' : counter,
                'harga_min' : harga_min,
                'harga_max' : harga_max,
                'margin' : margin,
                'action' : action
            }).draw(false);

            _jsTemp[key] = {
                'harga_min' : _befValueMin,
                'harga_max' : _befValueMax,
                'margin' : _befValueMargin,
                'is_deleted' : val.is_deleted,
            }
            _jsonData[key] = {
                'harga_min' : _befValueMin,
                'harga_max' : _befValueMax,
                'margin' : _befValueMargin,
                'is_deleted' : val.is_deleted,
            };
            if (parseInt(_befValueMin) > parseInt(_befValueMax)) {
                $(".addrow").prop("disabled", true);
            }
            _befValueMin = parseInt(_befValueMax) + 1;
            no++;
        });
    }

    function persenMargin(res){
        var id = $(res).attr('id');
        var val = $(res).val();
            val = val.toString();
        var value = docoHelper.convertToDecimal(val, '.', ',' , true);
        $('#'+id).val(value);
    }


    $(document).on("click", ".btn-deletes", function(event) {
        $(this).parent().parent().remove();
        var _idParent = $(this).attr('data-id');
        delete _jsonData[_idParent];
        _generateRow(_jsonData);
    });

    $(document).on("keyup", ".harga_max", function() {
        var _hMax = $(this).val();
        var idParent = $($(this).closest('tr').find("button")).attr('data-id');
        // var _nextTr = $(this).closest('tr').next('tr');

        // if (_nextTr.length > 0) {
        //     var _nextButton = _nextTr.find('button').attr('data-id');
        //     if(typeof _jsonData[_nextButton] != "undefined") {
        //         var _nextHargaMax = docoHelper.convertToAngka(_hMax) + 1;
        //         _jsonData[_nextButton].harga_min = _nextHargaMax;
        //         if (_jsonData[_nextButton].harga_min > _jsonData[_nextButton].harga_max) {
        //             _jsonData[_nextButton].harga_max = _jsonData[_nextButton].harga_min;
        //         }
        //         _nextTr.find('input.harga_min').val(_jsonData[_nextButton].harga_min).trigger('keyup');
        //         _nextTr.find('input.harga_max').val(_jsonData[_nextButton].harga_max).trigger('keyup');
        //     }
        // }

        if(typeof _jsonData[idParent] != "undefined") {
            _jsonData[idParent].harga_max = docoHelper.convertToAngka(_hMax);
            if (_jsonData[idParent].harga_max > _jsonData[idParent].harga_min) {
                $("button[data-id="+ idParent +"]").closest('tr').css('background','');
                $(".addrow").prop("disabled", false);
            } else {
                $("button[data-id="+ idParent +"]").closest('tr').css('background','#ff000047');
                $(".addrow").prop("disabled", true);
            }
        }

    });

    $(document).on("keyup", ".margin", function() {
        var _hMargin = $(this).val();
            _hMargin = _hMargin.toString();
        var _hMargin = docoHelper.convertToDecimal(_hMargin, '.', ',' , true);
        var idParent = $($(this).closest('tr').find("button")).attr('data-id');
        if(typeof _jsonData[idParent] != "undefined") {
            _jsonData[idParent].margin = _hMargin;
        }
    });
</script>
