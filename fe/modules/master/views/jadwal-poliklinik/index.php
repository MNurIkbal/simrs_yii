<?php
// Author : Naufal Ziyad L

use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\Select2;
use yii\web\JsExpression;
use app\modules\master\models\JadwalPoliklinikForm;
use Doco\master\controllers\JadwalPoliklinikController;
use app\components\DocoConstants;

$this->title = Yii::t('fe', 'Jadwal Poliklinik');
$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
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
        <div class="panel panel-white">
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
                        'reset'=> [
                            'attributes'=>[
                                'data-parent'=>'.filter-form'
                            ]
                        ],
                        'add' => [
                            'attributes' => [
                                'data-toggle' => 'modal',
                                'data-target' => '#modal_backdrop',
                                'action' => '/master/jadwal-poliklinik/create',
                            ]
                        ],
                        // 'edit' => [
                        //     'attributes' => [
                        //         'data-options' => 'modal',
                        //         'data-target' => '#modal_backdrop',
                        //         'data-url' => '/master/jadwal-poliklinik/update?id=',
                        //     ]
                        // ],
                        // 'delete' => [
                        //     'attributes' => [
                        //         'data-additional' => 'data-rm'
                        //     ]
                        // ],
                        // 'pdf',
                        // 'excel',
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
                    ], "#table-jadwalpoliklinik");?>
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
                                <label><?= Yii::t('fe', 'Ruangan'); ?> :</label>
                                <?php
                                 echo Html::dropDownList('ruangan_id',null,$ruangan,[
                                    'class' => 'select2',
                                    'id'=>'ruangan_id',
                                    'prompt'=>Yii::t('fe', '--Pilih--'),
                                ]);
                                ?>
                            </div>
                            <div class="col-md-3">
                                <label><?= Yii::t('fe', 'Shift'); ?> :</label>
                                <?php
                                 echo Html::dropDownList('shift_id',null,$shift,[
                                    'class' => 'select2',
                                    'id'=>'shift_id',
                                    'prompt'=>Yii::t('fe', '--Pilih--'),
                                ]);
                                ?>
                            </div>
                            <div class="col-md-3">
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
                                    echo Html::dropDownList('hari','',$hari,[
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

                    
                <br>

                <!-- todo -->
                <div id="calendar">
                </div>
                
            </div>
        </div>
    </div>
</div>
<div id="modal_jadwalpoliklinik" class="modal fade" data-backdrop="static">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
        </div>
    </div>
</div>
<?php
$this->registerJs("
    $('.pickatime').pickatime({
        format: 'HH:i'
    });
    ", View::POS_READY, 'time-handler');

$this->registerJs($this->render('index.js'));
$this->registerJs('
    // Global Var
    var tableJadwalPoliklinik;
    var konfigKuota = '.$konfigKuota.';
    var konfigKuotaPoli = '.DocoConstants::VAR_ID_KUOTA_ANTRIAN_POLIKLINIK.';

    // Event Reload
    $(document).on("click", ".data-reload", function() {
        tableJadwalPoliklinik.draw();
    });

    $(".data-reset").click(function() {
    $("#ruangan_id").val("");
    $("#shift_id").val("");
    $("#hari").val("");
    $("#jam_mulai").val("");
    $("#jam_selesai").val("");

    var ruangan_id  = $("#ruangan_id").val();
    var shift_id    = $("#shift_id").val();
    var hari        = $("#hari").val();
    var jam_mulai   = $("#jam_mulai").val();
    var jam_selesai = $("#jam_selesai").val();

    $(".data-filter").trigger("click");
    });

    $(document).on("switchChange.bootstrapSwitch", ".change-status", function (e, state) {

        var dataStatus = "0";
        var dataId = $(this).attr("data-id");

        if (e.target.checked == true)
            dataStatus = "1";

        $(this).docoForm("delete",{
            url: baseUrl+"master/jadwal-poliklinik/change-status?id="+dataId+"&status="+dataStatus,
            confirmTitle : "'.Yii::t('fe', 'Konfirmasi').'",
            confirmMessage : "'.Yii::t('fe', 'Apa anda yakin ingin mengubah status data ini ?').'",
            success : function (data) {
                tableJadwalPoliklinik.draw();
            }
        });
        tableJadwalPoliklinik.draw();
    });

', View::POS_END, 'b-index');

$this->registerJs("
    var _events = [];
    var _resourse = [];

    $(document).on('click','.data-filter',function (event) {
        event.preventDefault();
        $.ajax({
            data : $('.advancedFilter').serializeArray(),
            url : '/master/jadwal-poliklinik/get-data',
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

    $(document).on('click','#delete-jadwal', function (event) {
        event.preventDefault();
        var url = $(this).attr('action');
        $().docoForm('delete',{
            url: url,
            success: function(data){
                $('.data-filter').trigger('click');
            }
            })
    });

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
            resourceLabelText: 'Poliklinik - Hari',
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

                if (event.cek == 597) {
                var _html = '<div class=\"\"><label class=\"\"><b><table><tr><td>Jam Pelayanan</td><td>&nbsp:&nbsp</td><td>'+ event.jam_rencana_mulai +' - '+ event.jam_rencana_selesai +'</td></tr><tr><td>Kuota Offline</td><td>&nbsp:&nbsp</td><td>'+ event.kuota +'</td></tr><tr><td>Kuota Online</td><td>&nbsp:&nbsp</td><td>'+ event.kuota_online +'</td></tr></table></b></label></div>';
                }
                else{
                     var _html = '<div class=\"\"><label class=\"\"><b><table><tr><td>Jam Pelayanan</td><td>&nbsp:&nbsp</td><td>'+ event.jam_rencana_mulai +' - '+ event.jam_rencana_selesai +'</td></tr></table></b></label></div>';
                }
                        _html += '<hr><div class=\"text-center\">';
                        _html += '<a type=\"button\" class=\"btn btn-info btn-labeled btn-xs btn-ukuran\" action=\"/master/jadwal-poliklinik/update?id='+ event.id +'\" data-toggle=\"modal\" data-target=\"#modal_backdrop\">';
                        _html += '<b><i class=\"fa fa-eye\"></i></b>Edit</a></div>';
                        _html += '<div class=\"text-center\">';
                        _html += '<a type=\"button\" class=\"btn btn-info btn-labeled btn-xs btn-ukuran\" action=\"/master/jadwal-poliklinik/delete?id='+ event.id +'\" id=\"delete-jadwal\">';
                        _html += '<b><i class=\"fa fa-eye\"></i></b>Delete</a></div>';

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
