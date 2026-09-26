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
        'id' => 'invoice-form',
        'enableAjaxValidation' => false,
        'enableClientValidation' => false,
        'type' => ActiveForm::TYPE_HORIZONTAL,
    ]);
    ?>
    <div class="row">
        <div class="panel-toolbar clearfix">
            <div class="col-md-4">
                <?=DocoHelpers::generateToolbar([
                    'print-multiple-etiket' => [
                        'type' => 'link',
                        'title' => Yii::t('fe', 'Print Multiple Obat'),
                        'method' => '#',
                        'icon' => 'fa fa-print',
                        'attributes' => [
                            'id' => 'btn-print-multiple-etiket',
                            'class' => 'btn-print-etiket-multi',
                            'data-options' => 'click',
                            'data-target' => Url::home().'apotek/transaksi-resep/print-multiple-etiket?nomor='.$nomor.'&',
                            'data-conditions' => 'resepturdetail_id,obatalkespasien_id',
                            'data-table-id' => 'tbl-resep-multiple-etiket',
                            'disabled' => 'disabled'
                        ]
                    ],

                ], '#tbl-resep-multiple-etiket');?>
            </div>
            <br>
            <div class='col-md-12'>
                <label class="col-md-2 control-label"><?=Yii::t('fe', 'Jenis Obat')?></label>
                <div class="col-md-4">
                    <?= Html::radioList('is_oral', 'semua', ['semua'=>'Semua','non_oral'=>'Non Oral','oral'=>'Oral'], ['inline' => true,'class'=>'']) ?>
                </div>
                <label class="col-md-2 control-label"><?=Yii::t('fe', 'Obat')?></label>
                <div class="col-md-4">
                <?= Html::input('text', 'obat', '', ['id'=>'search-obat-etiket','class' => 'form-control']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <table class="table datatable-basic table-striped table-hover dataTable no-footer" id="tbl-resep-multiple-etiket" style="width: 100%">
                <thead>
                    <tr class="bg-inverse">
                        <th width="1"></th>
                        <th width="1">No</th>
                        <th width="1">Detail</th>
                        <th width="1">Detail</th>
                        <th><?= Yii::t('fe', 'R ke') ?></th>
                        <th><?= Yii::t('fe', 'Nama Obat Alkes') ?></th>
                        <th><?= Yii::t('fe', 'Jenis Obat') ?></th>
                        <th><?= Yii::t('fe', 'Signa') ?></th>
                    </tr>
                </thead>
                <tbody id="list-resep-obat">
                    <?php
                        $no=1;
                        foreach ($data_resep as $key => $value):
                    ?>
                        <tr>
                            <td class="select-checkbox"></td>
                            <td><?=$no;?></td>
                            <td><?=!empty($value['resepturdetail_id']) ? DocoHelpers::encrypt($value['resepturdetail_id']) : null; ?></td>
                            <td><?=!empty($value['obatalkespasien_id']) ? DocoHelpers::encrypt($value['obatalkespasien_id']) : null; ?></td>
                            <td><?=@$value['rke'];?></td>
                            <td><?=@$value['obatalkes_nama'];?></td>
                            <td><?=isset($value['is_oral']) ? $value['is_oral'] == TRUE ? 'Oral' : 'Non Oral' : '-';?></td>
                            <td><?=@$value['signa_nama'];?></td>
                        </tr>
                    <?php
                        $no++;
                        endforeach;
                    ?>
                </tbody>
                <tfoot>

                </tfoot>
            </table>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<div class="modal-footer">
    <?= Html::button("<i class='fa fa-arrow-left'></i> " . Yii::t('fe', 'Kembali'), [
        'class' => 'btn bg-slate',
        'data-dismiss' => 'modal'
    ]); ?>
</div>
<?php
$this->registerJs("
$(document).ready(function(){
    table = $('#tbl-resep-multiple-etiket').DataTable({
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
        columns: [
            { data: 'checked' },
            { data: 'rowNum' },
            { data: 'resepturdetail_id', visible:false },
            { data: 'obatalkespasien_id', visible:false },
            { data: 'rke' },
            { data: 'obatalkes_nama' },
            { data: 'is_oral' },
            { data: 'signa_nama' }
        ]
    });
    $('#tbl-resep-multiple-etiket_filter').hide();

    $('#search-obat-etiket').on('keyup', function () {
        console.log(table.row('.selected').data())
        var filteredData = table
        .column(5)
        .search( $('#search-obat-etiket').val() ).draw();
    });

    $('input[type=radio][name=is_oral]').change(function() {
        if (this.value == 'semua') {
            table.column(6).search('').draw();
        }else if (this.value == 'oral') {
            table.column(6).search('^Oral',true,false).draw();
        }else if (this.value == 'non_oral') {
            table.column(6).search('Non Oral',true).draw();
        }
    });
});
",VIEW::POS_END, 'js-kuning');
$this->registerJs($this->render("_modal_cetak_multi_etiket.js"));
?>
