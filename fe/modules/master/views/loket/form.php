<?php

/**
 * @Author: Arief Saputra
 * @Date:   2018-02-22 11:40:04
 * @Last Modified by:   Sigit
 * @Last Modified time: 2018-10-25 10:52:31
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use kartik\widgets\DepDrop;
use kartik\widgets\ActiveForm;
use yii\web\JsExpression;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Loket'), 'url' => ['/master/loket']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <?=Breadcrumbs::widget([
                    'homeLink' => [ 
                        'label' => Yii::t('yii', 'Home'),
                        'url' => Yii::$app->homeUrl,
                    ],
                    'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
                ]);?>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                        <li><a data-action="reload"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'back',
                    'save' => [
                        'type' => 'button',
                        'attributes' => [
                            'id' => 'simpan-loket',
                            'data-options' => 'click',
                            'class' => 'btn btn-info btn-labeled btn-xs data-edit btn-toolbar'
                        ]
                    ],
                    'reset',
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-8">
                    <?php 
                        $form = ActiveForm::begin([
                            'id'=>'loket-form',
                            'enableAjaxValidation'=> false, 
                            'enableClientValidation'=> false,
                            'type' => ActiveForm::TYPE_HORIZONTAL,
                            'formConfig' => ['showErrors' => true, 'labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
                        ]);
                    ?>
                        <?= $form->field($model, 'jenisantrian_id',[
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->dropDownList(ArrayHelper::map($ddljenis_antrian, 'lookup_id', 'lookup_name'),[
                                'class' => 'form-control select2 ddljenis_antrian',
                                'id' => 'jenisantrian_id',
                                'prompt' => Yii::t('fe','--Pilih--')
                            ]); ?>

                       <?= $form->field($model, 'nama_loket', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-8'
                                        ],
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('nama_loket'),
                                        'class' => 'form-control',
                                        'autocomplete' => "off",
                                    ]); ?>

                        <div class="form-group field-konfigantrian_id" style="display: none;">
                            <label class="control-label text-left control-label col-sm-4">
                                <?= $model->getAttributeLabel('konfigantrian_id') ?>
                                <br>
                                ( Cara bayar - Klasifikasi pasien (Kode antrian))
                            </label>
                            <div class="col-md-8 list-pengambilan-checkbox">
                            </div>
                        </div>

                        <?= $form->field($model, 'ruangan_id',[
                            'horizontalCssClasses' => [
                                    'label' => 'text-left control-label col-sm-4',
                                    'wrapper' => 'col-md-8'
                                ],
                            ])->dropDownList($ddlruangan_id,[
                                'class' => 'form-control select2 ddlruangan_id',
                                'id' => 'ruangan_id',
                                'prompt' => Yii::t('fe','--Pilih--')
                            ]); ?>

                       <?= $form->field($model, 'no_loket', [
                                    'horizontalCssClasses' => [
                                            'label' => 'text-left control-label col-sm-4',
                                            'wrapper' => 'col-md-3'
                                        ],
                                    ])->textInput([
                                        'placeholder' => $model->getAttributeLabel('no_loket'),
                                        'class' => 'form-control',
                                        'autocomplete' => "off",
                                        'type' => 'number'
                                    ]); ?>
                        <?php
                            if ($isUpdate == 1) {
                        ?>
                            <div class="form-group field-pemesanan-obat-qty">
                                <label class="control-label text-left control-label col-sm-4" for="pemesanan-obat-qty"><?= $model->getAttributeLabel('Status') ?></label>
                                <div class="col-md-8">
                                    <?=
                                        $form->field($model, 'is_active', [
                                            'horizontalCssClasses' => [
                                                'label' => 'text-left control-label col-sm-4',
                                                'wrapper' => 'col-md-8',
                                            ],
                                        ])->checkbox([
                                            'class' => 'styled checked-tablel',
                                            'label' => 'Aktif',
                                            'checked' => 'checked'
                                        ])
                                    ?>
                                </div>
                            </div>
                        <?php } ?>
                    <?php ActiveForm::end() ?>
                    </div>
                    <div class="col-md-12">
                        <table width="100%" class="table datatable-basic table-striped table-hover dataTable no-footer" id="data-loket" data-source="<?=Url::home();?>" data-filter=".form-filter" data-test="true">
                            <thead class="bg-inverse">
                                <tr>
                                    <th width="20">No</th>
                                    <th><?=\Yii::t('fe', 'Nama Jenis Antrian'); ?></th>
                                    <th><?=\Yii::t('fe', 'Kode Antrian'); ?></th>
                                    <th><?=\Yii::t('fe', 'No Loket'); ?></th>
                                    <th><?=\Yii::t('fe', 'Nama Loket'); ?></th>
                                    <th width="5%"><?=Yii::t('fe', 'Status'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function cekJenisPengambilan(idx)
    {
        var atLeastOneIsChecked = $('input[name="LoketForm[konfigantrian_id][]"]:checked').length > 0;

        if (atLeastOneIsChecked) {
            var check_id =  $(idx).parent().attr('id');
            $(".list-pengambilan-checkbox input").attr("disabled", true);
            $("#"+check_id+" input").attr("disabled", false);
        } else {
            $(".list-pengambilan-checkbox input").attr("disabled", false);
        };

    }
</script>
<?php
$urlJenis = Url::to(['/master/loket/add-item-konfig','id' => $val_konfig]);
if (!empty($val_konfig)) {
    $decrypted_val_konfig = DocoHelpers::decrypt($val_konfig);
} else {
    $decrypted_val_konfig = json_encode($val_konfig);
}
$this->registerJs("
var is_update = ".$isUpdate.";
var val_konfig = ".$decrypted_val_konfig.";
var is_keteranganpasien = false;
$('.field-konfigantrian_id').hide();
$('.field-ruangan_id').hide();

$('#loket-form').docoForm('submit',{
  success : function(data) {
    tableloket.draw();
    $('#loket-form')[0].reset();
    $('.select2').val('').trigger('change');
    location.reload()
  }
});

var tableloket;
$(document).ready(function() {
    numRow = 1;

    // Generate Table
    tableloket = $('#data-loket').docoTabel({
        filter: true,
        sorting: [[2, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        oLanguage: {
            sLengthMenu: '".(\Yii::t('fe', 'dt_length_menu'))."',
            sZeroRecords: '".(\Yii::t('fe', 'dt_zero_records'))."',
            sEmptyTable: '".(\Yii::t('fe', 'dt_empty_table'))."',
            sInfoFiltered: '".(\Yii::t('fe', 'dt_info_filtered'))."',
            sInfoEmpty: '".(\Yii::t('fe', 'dt_info_empty'))."',
            sInfo: '".(\Yii::t('fe', 'dt_info'))."',
            oPaginate: {
                sFirst: '".(\Yii::t('fe', 'dt_first_page'))."',
                sPrevious: '".(\Yii::t('fe', 'dt_previous_page'))."',
                sNext: '".(\Yii::t('fe', 'dt_next_page'))."',
                sLast: '".(\Yii::t('fe', 'dt_last_page'))."'
            }
        },
        ajax: {
            url:baseUrl+'master/loket/get-data?is_flag=1',
            type:'POST'
        },
        columns: [
            {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {title: '".(\Yii::t('fe', 'Nama Jenis Antrian'))."', data: 'jenis_name'},
            {title: '".(\Yii::t('fe', 'Kode Antrian'))."', data: 'jenis_value'},
            {title: '".(\Yii::t('fe', 'No Loket'))."', data: 'loket_nourut'},
            {title: '".(\Yii::t('fe', 'Nama Loket'))."', data: 'loket_namalain',searchable: false},
            {title: '".(\Yii::t('fe', 'Status'))."', data: 'status'},
        ],
        order: [[ 2, 'desc' ]],
        'responsive': true,
        drawCallback: function (settings) {
            var api = this.api();
            var rows = api.rows({ page: 'current' }).nodes();
            var last = null;

            api.column(2, { page: 'current' }).data().each(function (group, i) {

                if (last !== group) {

                    $(rows).eq(i).before(
                        '<tr class=\'group\'><td colspan=\'8\' style=\'BACKGROUND-COLOR:rgb(181, 216, 197);font-weight:700;color:#006232;\'>' + group  + '</td></tr>'
                    );

                    last = group;
                }
            });
        }
    });

    $('.dataTables_filter').hide();

    if (is_update == 1) {
        $('#jenisantrian_id').trigger('change');
    }
});

$(document).on('click', '#simpan-loket', function (event) {
    event.preventDefault();
    let data = $('#loket-form').serializeArray()
    let url;
    if(is_update) {
        url = window.location.origin + '/master/loket/update?id=".$idEncyrpt."'
    }else{
        url = window.location.origin + '/master/loket/create'
    }
        
    $(this).docoForm('click', {
        skipConfirm: true,
        skipNotifyMessage: true,
        skipErrorNotif: true,
        method: 'POST',
        data: data,
        url: url,
        success: function(res) {
            $('#loket-form').trigger('reset');
            tableloket.draw()
            setTimeout(() => {
                window.location.href = '/master/loket'
            }, 100)
        }
    })
});

$('#jenisantrian_id').change(function(){
    if($(this).val() == 176){
        $('.field-konfigantrian_id').show();
        $('.field-ruangan_id').show();
    } else{
        if($(this).val() == 177 && is_keteranganpasien) {
            $('.field-konfigantrian_id').hide();
        } else {
            $('.field-konfigantrian_id').show();
        }
        $('.field-ruangan_id').hide();
    }


    $.ajax({
        type: 'POST',
        url: '{$urlJenis}',
        data: {
            jenisantrian_id: $(this).val()
        },
        error: function() {
            console.log('An error has occurred');
        },
        dataType: 'json',
        success: function(data) {
            var input_id = [];
            $('.list-pengambilan-checkbox').html('');
            $.each(data.output, function(i,v){
                if(jQuery.inArray(v.konfigantrian_id, val_konfig) !== -1) {
                    $('.list-pengambilan-checkbox').append('<div class=\'col-sm-4 data-pengambilan\' id=\'jpa-'+v.kode_antrian+'\'><input type=\'checkbox\' id=\'jpa-'+v.konfigantrian_id+'-'+v.kode_antrian+'\' name=\'LoketForm[konfigantrian_id][]\' onchange=\'cekJenisPengambilan(this)\' value='+v.konfigantrian_id+' checked> '+ v.konfig_name +'</div>');

                    input_id.push('#jpa-'+v.konfigantrian_id+'-'+v.kode_antrian);
                } else {
                    $('.list-pengambilan-checkbox').append('<div class=\'col-sm-4 data-pengambilan\' id=\'jpa-'+v.kode_antrian+'\'><input type=\'checkbox\' id=\'jpa-'+v.konfigantrian_id+'-'+v.kode_antrian+'\' name=\'LoketForm[konfigantrian_id][]\' onchange=\'cekJenisPengambilan(this)\' value='+v.konfigantrian_id+'> '+ v.konfig_name +'</div>');
                }
            });

            if (input_id.length > 0) {
                $.each(input_id, function (key, value) {
                    console.log(value)
                    $(value).trigger('change');
                });
            }
        },
    });
    

});

$(document).on('click', '.data-reset', function (event) {
    event.preventDefault();

    location.reload();
});

",View::POS_END,'jkun');
