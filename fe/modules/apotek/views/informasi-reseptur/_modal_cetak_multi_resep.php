<?php


use yii\web\View;
use app\components\DocoHelpers;
use kartik\select2\Select2;
use kartik\widgets\ActiveForm;
use yii\widgets\Breadcrumbs;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\web\JsExpression;

$this->title = \Yii::t('fe', 'Riwayat Pasien');
$this->params['breadcrumbs'][] = ['label' => $title, 'url' => ['/apotek']];
$this->params['breadcrumbs'][] = $title;
?>

<style lang="">
    .info-pasien {
        width: 70px;
        height: 30px;
        border-radius: 5px;
        border: 1px solid black;
        float: left;
        margin: 3px;
    }

    .btn-riwayat {
        margin-bottom: 5px;
        margin-right: 5px;
    }

    .input-catatan {
        padding: 10px;
        padding-left: 1px;
    }
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <?php
        $form = ActiveForm::begin([
            'id' => 'modal-cetak-multi-resep',
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
                'enctype' => 'multipart/form-data'
            ]
        ]);
    ?>
    <div class="row">
        <div class="panel-toolbar clearfix">
            <div class="col-md-4">
                <?=DocoHelpers::generateToolbar([
                    'print-multiple-resep' => [
                        'type' => 'link',
                        'title' => Yii::t('fe', 'Print Multiple Obat'),
                        'method' => '#',
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id' => 'btn-print-multiple-resep',
                            'class' => 'btn-print-resep-multi',
                            'data-options' => 'click',
                            'data-target' => Url::home().('apotek/informasi-reseptur/print-multiple-resep?'),
                            'data-conditions' => 'obatalkes_nama,noresep',
                            'data-table-id' => 'example1'
                        ]
                    ],

                ], '#tbl-resep-multiple');?>
            </div>
            <br>
            <div class='col-md-12'>
                <div class="col-md-5 input-catatan">
                    <?= $form->field($modelReseptur, 'waktu_pemberian')->dropDownList($waktuPemberian, ['class' => 'select2 waktu_pemberian', 'prompt' => '--Pilih--']) ?>
                </div>
                <div class="col-md-5 input-catatan">
                <?= $form->field($modelReseptur, 'keterangan_pemberian')->dropDownList($keteranganPemberian, ['class' => 'select2 keterangan_pemberian', 'prompt' => '--Pilih--']) ?>
                </div>
            </div>
            <div class='col-md-6'>
                <input id = "checkObat" type="checkbox" name="terms" label="Pilih Semua Obat"/>
                <label>Pilih Semua Obat</label>
            </div>
        </div>
        <div class="col-md-12">
            <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tbl-resep-multiple" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1"></th>
                        <th width="1">No</th>
                        <th><?= Yii::t('fe', 'R ke') ?></th>
                        <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                        <th><?= Yii::t('fe', 'Signa') ?></th>
                        <th><?= Yii::t('fe', 'Reseptur ID') ?></th>
                        <th><?= Yii::t('fe', 'No Resep') ?></th>
                    </tr>
                </thead>
                <tbody id="list-resep-obat">
                    <tr>
                        <td colspan="8" class="text-center"><?= Yii::t('fe', 'Data tidak ditemukan') ?></td>
                    </tr>
                </tbody>
                <tfoot>

                </tfoot>
            </table>
        </div>
    </div>
    <?= $form->field($modelReseptur, 'catatan')->hiddenInput(['id' => 'data_catatan'])->label(false); ?>
</div>
<?php ActiveForm::end(); ?>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
<?php
$this->registerJs("
    var btn;
    var status_reseptur = '".$status_resep."'
    var test = '".$_GET['id']."'
    var nomorReseptur = '".$nomor."'
    $(document).ready(function(){
        table = $('#tbl-resep-multiple').docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets:   0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: 'multi',
                selector: 'tr'
            },
            sorting: [[3, `asc`]],
            order: [[3, `asc`]],
            bInfo: false,
            paging: false,
            bPaginate: false,
            // displayLength: 10,
            processing: true,
            serverSide: true,
            scrollX: true,
            ajax: function(data, callback, settings){
                $.ajax({
                    url: baseUrl+'apotek/informasi-reseptur/get-data-obat?id=".$_GET['id']."&noresep=".$nomor."&status_reseptur=' + status_reseptur,
                    data: data,
                    success: function(data)
                    {
                        callback(data);
                    }
                });

            },
            drawCallback: function(setting){
            },
            columns: [
                {
                    data: 'null',
                    searchable: false,
                    orderable: false,
                    defaultContent:'',
                    width: '7%',
                },
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '".(\Yii::t('fe', 'R ke'))."',
                    data: 'rke',
                    searchable: false,
                    orderable: false,
                },
                {
                    title: '".(\Yii::t('fe', 'nama obat alkes'))."',
                    data: 'obatalkes_nama',
                    searchable: false,
                    orderable: true
                },
                {
                    title: '".(\Yii::t('fe', 'Signa'))."',
                    data: 'signa_nama',
                    searchable: false,
                    orderable: false
                },
                {
                    title: '".(\Yii::t('fe', 'Reseptur ID'))."',
                    data: 'reseptur_id',
                    searchable: false,
                    orderable: false,
                    visible: false
                },
                {
                    title: '".(\Yii::t('fe', 'No Resep'))."',
                    data: 'noresep',
                    searchable: false,
                    orderable: false,
                    visible: false
                },

            ],

        });

        $('.dataTables_filter').hide();
        $(table.table().footer()).html('Your html content here ....');
        $('#btn-print-multiple-resep').attr('disabled', true);
    });
",VIEW::POS_END, 'js-kuning');
$this->registerJs($this->render("js/_modal_cetak_multi_resep.js"));
?>
