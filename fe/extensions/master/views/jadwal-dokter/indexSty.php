<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;

$this->title = Yii::t('fe', 'Jadwal dokter');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Master'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<!-- <style lang="css">
    .show-info:before {
        content:"";
        position: absolute;
        right: 11px;
        top: -10px;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 10px 10px 10px;
        border-color: transparent transparent #F2F2F2 transparent;
        z-index:9999;
    }
    .show-info:after {
        content:"";
        position: absolute;
        right: 4px;
        top: -22px;
        width: 0;
        height: 0;
        border-style: solid;
        border-width: 0 17px 17px 17px;
        border-color: transparent transparent #fffefe transparent;
        z-index:9998;
    }
    .show-info {
        display: none;
        background: #F2F2F2;
        border: 8px solid #fffefe;
        box-shadow: 0 3px 3px rgba(0, 0, 0, 0.2);
        float: left;
        position: absolute;
        color: black;
        padding: 5px;
        margin: 0;
        top: 2.9em;
        line-height: 1.8em;
        width: 210px;
        z-index: 999999;
    }

    .btn-jadwal {
        margin: 1px;
    }
</style> -->

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
.datepicker>div{
    display:block;
}
.fc-divider {
    width : 10px;
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

    .btn-ukuran{
    height: 30px !important;
    width: 120px !important;
    }
</style>


<div class="row">
    <div class="col-md-12">
        <div class="panel panel-white" style="margin-top: 0px !important;">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                    <div class="column-1">
                        <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                    </div>
                    <div class="column-2">
                        <h3 class="panel-title"><b><?= Yii::$app->docoVars->workspace("modul_alias",$this->title); ?></b></h3>
                        <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                    </div>
                </div>
                <!-- end -->
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
            </div>
            <div class="panel-toolbar clearfix">
                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset'=>['attributes'=>['data-parent'=>'.filter-form']],
                    'excel2' => [
                        'type' => 'button',
                        'title' => \Yii::t('fe', 'Unduh excel'),
                        'icon' => 'fa fa-file-excel-o',
                        'method' => 'not exist',
                        'attributes' => [
                            'class' => 'data-excel2',
                            'data-options' => 'click',
                        ] 
                    ],
                    'add' => [
                        'attributes' => [
                            'data-toggle' => 'modal',
                            'data-target' => '#modal_backdrop',
                            'action' => '/master/jadwal-dokter/create',
                        ]
                    ],
                ]);?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?php
                        echo Html::beginForm(null,'POST',[
                                'class' => 'advancedFilter',
                            ]);
                    ?>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label><?= Yii::t('fe', 'Ruangan') ; ?> :</label>
                                <?php
                                echo Html::dropDownList('ruangan_id',null,$listRuangan,[
                                    'class' => 'select2',
                                    'id'=>'ruangan_id',
                                    'prompt'=>Yii::t('fe', '--Pilih--'),
                                ]);
                                ?>
                            </div>
                            <div class="col-md-3">
                                <label><?= Yii::t('fe', 'Dokter'); ?> :</label>
                                <?php
                                echo DepDrop::widget(
                                    [
                                        'name'=>'dokter_id',
                                        'options'=>[
                                            'id'=>'dokter_id',
                                            'class'=>'select2',
                                        ],
                                        'pluginOptions'=>[
                                            'depends'=>['ruangan_id'],
                                            'placeholder'=>\Yii::t('fe', '--Pilih--'),
                                            'url'=>Url::to(['/master/jadwal-dokter/dep-list-dokter-ruangan'])
                                        ]
                                    ]
                                );
                                ?>
                            </div>
                            <div class="col-md-3">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <div class="col-md-3">
                                <label><?= Yii::t('fe', 'Hari'); ?> :</label>
                                <?php
                                    echo Html::dropDownList('hari','',$listHari,[
                                        'class' => 'select2',
                                        'id'=>'hari',
                                        'prompt'=>Yii::t('fe', '--Pilih--'),
                                    ]);
                                ?>
                            </div>
                            <div class="col-md-3">
                                <?= Html::label(Yii::t('fe', 'Jam Mulai:')) ?>
                                <?php
                                    echo Html::textInput('jam_mulai','',[
                                        'class' => 'form-control pickatime',
                                        'id' => 'jam_mulai',
                                        'placeholder' => 'Jam Mulai'
                                    ]);
                                ?>
                            </div>
                            <div class="col-md-3">
                                <?= Html::label(Yii::t('fe', 'Jam Selesai:')) ?>
                                <?php
                                    echo Html::textInput('jam_selesai','',[
                                        'class' => 'form-control pickatime',
                                        'id' => 'jam_selesai',
                                        'placeholder' => 'Jam Selesai'
                                    ]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php
                    echo Html::endForm();
                    ?>
                    </div>

                    <!-- <br> -->

                    <!-- table content -->
                   <!--  <div class='col-md-offset-9 col-md-3'>
                        <table>
                            <tr>
                                <td>Legend : &nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td class='bg-info'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td>&nbsp;&nbsp; Aktif &nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td class='bg-light'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
                                <td>&nbsp;&nbsp; Tidak aktif &nbsp;&nbsp;&nbsp;&nbsp;</td>
                            </tr>
                        </table>
                    </div> -->
                    <!-- <div class="table-responsive pre-scrollable" style="padding-left:4px; padding-right:4px; min-height:400px; overflow:auto;">
                        <table
                                class="table table-xs table-bordered"
                                spam="datatable-basic table-striped table-hover dataTable no-footer"
                                id="data-jadwaldokter"
                                style="width: 100%;"
                                >
                            <thead>
                                <tr>
                                    <th>Poliklinik</th>
                                    <th>Dokter</th>
                                    <th>Hari</th>
                                    <?php foreach ($listJam as $jam): ?>
                                        <th style='padding-left:3px; padding-right:3px;'>
                                            <?= $jam; ?>
                                        </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody class='list-jadwal-dokter'>
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td colspan=29><?= Yii::t('fe', 'Data tidak ditemukan'); ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div> -->
                <!-- </div> -->
                <br>

                <!-- todo -->
                <div id="calendar">
                </div>

            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs("
    $('.pickatime').pickatime({
        format: 'HH:i'
    });
    ", View::POS_READY, 'time-handler');
$this->registerJs($this->render('js/index.js'));

$this->registerJs("
    var _events = [];
    var _resourse = [];

    $(document).on('click','.data-filter',function (event) {
        event.preventDefault();
        $.ajax({
            data : $('.advancedFilter').serializeArray(),
            url : '/master/jadwal-dokter/get-data',
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

    // $(document).on('click','.data-reset', function (event) {
    //     event.preventDefault();
    //     var _tanggal = $('input[name=tanggal_operasi]').val();
    //     docoResetForm($('.advancedFilter'));
    //     $('input[name=tanggal_operasi]').val('". date('d-M-Y') ."');
    //     $('.data-filter').trigger('click');
    // });

    $(document).ready(function() {
        $('#calendar').fullCalendar({
            schedulerLicenseKey: 'nesimrs',
            refetchResourcesOnNavigate : true,
            locale : 'id',
            height: 650,
            defaultEventMinutes: 30,
            timeFormat : 'H:mm',
            editable: false, // enable draggable events
            droppable: false, // this allows things to be dropped onto the calendar
            aspectRatio: 2,
            scrollTime: '00:00', // undo default 6am scrollTime
            header: {
                right: 'timelineDay'
            },
            defaultView: 'timelineDay',
            resourceLabelText: 'Poliklinik - Dokter - Hari',
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
                $(element).attr('tabindex','0');
                var _html = '<div class=\"\"><label class=\"\"><b>'+ event.hari +'</b></label></div>';
                if (event.cek == 598) {
                    _html += '<div class=\"\"><label class=\"\"><b><table><tr><td>Jam Pelayanan</td><td>&nbsp:&nbsp</td><td>'+ event.jam_rencana_mulai +' - '+ event.jam_rencana_selesai +'</td></tr><tr><td>Kuota Offline</td><td>&nbsp:&nbsp</td><td>'+ event.kuota +'</td></tr><tr><td>Kuota Online</td><td>&nbsp:&nbsp</td><td>'+ event.kuota_online +'</td></tr><tr><td>Kuota Total</td><td>&nbsp:&nbsp</td><td>'+ event.kuota_total +'</td></tr></table></b></label></div>';
                }
                else{
                   _html += '<div class=\"\"><label class=\"\"><b><table><tr><td>Jam Pelayanan</td><td>&nbsp:&nbsp</td><td>'+ event.jam_rencana_mulai +' - '+ event.jam_rencana_selesai +'</td></tr></table></b></label></div>'; 
                }
                    // if (event.status == 470) {
                        _html += '<hr><div class=\"text-center\">';
                        _html += '<a type=\"button\" class=\"btn btn-info btn-labeled btn-xs btn-ukuran\" action=\"/master/jadwal-dokter/update?id='+ event.id +'\" data-toggle=\"modal\" data-target=\"#modal_backdrop\">';
                        _html += '<b><i class=\"fa fa-eye\"></i></b>Edit</a></div>';
                    // }
                $(element).attr('data-content',_html);
                $(element).attr('data-original-title',event.label);
                $(element).attr('data-placement','bottom');
                element.popover({
                    trigger: 'focus',
                    container:'body',
                    title: '<div class=\"label label-lg label-primary col-xs-12\">Informasi Jadwal</div>',
                    html: true,
                    template: '<div class=\"popover border-teal-400\"><div class=\"arrow\"></div><h3 class=\"popover-title bg-teal-400\"></h3><div class=\"popover-content\"></div></div>'

                });
            },
            loading : function (isLoading, view) {

            }

        });
        $('.fc-toolbar, .fc-license-message').hide();
        $('.data-filter').trigger('click');
    });
    ", VIEW::POS_END, 'js-kunings');
?>
