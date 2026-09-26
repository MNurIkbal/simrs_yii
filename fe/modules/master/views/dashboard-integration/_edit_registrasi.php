<?php

use yii\helpers\Html;
use yii\helpers\Url;
use app\components\DocoHelpers;
use yii\widgets\Breadcrumbs;
use yii\web\View;

$this->title = Yii::t('fe', 'Edit Registrasi');
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
                                'data-parent'=>'.filter-edit-registrasi',
                            ]
                        ],
                        'resend'=> [
                            'title' => Yii::t('fe', 'Resync'),
                            'icon' => 'fa fa-paper-plane',
                            'attributes' => [
                                'id' => 'resend-edit-registrasi',
                                'data-options' => 'click',
                            ]
                        ],
                    ], '#table-edit-registrasi');?>    
            </div>
            <div class="panel-body">
                <div class="tab-edit-registrasi">
                </div>
                <table id="table-edit-registrasi" class="table-striped table-condensed table-hover" style="width:100%">
                    <thead>
                        <tr class="bg-inverse">
                            <th class="text-center" width="1px"><?= Yii::t('fe', 'Resend All'); ?><br>
                                <?= Html::checkbox('select_all', 0, ['class' => 'resend-all-dp']); ?></th>
                            <th>No</th>
                            <th><?=\Yii::t("fe", "Detail");?></th>
                            <th><?=Yii::t('fe', 'Tanggal Transaksi')?></th>
                            <th><?=Yii::t('fe', 'No Registrasi')?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center" colspan="6">Data tidak ditemukan.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs('
    var tableEditRegistrasi;
    $(function() {
        generateFilter("tab-edit-registrasi", "filter-edit-registrasi");
        tableEditRegistrasi = $("#table-edit-registrasi").docoTabel({
            filter: true,
            sorting: [[3, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"master/dashboard-integration/get-data?type=edit_regis&state=edit",
            columns: [
                {
                    data: "select_item",
                    searchable: false,
                    orderable: false,
                    className: "text-center",
                },
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false,
                    width:"5%"
                },
                {
                    title: "'.(\Yii::t("fe", "Detail")).'", 
                    data: "detail", 
                    searchable: false,
                    orderable: false
                },
                {
                    title: "'.(\Yii::t("fe", "Tanggal Transaksi")).'", 
                    data: "tgl_pendaftaran",
                },
                {
                    title: "'.(\Yii::t("fe", "No Registrasi")).'", 
                    data: "no_pendaftaran", 
                },
            ],
        });

        $(".dataTables_filter").hide();
        $(".filter-edit-registrasi").datatableBootstrapFilter(tableEditRegistrasi, [
            [
                3,
                \'<div class="input-group"><input type="text" id="rangeDemoStartDp" value="'.date('d-M-Y').'" class="form-control startDate"/><span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span><input type="text" id="rangeDemoFinishDp" value="'.date('d-M-Y').'" readonly="true" class="form-control endDate"/><input type="text" style="display:none" class="targetDate" col-index=3></div>\'
            ],
        ], {
            3:0,
            4:1
        }, true);

        dateRangeHelper(".startDate",".endDate",".targetDate");
        $(".resend-all-dp").on("click", function(){
            var rows = tableEditRegistrasi.rows({"search" : "applied"}).nodes();
            if($(".select_item", rows).prop("disabled") == false) {
                $(".select_item", rows).prop("checked", this.checked);
            }
        });

        $("#resend-edit-registrasi").on("click", function(){
            var _data = [];
            $(".select_item").each(function(){
                if(this.checked == true) {
                    _data.push($(this).val());
                }
            });

            if(_data.length === 0) {
                docoNotification("error", i18next.t("Proses Gagal"), i18next.t("Tidak Ada Data yang dapat di Resync"));
            }
            else {
                var _dataPost = {
                    id: _data,
                }
                $.ajax({
                    method: "POST",
                    data: _dataPost,
                    url: baseUrl+"master/dashboard-integration/resync?model=edit_regis&state=edit",
                    success: function(res) {
                        tableEditRegistrasi.draw();
                    },
                    error: function(data) {
                        docoNotification("error", i18next.t("Proses Gagal"), i18next.t(data.response.message));
                    }
                });
            }
        });
    });

', View::POS_END, 'e-index');
?>