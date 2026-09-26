<?php

use yii\web\View;
use yii\helpers\Url;
use yii\helpers\Html;
use app\components\DocoHelpers;

$this->params['breadcrumbs'][] = ['label' => 'Master', 'url' => ['index']];
$this->params['breadcrumbs'][] = $title;
?>

<div class="row">
	<div class="col-md-12">
		<div class="panel panel-white">
			<div class="panel-heading">
				<h3 class="panel-title"><b><?=$title;?></b></h3>
                <div class="heading-elements">
                    <ul class="icons-list">
                        <li><a data-action="collapse"></a></li>
                    </ul>
                </div>
			</div>

			<div class="panel-toolbar clearfix">
                <div class="btn-group pull-left">
                    <?=Html::button('<i class="fa fa-plus"></i> Tambah', [
                        'class' => 'btn btn-primary btn-sm data-add',
                        'action' =>  Url::home().'master/layarantrian/create-pengambilan-antrian',
                        'data-toggle' => 'modal',
                        'data-target' => '#modal_backdrop'
                    ]);?>
                    <?=Html::button('<i class="fa fa-refresh"></i> Muat Ulang', [
                        'class' => 'btn btn-info btn-sm data-reload',
                    ]);?>
                    <?=Html::a('<i class="fa fa-file-excel-o"></i> Ekspor', '#', [
                        'class' => 'btn btn-success btn-sm data-export-all',
                        'action' =>  Url::home().'master/kelompokremunerasi/export-all'
                    ]);?>
                    <?=Html::a('<i class="fa fa-print"></i> Cetak', '#', [
                        'class' => 'btn btn-warning btn-sm data-export-all',
                        'action' =>  Url::home().'master/kelompokremunerasi/print-all',
                        'style' => 'display:none;'
                    ]);?>
                </div>
            </div>

			<div class="panel-body">
				<?php
                    echo Html::beginForm(null,'POST',[
                            'class' => 'form-filter',
                        ]);
                ?>
                <div class="form-group">
                    <div class="col-md-4">
                        <label>Nama :</label>
                        <?php
                            echo Html::textInput('layarantrian_name',null,[
                                'class' => 'form-control',
                                'placeholder' => 'Masukan nama'
                            ]);
                        ?>
                    </div>
                    <div class="col-md-4">
                        <label>Status :</label>
                        <?php
                            echo Html::dropDownList('is_active',1,$status,[
                                'class' => 'select2',
                            ]);
                        ?>
                    </div>
                    <div class="col-md-4">
                        <label></label>
                        <?php
                        echo Html::submitButton('<i class="fa fa-search"></i>&nbsp;Cari',[
                                                'class' => 'btn btn-default',
                                                'style' => 'margin-right:5px;margin-top:26px;'
                                            ]);
                        echo Html::button('<i class="fa fa-refresh"></i>&nbsp;Ulang',[
                                                'class' => 'btn btn-default reset-filter',
                                                'style' => 'margin-top:26px;'
                                            ]);
                        ?>
                    </div>
                </div>
                <?php
                    echo Html::endForm();
                ?>

				<table class="table datatable-basic table-striped table-hover dataTable no-footer" id="data-pengambilan-antrian" data-source="<?=Url::home();?>master/layarantrian/get-data" data-filter=".form-filter" data-test="true">
					<thead>
                        <tr>
                            <th width="10%">No</th>
                            <th><?=Yii::t('fe', 'layarantrian_jenis'); ?></th>
                            <th width="15%"><?=Yii::t('fe', 'layarantrian_fungsi'); ?></th>
                            <th><?=Yii::t('fe', 'fungsi_code_queue'); ?></th>
                            <th width="10%">Aksi</th>
                            <th width="5%">Status</th>
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
    var tabel = $('#data-pengambilan-antrian').docoTabel({
        columns : [
            {data: 'rowNum', name : 'rowNum'},
            {data: 'jenis', name : 'jenis'},
            {data: 'fungsi_code_queue', name : 'fungsi_code_queue'},
            {data: 'fungsi', name : 'fungsi'},
            {data: 'aksi', name : 'aksi'},
            {data: 'status', name : 'is_active'}
        ],
        rowsGroup: [1],
        colNoOrder : [0,4,5]
    });

    var _test = function (bool) {
        if (bool) {
            tabel.reload(false);
        } else {
            tabel.reload();
        }
    }

    var _afterSave = function (bool) {
        tabel.reload();
    }

    $(document).on('click','.delete', function(event) {
        event.preventDefault();
        $(this).docoForm('delete',{
            success : function (data) {
                tabel.reload()
            }
        });
    })

    $('.reset-filter').on('click', function (e) {
        e.preventDefault();
        tabel.reset();
    });

    $('.select2', $('form.form-filter')).change(function (event) {
        event.preventDefault();
        tabel.reload();
    });

    $('form.form-filter').on('submit', function (e) {
        e.preventDefault();
        tabel.reload();
    });
",View::POS_END,'Layarantrian');
?>