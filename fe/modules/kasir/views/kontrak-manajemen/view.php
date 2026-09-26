<?php

/**
 * @Author: Rizqi Fitrianto
 * @Date:   2018-03-09 11:00:02
 * @Last Modified by:   Ragnar-Lothbroc
 * @Last Modified time: 2019-01-03 17:12:58
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use kartik\form\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::$app->docoVars->workspace("modul_alias"), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
    .datepicker>div{
        display:block;
    }
    p.barang_merk {
        font-size: 15px;
        padding-left: 5px;
        font-weight: 500;
    }
    .panel-heading{
        margin-bottom: 10px;
    }
    legend {
        font-weight: bold !important;
        padding: 5px;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= $this->title; ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=
                    DocoHelpers::generateToolbar([
                        'back',
                    ]);
                ?>
            </div>

            <div class="panel-body">
                <div>

                </div>

                <div class="col-md-12">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h6 class="panel-title"><b><?= Yii::t('fe','Form').' '.$this->title ?></b></h6>
                        </div>

                        <div class="panel-body">
                        <div class="panel panel-heading p-17">
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="text-left control-label col-sm-2">
                                            <strong><?= Yii::t("fe", "Nama Penjamin") ?></strong>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="col-sm-1" style="width: 2%;"><?= Yii::t("fe", " : ") ?></div>
                                            <div class="col-sm-11">
                                                <?= $penjamin_nama ?>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="text-left control-label col-sm-2">
                                            <strong><?= Yii::t("fe", "Nomor Kontrak") ?></strong>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="col-sm-1" style="width: 2%;"><?= Yii::t("fe", " : ") ?></div>
                                            <div class="col-sm-11">
                                                <?= $no_kontrak ?>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="text-left control-label col-sm-2">
                                            <strong><?= Yii::t("fe", "Nama Kontrak") ?></strong>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="col-sm-1" style="width: 2%;"><?= Yii::t("fe", " : ") ?></div>
                                            <div class="col-sm-11">
                                                <?= $nama_kontrak ?>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="text-left control-label col-sm-2">
                                            <strong><?= Yii::t("fe", "Tanggal Mulai") ?></strong>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="col-sm-1" style="width: 2%;"><?= Yii::t("fe", " : ") ?></div>
                                            <div class="col-sm-11">
                                                <?= $tgl_mulai ?>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                                <div class="row">
                                    <div class="col-md-9">
                                        <div class="text-left control-label col-sm-2">
                                            <strong><?= Yii::t("fe", "Tanggal Selesai") ?></strong>
                                        </div>
                                        <div class="col-sm-10">
                                            <div class="col-sm-1" style="width: 2%;"><?= Yii::t("fe", " : ") ?></div>
                                            <div class="col-sm-11">
                                                <?= $tgl_selesai ?>
                                            </div>
                                        </div>
                                    </div>                                    
                                </div>
                            </div>
                        </div>
                            <!-- Tabel Grades -->
                            <table id="GradePenjamin" class="table table-striped table-condensed table-hover" style="width:100%">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th>No</th>
                                        <th><?=\Yii::t("fe", "Grade");?></th>
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
                </div>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs('
    var tableGradePenjamin;
    var groupColumn = 1;
    var kontrak_id = "'.$kontrak_id.'";
    tableGradePenjamin = $("#GradePenjamin").docoTabel({
        filter: false,
        displayLength: 10,
        processing: true,
        serverSide: true,
        lengthChange: false,
        paging: false,
        columnDefs: [
            { visible: false, targets: groupColumn }
        ],
        order: [[groupColumn, "asc"]],
        ajax: baseUrl+"kasir/kontrak-manajemen/get-list-grade?kontrak_id=" + kontrak_id,
        columns: [
            {
                title: "No",
                data: "rowNum",
                searchable: false,
                orderable: false
            },
            {
                title: "Grade", 
                data: "grade",
            },
            {
                title: "LOB", 
                data: "lookup_name",
                orderable: false,
            },
            {
                title: "Tipe Diskon",
                data: "tipediskon_nama",
                searchable: false,
                orderable: false,
            },
        ],
        drawCallback: function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:"current"} ).nodes();
            var last=null;
 
            api.column(groupColumn, {page:"current"} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        `<tr class="group" style="background-color:#d9d9d9"><td colspan="5" style="font-weight:bold">GRADE : ${group}</td></tr>`
                    );
 
                    last = group;
                }
            } );
        }
    });

', View::POS_END, 'b-index');
?>