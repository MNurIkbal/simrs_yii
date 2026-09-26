<?php
// Author : Naufal Ziyad L
// Modified By: Ardi Pratama

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Pekerjaan');
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
                                'data-parent'=>'.filter-form-pekerjaan'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'id' => 'btn-add-pekerjaan',
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => Url::home().'master/identitas-sosial/create-pekerjaan',
                            ]
                        ],
                        'edit' => [
                            'attributes' => [
                                'data-url' => Url::home().'master/identitas-sosial/edit-pekerjaan?id=',
                                'id' => 'btn-edit-pekerjaan',
                                'data-options' => 'modal',
                                'data-target' => '#modal_backdrop',
                            ]
                        ],
                        'delete' => [
                            'attributes' => [
                                'data-additional' => 'data-rm',
                                'id' => 'btn-delete-pekerjaan',
                                'data-target' => Url::home().'master/identitas-sosial/delete-pekerjaan?id=',
                            ]
                        ],
                        'pdf' => [
                            'attributes' => [
                                'data-target' => Url::home().'master/identitas-sosial/export-pdf-pekerjaan?',
                                'id' => 'btn-pdf-pekerjaan',
                            ]
                        ],
                        'excel' => [
                            'attributes' => [
                                'data-target' => Url::home().'master/identitas-sosial/export-excel-pekerjaan?',
                                'id' => 'btn-excel-pekerjaan',
                            ]
                        ],
                    ],'#table-pekerjaan');?>
            </div>

            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form-pekerjaan"></div>
                </div>
                <table id="table-pekerjaan" class="table table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th></th>
                            <th width="1">No</th>
                            <th><?=\Yii::t("fe", "Nama Pekerjaan");?></th>
                            <th><?=\Yii::t("fe", "Nama Lainnya");?></th>
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
    var tablePekerjaan;

    $(document).ready(function() {

        tablePekerjaan = $('#table-pekerjaan').docoTabel({
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
                ajax: baseUrl+"master/identitas-sosial/get-data-pekerjaan",
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
                    {title: "<?=Yii::t('fe', 'Nama Pekerjaan')?>",  data: "pekerjaan_nama"},
                    {title: "<?=Yii::t('fe', 'Nama Lainnya')?>", data: "pekerjaan_namalainnya"},
                    {title: "Status",  data: "status"},
                ]
            });
         $(".dataTables_filter").hide();
         $(".filter-form-pekerjaan").datatableBootstrapFilter(tablePekerjaan,
                [
                    [2, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('pekerjaan_nama', '', ['class' => 'form-control','placeholder'=>'Nama Pekerjaan'])));?>'],
                    [3, '<?=(preg_replace("/[\n\t\r]/i", '', Html::textInput('pekerjaan_namalainnya', '', ['class' => 'form-control','placeholder'=>'Nama Lainnya'])));?>'],
                    [4, '<?=(preg_replace("/[\n\t\r]/i", '', Html::dropDownList('status', '', $status, ['class' => 'select2', 'prompt' => \Yii::t('fe', 'Pilih')])));?>']
                ]
            );

        $("#data-pendidikan tbody").on("click", "tr", function(){
            try {
                primaryKey = tablePekerjaan.row(".selected").data().primary ? tablePekerjaan.row(".selected").data().primary : null;
            } catch (e) {
                primaryKey = false;
            }

            if (primaryKey) {
                $("#btn-edit-pendidikan").attr("action",$("#btn-edit-pendidikan").data("url")+primaryKey);
                $("#btn-delete-pendidikan").attr("action",$("#btn-delete-pendidikan").data("target")+primaryKey);
            } else {
                $("#btn-edit-pendidikan").removeAttr("action");
                $("#btn-delete-pendidikan").removeAttr("action");
            }
        });

        // Event Delete
        $(document).on("click", ".data-delete-pekerjaan", function(e) {
            e.preventDefault();
            $(this).docoForm("delete",{
                success : function (data) {
                    tablePekerjaan.draw()
                }
            });
            return false;
        });

        // Event Reload
        $(document).on("click", ".data-reload", function() {
            tablePekerjaan.draw();
        });
    });
</script>
