<?php

/**
 * @Author: Ardi Pratama Septiadi
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use app\components\DocoConstants;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Data Kamar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Data Kamar'), 'url' => ['/ranap/inf-data-kamar']];
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .padding-bottom-8px {
        padding-bottom: 8px !important;
    }

    .box {
        border-radius:5px;
        float:left;
        height:20px;
        width:20px;
        margin-bottom:15px;
        border:1px solid black;
        clear:both;
    }
    .actives{
      color: #37474f;
      
box-shadow: 0 0 4px 3px;
    }
    .checkmark {
      display: inline-block;
      transform: rotate(45deg);
      height: 25px;
      width: 12px;
      margin-left: 60%; 
      border-bottom: 7px solid #78b13f;
      border-right: 7px solid #78b13f;
    }
    :root {
  --borderWidth: 4px;
  --height: 16px;
  --width: 8px;
  --borderColor: green;
}

    .check {
  display: inline-block;
  transform: rotate(45deg);
  height: var(--height);
  width: var(--width);
  margin-left: 12px;
  border-bottom: var(--borderWidth) solid var(--borderColor);
  border-right: var(--borderWidth) solid var(--borderColor);
}

.tooltip-inner {
    max-width: none;
    white-space: nowrap;
    text-align: left;
}

.dataTables_scroll {
    max-height: 600px !important;
    overflow: auto;
    position: relative;
}

</style>

<div class="row body">
    <div class="col-md-12">
        <div class="panel panel-white">
            <div class="panel-heading">
                <!-- breadcrumbs replace with this -->
                <div class="row">
                  <div class="column-1">
                    <img src="<?= Yii::$app->docoVars->workspace("modul_icon"); ?>">
                  </div>
                  <div class="column-2">
                    <h3 class="panel-title"><b>
                        <?= Yii::t('fe', 'Informasi Data Kamar'); ?>
                       </b></h3>
                    <?=Breadcrumbs::widget(DocoHelpers::breadcrumbs($this->params['breadcrumbs']));?>
                  </div>
                </div>
                <!-- end -->
            </div>
            <div class="panel-toolbar clearfix">
                <?= DocoHelpers::generateToolbar([
                    'search',
                    'reset' => [
                        'attributes' => [
                             'data-parent' => '.filter-form',
                             'id' => 'btn-reset',
                        ]
                    ],
                ], '#table-data-kamar');?>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                    <div class="col-md-3 col-xs-6 filter_status mb-10 pb-5">
                        <div class='form-group'>
                            <label>Status :</label><br> 
                        <?=  Html::dropDownList('is_stopakomodasi', '',
                                ArrayHelper::map($list_status, 'is_stopakomodasi', 'status'),
                                [
                                    'id' => 'filter_status',
                                    'class' => 'form-control select2',
                                    'style'=>'width:100%;',
                                    'prompt' => \Yii::t('fe', '--Pilih--'),
                                ]
                            )
                        ?>
                        </div>
                    </div>
                </div>
                <!-- <div class="row" style="margin-top:5px;">
                    <?php $counter = 0; ?>
                    <?php $data_type = 1; ?>
                    <?php foreach ($list_keterangan as $keterangan) { ?>
                    <div class="col-xs-2 filter-legend" data-type="<?=@$keterangan['kettempattidur_warna']++?>">
                        <div class="box" id="<?=@$counter++?>" style="background-color:<?=@$keterangan['kode_warna']?>;border-color:<?=@$keterangan['rgb']?>" ></div>
                        <span style="margin-left:5px;font-size:11px;"><?= @$keterangan['kettempattidur_nama'] ?><div class="" id="checkmark"></div></span>
                    </div>
                    <?php } ?>
                </div> -->
                <div class="row">
                    <div class="col-sm-12">
                        <table id="table-data-kamar" class="table table-striped table-hover" style="width:100%;">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="20">No</th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Kamar");?></th>
                                    <th><?=\Yii::t("fe", "Kelas");?></th>
                                    <th><?=\Yii::t("fe", "Harga");?></th>
                                    <th><?=\Yii::t("fe", "Keterangan");?></th>
                                    <th><?=\Yii::t("fe", "Tempat Tidur");?></th>
                                    <th style="display: none;"><?=\Yii::t("fe", "No Rekam Medik");?></th>
                                    <th style="display: none;"><?=\Yii::t("fe", "Status");?></th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div id="modal_detail_kamar" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Informasi</h4>
      </div>
      <div class="modal-body">
      </div>
    </div>
  </div>
</div>

<div id="modal_update_status" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Update Status</h4><h4><b class="bed-name">xxx</b></h4>                
            </div>
            <div class="modal-body">
            <form action="#" id="form-update-status">
                    <div class="row">
                        <div class="col-md-12 select2-md">
                            <?=Html::hiddenInput('kamartempattidur_id', null, ['id' => 'updatestatus-kamartempattidur_id'])?>
                            <?=Html::dropDownList('kettempattidur_id', '', ArrayHelper::map($list_keterangan_nonisi, 'kettempattidur_id', 'kettempattidur_nama'), [
                                'class' => 'form-control select2',
                                'prompt' => 'Pilih Status',
                                'id' => 'updatestatus-status'
                            ])?>
                            <p class="error-message" style="color: red;margin-top: 10px; margin-left: 5px"></p>
                        </div>
                        <div class="col-md-12 text-right">
                        <br>
                            <button type="submit" id="save-update-status" class="btn btn-success btn-xs btn-labeled"><b><i class="fa fa-save"></i></b> Simpan</button>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php 
$this->registerJs("
    var table;

    $(document).ready(function() {
        var groupColumn = 1;
        $('.ceklis').hide();
        table = $('#table-data-kamar').removeAttr('width').DataTable({
            bInfo:false,
            bPaginate:false,
            filter:true,
            order:[[groupColumn,'asc']],
            sorting:[[1,'asc']], 
            displayLength:10,
            processing:true,
            serverSide:true,
            scrollX:true,
            scrollY: false,
            ajax:baseUrl+'ranap/inf-data-kamar/get-data-tempat-tidur-v2',
            columnDefs:[
                {
                    targets:groupColumn,
                    visible:false
                },
                {
                    targets:8,
                    render:function(data, type, full, meta){
                        return '<div style=white-space:normal;width:100%;>'+data+'</div>';
                    }
                }
            ],
            columns:[                
               {
                    title:'No',
                    data:'rowNum',
                    searchable:false,
                    orderable:false
                },
                {
                    title:'".(\Yii::t('fe', 'Ruangan'))."', 
                    data:'ruangan_nama',
                    name: 'ruangan_id'
                },
                {
                    title:'".(\Yii::t('fe', 'Kamar'))."', 
                    data:'kamarruangan_nokamar',
                    name: 'kamarruangan_id'
                },
                {
                    title:'".(\Yii::t('fe', 'Kelas'))."',
                    data:'kelaspelayanan_nama',
                    name:'kelaspelayanan_id',
                },
                {
                    title:'".(\Yii::t('fe', 'Harga'))."',
                    data:'harga_tariftindakan',
                    searchable:false,
                    orderable:false
                },
                {
                    title:'".(\Yii::t('fe', 'Keterangan'))."',
                    data:'keterangan',
                    searchable:false,
                    orderable:false
                },
                {
                    title:'".(\Yii::t('fe', 'Tempat Tidur'))."',
                    data:'no_tempattidur',
                    searchable: false,
                    orderable:false
                },
                {
                    title:'".(\Yii::t('fe', 'No Rekam Medik'))."',
                    data:'no_rekam_medik',
                    visible: false,
                    orderable:false
                },
                {
                    title:'".(\Yii::t('fe', 'Status'))."',
                    data:'is_stopakomodasi',
                    visible: false,
                    orderable:false
                   
                },
            ],     
            drawCallback:function (settings) {
                var api = this.api();
                var rows = api.rows({page:'current'}).nodes();
                var last = null;
     
                api.column(groupColumn, {page:'current'}).data().each(function(group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            '<tr class=\'ruangan-tr\'><td class=\'ruangan-name\' colspan=8 style=padding-bottom:8px!important;background-color:#d8efff!important;><b>RUANGAN '+group.toUpperCase()+'</b></td></tr>'
                        );
     
                        last = group;
                    }
                });
                $('.btn-bed').bind('click', ({delegateTarget}) => {
                    const _elem = $(delegateTarget)
                          _val = _elem.attr('data-value')
                          _title = _elem.attr('data-title')
                    if(_elem.attr('data-ket_id') == 11){
                        docoNotification('warning', 'Perhatian', 'Tempat tidur dengan status occupied tidak dapat melakukan perubahan status')
                        return false
                    }

                    const _placement = _elem.attr('data-placement')
                    if (_placement != undefined) {
                        docoNotification('warning', 'Perhatian', 'Status tempat tidur tidak dapat diubah karena saat ini sedang digunakan oleh pasien.')
                        return false
                    }
                        
                    $('#modal_update_status').modal()
                    $('#updatestatus-kamartempattidur_id').val(_val)
                    $('.bed-name').text( _title )
                })
            },
            fnRowCallback: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
                $(nRow).children().each(function (index, td) {
                    $(this).addClass('padding-bottom-8px');
                });
            }
        });

        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(table, [      
            [
                1,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('ruangan_id', '',
                        ArrayHelper::map($list_data_ruangan, 'ruangan_id', 'ruangan_nama'),
                        [
                            'id' => 'filter_ruangan',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                2,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kamarruangan_id', '',
                        ArrayHelper::map($list_data_kamar, 'kamarruangan_id', 'kamarruangan_nokamar'),
                        [
                            'id' => 'filter_kamar',
                            'class' => 'form-control select2',
                            'style' => 'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                3,
                \"<div class='form-group'>".preg_replace("/[\n\t\r]/i", '', preg_replace("/[\"]/i", '\'',
                    Html::dropDownList('kelaspelayanan_id', '',
                        ArrayHelper::map($list_kelas_pelayanan, 'kelaspelayanan_id', 'kelaspelayanan_nama'),
                        [
                            'id' => 'filter_kelas',
                            'class' => 'form-control select2',
                            'style'=>'width:100%;',
                            'prompt' => \Yii::t('fe', '--Pilih--'),
                        ]
                    )
                ))."<div>\"
            ],
            [
                7,
                \"<input type='text' id='no_rekam_medik' class='form-control' value='' />\"
            ],
            [
                8,
                \"<input type='text' id='status' class='form-control' value='' />\"
            ],
            
            
        ],
        {
            7:0,
            1:1,
            2:2,
            3:3,
            8:4,
        });

        $('#table-data-kamar tbody').on( 'click', '.modal_detail', function () {
            var button = $(this);
            var data_kamarruangan_id = button.data('id');
            var data_ruangan_id = button.data('ruangan_id');
            var data_kelas_id = button.data('kelaspelayanan_id');
            $('.modal-body').load(
                '/ranap/inf-data-kamar/detail-kamar-ruangan?kamarruangan_id='
                +data_kamarruangan_id+'&ruangan_id='+data_ruangan_id+'&kelaspelayanan_id='+data_kelas_id
                , function(result){
                    $('#modal_detail_kamar').modal({show:true});
                }
            );
        });

        
        

        $('#form-update-status').on('submit', function(e) {
            e.preventDefault()
            const data = $('#form-update-status').serializeArray()
            if( $('#updatestatus-status').val() == ''){
                $('.error-message').text('Status Tidak Boleh Kosong!')
                setTimeout(function() {
                    $('.error-message').text('')
                }, 1500);
                return false
            }
            $.ajax({
                url: '/ranap/inf-data-kamar/save-update-status',
                data: data,
                method: 'POST',
                success: function(response) {
                    $('#updatestatus-status').val(null).trigger('change')
                    $('#modal_update_status').modal('hide')
                    docoNotification('success', 'Proses Berhasil!', 'Update status Tempat Tidur berhasil!')
                    table.draw()
                }
            })
        })
    });
", View::POS_END, 'js-kuning');
$this->registerJs($this->render('js/index.js'), View::POS_END);