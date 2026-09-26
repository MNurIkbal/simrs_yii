<?php

/**
 * @author Randy Vianda Putra
 * @todo Informasi keterserdiaan kamar
 * @copyright 14 November 2018 aweutist
 */

use yii\bootstrap\Modal;
use yii\web\View;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\Breadcrumbs;
use app\components\DocoHelpers;
use kartik\widgets\DepDrop;
use kartik\widgets\Select2;
use yii\web\JsExpression;

$this->title = Yii::t('fe', 'Data Kamar');
$this->params['breadcrumbs'][] = ['label' => Yii::t('fe', 'Informasi Data Kamar'), 'url' => ['/ranap/inf-data-kamar']];
$this->params['breadcrumbs'][] = $this->title;
?>
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

                <?=DocoHelpers::generateToolbar([
                    'search',
                    'reset' => ['attributes' => ['data-parent' => '.filter-form']],
                ], '#table-data-kamar');?>
            </div>
            <div class="panel-body">

                <div class="row">
                    &nbsp;
                </div>
                <div class="row">
					<?php foreach($list_keterangan as $keterangan){?>

						<div class="col-md-2">
							<div class="square" style="background-color: <?=@$keterangan['kode_warna']?>"></div>
							<h6><?=@$keterangan['kettempattidur_nama']?></h6>
						</div>
					<?php } ?>
				</div>

                <div class="row">
                    <div class="col-md-12 filter-form"></div>
                </div>

                <div class="row">
                    <div class="col-sm-12"><br><br>
                        <table id="table-data-kamar" class="table table-striped table-condensed table-hover" style="width:100%">
                            <thead>
                                <tr class="bg-inverse">
                                    <th width="20">No</th>
                                    <th><?=\Yii::t("fe", "Ruangan");?></th>
                                    <th><?=\Yii::t("fe", "Kelas");?></th>
                                    <th><?=\Yii::t("fe", "Kamar");?></th>
                                    <th><?=\Yii::t("fe", "No Tempat Tidur");?></th>
                                    <th><?=\Yii::t("fe", "Jumlah Tempat Tidur");?></th>
                                    <th><?=\Yii::t("fe", "Total Terpakai");?></th>
                                    <th><?=\Yii::t("fe", "Total Kosong");?></th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
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
<?php
$this->registerJs("
var table;
// Event Ready
$(document).ready(function() {
    // Generate Table
    var groupColumn = 1;
    table = $('#table-data-kamar').removeAttr('width').DataTable({
        bInfo : false,
        bPaginate: false,
        columnDefs: [
            { visible: false, targets: groupColumn }
        ],
        filter: true,
        order: [[ groupColumn, 'asc' ]],
        sorting: [[1, 'asc']],
        displayLength: 10,
        processing: true,
        serverSide: true,
        scrollX: true,
        ajax: baseUrl+'pendaftaran/inf-data-kamar/get-data-tempat-tidur',
        columns: [
           {
                title: 'No',
                data: 'rowNum',
                searchable: false,
                orderable: false
            },
            {
                title: '".(\Yii::t('fe', 'Ruangan'))."',
                data: 'ruangan_nama',
                searchable:false
            },
            {
                title: '".(\Yii::t('fe', 'Kamar'))."',
                data: 'data_kamar_ruangan',
                searchable:false,
                render:function(data, type, row){
                    // return '<a type=button data-kelaspelayanan_id='+data.kelaspelayanan_id+' class= modal_detail>'+data.kamarruangan_nokamar +'</a>';
                    return data.kamarruangan_nokamar;
                }
            },
            {title: '".(\Yii::t('fe', 'Kelas'))."', data: 'kelaspelayanan_nama'},
            {title: '".(\Yii::t('fe', 'No Tempat Tidur'))."', data: 'detail_tempat_tidur',searchable:false},
            {title: '".(\Yii::t('fe', 'Jumlah Tempat Tidur'))."', data: 'jumlah_tempattidur',searchable:false},
            {title: '".(\Yii::t('fe', 'Total Terpakai'))."', data: 'total_terpakai',searchable:false},
            {title: '".(\Yii::t('fe', 'Total Kosong'))."', data: 'total_kosong',searchable:false}
        ],
        drawCallback: function ( settings ) {
            var api = this.api();
            var rows = api.rows( {page:'current'} ).nodes();
            var last=null;

            api.column(groupColumn, {page:'current'} ).data().each( function ( group, i ) {
                if ( last !== group ) {
                    $(rows).eq( i ).before(
                        '<tr class=group><td colspan=13><b>'+group+'</b></td></tr>'
                    );

                    last = group;
                }
            } );
        }
    });

    $('.dataTables_filter').hide();

    $('.filter-form').datatableBootstrapFilter(table, [
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
    ]);

     $('#table-data-kamar tbody').on( 'click', '.modal_detail', function () {
        var button = $(this);
        var data_kamarruangan_id = button.data('id');
        var data_ruangan_id = button.data('ruangan_id');
        var data_kelas_id = button.data('kelaspelayanan_id');
        $('.modal-body').load(
            '/pendaftaran/inf-data-kamar/detail-kamar-ruangan?kamarruangan_id='
            +data_kamarruangan_id+'&ruangan_id='+data_ruangan_id+'&kelaspelayanan_id='+data_kelas_id
            ,function(result){
            $('#modal_detail_kamar').modal({show:true});
        });

    });


});", View::POS_END, 'js-kuning');
