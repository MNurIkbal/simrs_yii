<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;
use yii\helpers\ArrayHelper;

?>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Berkas - <?=ArrayHelper::getValue($info_pasien, 'nama_pasien')?> - <?=ArrayHelper::getValue($info_pasien, 'no_rekam_medik')?></h5>
</div>
<div class="modal-body">
    <div class="row">
      <div class="col-md-12">
        <div class="panel-toolbar clearfix">                
            <?=DocoHelpers::generateToolbar([
                'search',
                'reset'=>['attributes'=>['data-parent'=>'.filter-form']],   
            ], '#table-berkas-pasien');?>  
        </div>
        <div class="panel-body">
          <div class="row">
            <div class="col-md-12 filter-form"></div>
          </div>
          <table id="table-berkas-pasien" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead class="text-center">
              <tr class="bg-inverse">
                <th><?= Yii::t('fe', 'No') ?></th>
                <th><?= Yii::t('fe', 'Nama File') ?></th>
                <th><?= Yii::t('fe', 'Terakhir Diubah') ?></th>
                <th><?= Yii::t('fe', 'Action') ?></th>
              </tr>
            </thead>
            <tbody> 
            </tbody>
          </table>
        </div>
     </div>
   </div>
</div>

<?php 
$this->registerJs("
    var no_rekam_medik = '".$no_rekam_medik."';
    $(document).ready(function(){
        var tableBerkasPasien = $('#table-berkas-pasien').docoTabel({
            filter: true,
            cacheFilter: false,
            sorting: [[2, 'desc']],
            displayLength: 10,
            processing: true,
            serverSide: true,
            ajax: {
                url : '/igd/riwayat-pasien/datatable-berkas-pasien?no_rekam_medik=' + no_rekam_medik,
            }, 
            columns: [
                {
                    title: 'No',
                    data: 'no',
                    orderable: false,
                    searchable: false,
                    render: (data, rowElement, rowData, meta) => {
                        return meta.row + 1;
                    }
                },
                {
                    title: 'Nama File',
                    data: 'filename',
                },
                {
                    title: 'Terakhir diubah',
                    data: 'timestamp',
                    searchable: false,
                    render: (data, rowElement, rowData) => {
                        return rowData.timestamp == null ? '-' : moment.unix(rowData.timestamp).format('DD/MM/YYYY H:mm');
                    }

                },
                {
                    title: 'Aksi',
                    data: 'filename',
                    searchable: false,
                    orderable: false,
                    render: (data, rowElement, rowData) => {
                        if (rowData.type == 'file') {
                            return '<button type=\"button\" class=\"btn btn-info btn-sm btn-preview-berkas-pasien\" data-target=\"#modal-preview\" data-url=\"/igd/riwayat-pasien/preview-berkas-pasien?name='+data+'&no_rekam_medik='+no_rekam_medik+'\">Preview</button>';
                        }
                        return '';
                    }
                },
            ],
        });
        
        $('.dataTables_filter').hide();

        $('.filter-form').datatableBootstrapFilter(tableBerkasPasien, [      
          ], {
            1:0,
          }, true
        );
    });
    

    
    $(document).on('click', '.btn-preview-berkas-pasien', ({ currentTarget }) => {
        const dataBtn = $(currentTarget).data()
        if (typeof dataBtn.url != 'undefined' && dataBtn.url != null && dataBtn != '') {
            $('#modal-preview').data('url', dataBtn.url)
            $('#modal-preview').modal({
                backdrop: 'static',
                keyboard: false
            })
        }
    })
 ", View::POS_END, "b-index"); ?>
