<?php

/**
 * @Author: rizqi_fitrianto
 * @Date:   2018-08-07 11:03:58
 * @Last Modified by:   rizqi_fitrianto
 * @Last Modified time: 2018-08-07 17:51:59
 */

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use kartik\widgets\DepDrop;
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
<style>
    .my-legend .legend-title {
    text-align: left;
    margin-bottom: 8px;
    font-weight: bold;
    font-size: 90%;
    }
  .my-legend .legend-scale ul {
    margin: 0;
    padding: 0;
    float: left;
    list-style: none;
    }
  .my-legend .legend-scale ul li {
    display: block;
    float: left;
    width: 50px;
    margin-bottom: 6px;
    margin-right: 5px;
    text-align: center;
    font-size: 80%;
    list-style: none;
    }
  .my-legend ul.legend-labels li span {
    display: block;
    float: left;
    height: 15px;
    width: 50px;
    border: solid 0.2px;
    }
  .my-legend .legend-source {
    font-size: 70%;
    color: #999;
    clear: both;
    }
  .my-legend a {
    color: #777;
    }

    .square-sukses {
    height: 30px;
    width: 120px;
    background-color: #26A65B;
    color:#ffffff;
    padding: 5px 0 5px 10px;
    margin-right:20px;
    }
    .square-batal {
    height: 30px;
    width: 70px;
    background-color: #D24D57;
    color:#ffffff;
    padding: 5px 0 5px 10px;
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
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form">
                        <form class="advancedFilter" onsubmit="return false;">
                            <div class="row" id="ffBody">
                                <div class="form-group col-md-3">
                                    <label>Tanggal Operasi :</label>
                                    <?=
                                        DatePicker::widget([
                                            'name' => 'tanggal_operasi',
                                            'language' => 'en',
                                            'value' => date('d-M-Y'),
                                            'readonly' => true,
                                            'pluginOptions' => [
                                                'autoclose' => true,
                                                'format' => 'dd-M-yyyy',
                                            ]
                                        ]);
                                    ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Ruangan Bedah Sentral :</label>
                                    <?= 
                                        Html::dropDownList('ruangan_id', null, 
                                            ArrayHelper::map($list_ruangan,'ruangan_id','ruangan_nama'), [
                                         'class' => 'select2',
                                         'prompt' => Yii::t('fe','--Pilih--')
                                        ]) 
                                    ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Nomor Request :</label>
                                    <?= 
                                        Html::textInput('nomor_request', null, [
                                         'class' => 'form-control',
                                        ]) 
                                    ?>
                                    <?php
                                        // Select2::widget([
                                        //     'name' => 'nomor_request',
                                        //     'options' => ['placeholder' => ''],
                                        //     'pluginOptions' => [
                                        //         'allowClear' => true,
                                        //         'minimumInputLength' => 3,
                                        //         'ajax' => [
                                        //             'url' => Url::home() . (Yii::$app->controller->module->id) . 
                                        //             '/jadwal-operasi/get-no-request',
                                        //             'dataType' => 'json',
                                        //             'data' => new JsExpression('function(params) { 
                                        //                 var _tanggal = $(\'input[name=tanggal_operasi]\').val();
                                        //                 return {
                                        //                     q:params.term,
                                        //                     tanggal: _tanggal
                                        //                 }; 
                                        //             }')
                                        //         ],
                                        //     ],
                                        // ]);
                                    ?>
                                </div>
                                <div class="form-group col-md-3">
                                    <label>Dokter Operator :</label>
                                    <?= 
                                        Select2::widget([
                                            'name' => 'dokter_id',
                                            'options' => ['placeholder' => ''],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'minimumInputLength' => 3,
                                                'ajax' => [
                                                    'url' => Url::home() . (Yii::$app->controller->module->id) . 
                                                    '/jadwal-operasi/get-dokter-operator',
                                                    'dataType' => 'json',
                                                    'data' => new JsExpression('function(params) { 
                                                        return {
                                                            q:params.term,
                                                        }; 
                                                    }')
                                                ],
                                            ],
                                        ]);
                                    ?>

                                </div>
                            </div>
                        </form>
                        <hr>
                       <div class="col-md-12">
                            <div class='my-legend'>
                                <div class='legend-title'>Keterangan</div>
                                <div class='legend-scale'>
                                <ul class='legend-labels'>
                                    <li><span style='background:rgb(74, 238, 142);'></span>SUDAH DI SETUJUI</li>
                                    <li><span style='background:rgb(255, 15, 33);'></span>DITOLAK</li>
                                    <li><span style='background:#FFFfff;'></span>BELUM DISETUJUI</li>
                                    <li><span style='background:rgb(255, 137, 0);'></span>CYTO</li>
                                    <li><span style='background:rgb(204, 204, 204);'></span>BATAL</li>
                                    <li><span style='width:60px;background:yellow;'></span>RESCHEDULE</li>
                                </ul>
                            </div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                        <br>
                    </div>
                </div>
                <div class="text-center" id="loading-kalender">
                    <h3>
                        <i class="icon-spinner4 spinner position-center"></i>&nbsp;&nbsp;<b>Loading . . . </b>
                    </h3>
                </div>
                <div id="calendar">
                </div>
            </div>
        </div>
    </div>
