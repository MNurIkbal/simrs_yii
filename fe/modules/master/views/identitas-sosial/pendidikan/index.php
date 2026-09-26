<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use yii\widgets\Breadcrumbs;

$this->title = Yii::t('fe', 'Pendidikan');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
                                'data-parent'=>'.filter-form-pendidikan'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'id' => 'btn-add-pendidikan',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => Url::home().'master/identitas-sosial/create-pendidikan',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-url' => Url::home().'master/identitas-sosial/edit-pendidikan?id=',
                                'id' => 'btn-edit-pendidikan',
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                                'id' => 'btn-delete-pendidikan',
                                'data-target' => Url::home().'master/identitas-sosial/delete-pendidikan?id=',
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => Url::home().'master/identitas-sosial/export-pdf-pendidikan?',
                                'id' => 'btn-pdf-pendidikan',
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => Url::home().'master/identitas-sosial/export-excel-pendidikan?',
                                'id' => 'btn-excel-pendidikan',
                            ]
                        ],
                    ],'#data-pendidikan');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-pendidikan"></div>
                </div>
                <table id="data-pendidikan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                       <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Urutan Pendidikan");?></th>
                            <th><?=\Yii::t("fe", "Nama Pendidikan");?></th>
                            <th><?=\Yii::t("fe", "Nama Lainnya");?></th>
                            <th><?=\Yii::t("fe", "Indexing");?></th>
                            <th><?=\Yii::t("fe", "Status");?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="5">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script>
    var tablePendidikan;

    $(document).ready(function() {
        tablePendidikan = $('#data-pendidikan').docoTabel({
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
            sorting: [[2, 'asc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            // scrollX: true,
            ajax: baseUrl+"master/identitas-sosial/get-data-pendidikan",
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
                {title: "<?=Yii::t('fe', 'Urutan Pendidikan')?>",  data: "pendidikan_urutan", searchable: false },
                {title: "<?=Yii::t('fe', 'Nama Pendidikan')?>",  data: "pendidikan_nama"},
                {title: "<?=Yii::t('fe', 'Nama Lainnya')?>", data: "pendidikan_namalainnya"},
                {title: "<?=Yii::t('fe', 'Indexing')?>", data: "indexing_nama", visible: false, searchable: false},
                {title: "Status",  data: "status"},
            ]
        });
        $(".dataTables_filter").hide();
        $(".filter-form-pendidikan").datatableBootstrapFilter(tablePendidikan,
            [
                [3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('pendidikan_nama', '', ['class' => 'form-control','placeholder'=>'Nama Pendidikan'])));?>'],
                [4, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('pendidikan_namalainnya', '', ['class' => 'form-control','placeholder'=>'Nama Lainnya'])));?>'],
                //[5, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('indexing_nama', '', $dataIndexing, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>'],
                [6, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('is_active', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>']
            ]
        );

        var primaryKey;

        $("#data-pendidikan tbody").on("click", "tr", function(){
            try {
                primaryKey = tablePendidikan.row(".selected").data().primary ? tablePendidikan.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $("#btn-edit-pendidikan").attr("action",$("#btn-edit-pendidikan").data("target")+primaryKey);
                $("#btn-delete-pendidikan").attr("action",$("#btn-delete-pendidikan").data("target")+primaryKey);
            } else {
                $("#btn-edit-pendidikan").removeAttr("action");
                $("#btn-delete-pendidikan").removeAttr("action");
            }
        });

        // Event Delete
        $(document).on("click", ".data-delete-pendidikan", function(e) {
            e.preventDefault();
            $(this).docoForm("delete",{
                success : function (data) {
                    tablePendidikan.draw()
                }
            });
            return false;
        });

        $(document).ready(function () {
            $(document).on("click", ".data-reset", function() {
                tablePendidikan.draw();
            });
        });
    });


</script>