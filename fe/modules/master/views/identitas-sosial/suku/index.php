<?php
// Author : Ramdhan Nurrachman
// Modify : Naufal Ziyad L
// Modify : Arief Saputra (18/4)

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Suku');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['/master/identitas-sosial']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <h3 class="panel-title"><b><?=$this->title;?></b></h3>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                        'search',
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form-suku'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'id' => 'btn-add-suku',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => Url::home().'master/identitas-sosial/create-suku',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-url' => Url::home().'master/identitas-sosial/edit-suku?id=',
                                'id' => 'btn-edit-suku',
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                                'id' => 'btn-delete-suku',
                                'data-target' => Url::home().'master/identitas-sosial/delete-suku?id=',
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => Url::home().'master/identitas-sosial/export-pdf-suku?',
                                'id' => 'btn-pdf-suku',
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => Url::home().'master/identitas-sosial/export-excel-suku?',
                                'id' => 'btn-excel-suku',
                            ]
                        ],
                    ],'#table-suku');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-suku"></div>
                </div>
                <table id="table-suku" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama Suku");?></th>
                            <th><?=\Yii::t("fe", "Nama Lainnya");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="8">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
    </div>
</div>

<div id="modal_suku" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>

<script>
    var tableSuku;

    $(document).ready(function() {

        tableSuku = $('#table-suku').docoTabel({
            filter: true,
            columnDefs: [ {
                orderable: false,
                className: 'select-checkbox',
                targets: 0,
                checkboxes: {
                    selectRow: true
                }
            }],
            select: {
                style: 'os',
                selector: 'tr'
            },
            sorting: [[3, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            // scrollX: true,
            ajax: baseUrl+"master/identitas-sosial/get-data-suku",
            columns: [
                {
                    title: '',
                    data: null,
                    defaultContent: '',
                    searchable: false,
                    orderable: false
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                },
                {title: "<?=Yii::t('fe', 'Nama Suku')?>",  data: "suku_nama"},
                {title: "<?=Yii::t('fe', 'Nama Lainnya')?>", data: "suku_namalainnya"},
                {title: "Status",  data: "status"},
            ]
        });

        $(".dataTables_filter").hide();

        $(".filter-form-suku").datatableBootstrapFilter(tableSuku,
            [
                [2, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('suku_nama', '', ['class' => 'form-control','placeholder'=>'Nama Suku'])));?>'],
                [3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('suku_namalainnya', '', ['class' => 'form-control','placeholder'=>'Nama Lainnya'])));?>'],
                [4, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>']
            ]
        );

        // Event Delete
        $(document).on("click", ".data-delete-suku", function(e) {
            e.preventDefault();
            $(this).docoForm("delete",{
                success : function (data) {
                    tableSuku.draw()
                }
            });
            return false;
        });

        // Event Reload
        $(document).on("click", ".data-reload", function() {
            tableSuku.draw();
        });

    });
</script>