</div>
<?php 
    
    $this->registerJs("
    var _events = [];
    var _resourse = [];

    $(document).on('click','.data-filter',function (event) {
        event.preventDefault();
        $.ajax({
            data : $('.advancedFilter').serializeArray(),
            url : '/bedah/jadwal-operasi/get-data',
            dataType : 'json',
            type : 'POST',
            beforeSend: function() {
                $('#calendar').hide();
                $('#loading-kalender').show();
                $('[data-popup=\"popover\"]').popover('destroy')
            },
            success : function (data) {
                _resourse = data.ruangan;
                _events = data.data_jadwal;

                $('#loading-kalender').hide();
                $('#calendar').show();
                $('#calendar').fullCalendar('refetchEvents');
                $('#calendar').fullCalendar( 'refetchResources');
            },
            error : function (data) {
                return false;
            }
        }).done(function () {
            $('#loading-kalender').hide();
            $('#calendar').show();
        });
    });

    $(document).on('click','.data-reset', function (event) {
        event.preventDefault();
        var _tanggal = $('input[name=tanggal_operasi]').val();
        docoResetForm($('.advancedFilter'));
        $('input[name=tanggal_operasi]').val('". date('d-M-Y') ."');
        $('.data-filter').trigger('click');
    });

    $(document).ready(function() {
        $('#calendar').fullCalendar({
            schedulerLicenseKey: 'nesimrs',
            refetchResourcesOnNavigate : true,
            defaultView: 'timelineDay',
            locale : 'id',
            height: 650,
            defaultEventMinutes: 30,
            timeFormat : 'H:mm',
            editable: false,
            droppable: false,
            aspectRatio: 2,
            scrollTime: '00:00',
            header: {
                right: 'timelineDay'
            },
            resourceLabelText: 'Ruangan / Jam',
            eventClick: function(event) {
            },
            resources: function (callback) {
                callback(_resourse);
            },
            events: function (start, end, tz, callback) {
                callback(_events);
            },
            eventRender: function(event, element) {
                $(element).attr('data-dadang',event.dadang);
                $(element).attr('data-popup','popover');
                $(element).attr('data-trigger','focus');
                $(element).attr('tabindex', '0');
                var _html = '<div class=\"\"><label class=\"\"><b>Estimasi Jam Mulai : '+ event.jam_rencana_mulai +'</b></label></div>';
                    _html += '<div class=\"\"><label class=\"\"><b>Estimasi Jam Selesai : '+ event.jam_rencana_selesai +'</b></label></div>';
                    _html += '<div class=\"\"><label class=\"\"><b>Dokter Operator : '+ event.nama_dokter +'</b></label></div>';
                    _html += '<div class=\"\"><label class=\"\"><b>Jam Mulai : '+ event.mulai_operasi +'</b></label></div>';
                    _html += '<div class=\"\"><label class=\"\"><b>Jam Selesai : '+ event.selesai_operasi +'</b></label></div>';
 
                if (event.status == 471){
                    if (event.kamar == null){
                        _html += '<div class=\"\"><label class=\"\"><b>Kamar : - </b></label></div>';
                    }else{
                        _html += '<div class=\"\"><label class=\"\"><b>Kamar : '+ event.kamar +'</b></label></div>';
                    }
                }
                _html += '<div class=\"\"><label class=\"\"><b>Catatan Klinis : '+ event.catatan_klinis +'</b></label></div>';
                _html += '<div class=\"\"><label class=\"\"><b>Pemakaian Implant : '+ event.pemakaian_implant +'</b></label></div>';
                _html += '<div class=\"\"><label class=\"\"><b>Sewa Alat RS : '+ event.sewa_alat_rs +'</b></label></div>';
                _html += '<div class=\"\"><label class=\"\"><b>Sewa Vendor : '+ event.sewa_vendor +'</b></label></div>';
                if(event.operasi_cyto == 0 && event.operasi_elektif == 0 && event.operasi_odc == 0){
                    _html += '<div class=\"\"><label class=\"\"><b>Jenis Operasi : '+ event.kegiatan_operasi_nama +' </b></label></div>';
                }
                else{
                    _html += '<div class=\"\"><label class=\"\"><b>Jenis Operasi : '+ event.kegiatan_operasi_nama +'</b></label></div>';
                }
                
                if(event.operasi_cyto == 1){
                    _html += '<div class=\"\"><label class=\"\"><b>- Operasi Cyto  </b></label></div>';
                }
                if(event.operasi_elektif == 1){
                    _html += '<div class=\"\"><label class=\"\"><b>- Operasi Elektif </b></label></div>';
                }
                if(event.operasi_odc == 1){
                    _html += '<div class=\"\"><label style = \"color:rgb(0 82 230)\" class=\"\"><b>- Operasi ODC </b></label></div>';
                }
                    
                // if (event.status == 470) {
                    _html += '<hr><div class=\"text-center\">';
                    _html += '<a href=\"/bedah/jadwal-operasi/view?id='+ event.id +'\"><button style=\"width: 100%\" class=\"btn btn-info btn-labeled btn-lg\" type=\"button\">';
                    _html += '<b><i class=\"fa fa-eye\"></i></b>Lihat Detail Operasi</button></a></div>';
                // }
               
                // else if(event.status == 692) {
                //    _html += '<hr><div class=\"text-center\">';
                //    _html += '<button class=\"btn btn-danger btn-sm\" data-toggle=\"modal\" data-target=\"#modal_backdrop\" action=\"/bedah/jadwal-operasi/batal?id='+ event.id +'\" data-width=\"50%\">';
                //    _html += '<b><i class=\"fa fa-times\"></i> Batal </b></button>';
                //    _html += '&nbsp;&nbsp;';
                //    _html += '<button class=\"btn btn-success btn-sm\" data-toggle=\"modal\" data-target=\"#modal_backdrop\" action=\"/bedah/jadwal-operasi/reschedule?id='+ event.id +'\" data-width=\"75%\">';
                //    _html += '<b><i class=\"fa fa-calendar\"></i> Reschedule </b></button></div>';
                //    _html += '<hr><div class=\"text-center\">';
                //     _html += '<a href=\"/bedah/jadwal-operasi/view?id='+ event.id +'\"><button style=\"width: 100%\" class=\"btn btn-info btn-labeled btn-lg\" type=\"button\">';
                //     _html += '<b><i class=\"fa fa-eye\"></i></b>Lihat Detail Operasi</button></a></div>';

                // }
                // else if(event.status == 692) {
                    // _html += '<hr><div class=\"text-center\">';
                    // _html += '<a href=\"/bedah/jadwal-operasi/view?id='+ event.id +'\"><button style=\"width: 100%\" class=\"btn btn-info btn-labeled btn-lg\" type=\"button\">';
                    // _html += '<b><i class=\"fa fa-eye\"></i></b>Lihat Detail Operasi</button></a></div>';
                //  }
                
                $(element).attr('data-content',_html);
                $(element).attr('data-original-title',event.label);
                $(element).attr('data-placement','bottom');
                element.popover({
                    trigger: 'click',
                    container:'body',
                    title: '<div class=\"label label-lg label-primary col-xs-12\">Informasi Pasien</div>',
                    html: true,
                    template: '<div class=\"popover border-teal-400\"><div class=\"arrow\"></div><h3 class=\"popover-title bg-teal-400\"></h3><div class=\"popover-content\"></div></div>'
                });

            },
            loading : function (isLoading, view) {

            }
        });
        
     

        $('.fc-toolbar, .fc-license-message').hide();
        $('.data-filter').trigger('click');
        $('.fc-view-container').on('click', function (e) {
            if ($(e.target).find('.fc-event-container').length) {
                $('.fc-timeline-event').each(function() {
                    var _attrPop = $(this).attr('aria-describedby')
                    if (typeof _attrPop !== 'undefined') {
                        $(this).trigger('click')
                    } 
                })
            }
        })
    });

    $(document).ready(function () {
        // var regex = /id=\"([^<]*)</g;
        // var matches = regex.exec(_html);
        // var readmore = matches[1];
        // var lessmore = readmore.substr(0, 70);
        // var bas = event.catatan_klinis;
        // var lessmore = bas.substr(0, 50);
        // console.log(bas);
        // console.log(bas.length);
        // if (bas.length > 50) {
                
        //   $('.ken').hide();

        
        //     } else {
        //         var a = 30;
        //         $('.baru').html(readmore);
        //     }
        // var readmore = $('#c_klinis').html();
        // var lessmore = readmore.substr(0, 112);
        // if (readmore.length > 150) {
    
        //     $('#c_klinis').html(lessmore).append('<a href=\"\" class=\"read-more-link\">Tampilkan Lebih ...</a>');
    
        // } else {
        //     $('#c_klinis').html(readmore);
        // }
    
        // $('body').on('click', \".read-more-link\", function (event) {
        //     event.preventDefault();
        //     $(this).parent('#c_klinis').html(readmore).append('<a href=\"\" class=\"show-less-link\" >Sembunyinkan ...</a>');
        // });
        // $(\"body\").on(\"click\", \".show-less-link\", function (event) {
        //     event.preventDefault();
        //     $(this).parent('#c_klinis').html(readmore.substr(0, 112)).append('<a href=\'\' class=\'read-more-link\' >Tampilkan Lebih ...</a>');
        // });

    });

    ", VIEW::POS_END, 'js-kunings');
?>