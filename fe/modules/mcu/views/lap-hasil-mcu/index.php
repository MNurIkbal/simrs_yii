<?php
 
use yii\web\View;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DatePicker;

$this->title = Yii::t('fe', $title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', Yii::$app->docoVars->workspace("instalasi_name")), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

?>
<style type="text/css">
    .zIndex { z-index: 1;  }
    .fc th {
        padding: 12px 8px !important;
    }
    .fc-event {
        position : absolute !important;
    }
    .fc-resource-area {
        width : 200px;
    }
    .fc-head {
        background-color : #606060;
        border-color : #606060;
        color : #fff;
        font-size : 12px;
    }
    .fc-content {
        overflow: inherit !important;
        white-space: normal !important;
        font-size : 12px !important;
        font-weight: bold;
    }
    .fc-timeline-event {
        word-break: break-word;
    }
    .datepicker>div{
        display:block;
    }
</style>

<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias"); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
            <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset'  
                ], '#example'); ?>
            </div>
            <div class="panel-body">
                 <div class="advanced-filter"></div>
                <table id="example" class="table table-striped table-condensed table-hover" style="width:100%;">
                    <thead>
                        <tr class="bg-inverse"> 
                            <th width="1">No</th> 
                            <th><?=\Yii::t("fe", "Tanggal Daftar");?></th> 
                            <th><?=\Yii::t("fe", "Instansi");?></th> 
                            <th><?=\Yii::t("fe", "Jumlah Peserta");?></th> 
                            <th><?=\Yii::t("fe", "Aksi");?></th>  
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
<?php 
    $this->registerJs('  
    var table;
    $(document).ready(function() {  
        table = $("#example").docoTabel({
            select: {
                style: "os",
                selector: "tr"
            },
            filter: true,
            sorting: [[2, "desc"]],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: baseUrl+"mcu/lap-hasil-mcu/get-data", 
            columns: [ 
                {
                    title: "No",
                    data: "rowNum",
                    searchable: false,
                    orderable: false
                }, 
                {title: "'.(\Yii::t("fe", "Tanggal Daftar")).'", data: "tgl_pendaftaran"}, 
                {title: "'.(\Yii::t("fe", "Instansi")).'", data: "instansi"}, 
                {title: "'.(\Yii::t("fe", "Jumlah Peserta ")).'", data: "jumlah_peserta", searchable: false}, 
                {title: "'.(\Yii::t("fe", "Aksi")).'", data:"aksi",  searchable: false},  
            ], 
            rowCallback: function (row, data) {
                
            }
        });
        $(".dataTables_filter").hide();
        
        $(".filter-form").datatableBootstrapFilter(table,
        [
                [1,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            DatePicker::widget([
                                'name' => 'tgl_pendaftaran',
                                'type' => 2,
                                'language' => 'en',
                                'value' => date('d-M-Y'),
                                'id' => 'tgl_pendaftaran_picker',
                                'readonly' => true,
                                'options' => ['placeholder' => date('d-M-Y')],
                                'pluginOptions' => [
                                    'autoclose' => true,
                                    'format' => 'dd-M-yyyy',
                                ]
                            ]) 
                        )
                    ).'\'
                ], 
                [
                2,
                    \''.(
                        preg_replace(
                            "/[\n\t\r]/i",
                            '',
                            Html::dropDownList(
                                'instansi',
                                '',
                                $instansi,
                                [
                                    'class' => 'form-control select2',
                                    'id' => 'instansi',
                                    'prompt' => \Yii::t('fe', '--pilih Instansi--')
                                ]
                            )
                        )
                    ).'\'
                ] 
            ]
        );
    });  
    ', VIEW::POS_END, 'b-index');
?>