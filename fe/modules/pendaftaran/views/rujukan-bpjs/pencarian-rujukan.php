<?php
use app\components\DocoHelpers;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\View;
use kartik\widgets\ActiveForm;
?>

<style>
.modal {
  overflow-y:auto;
}
</style>

<?php
$form = ActiveForm::begin([
    'id' => 'ajax-form', 
    'type' => ActiveForm::TYPE_HORIZONTAL,
    'formConfig' => ['labelSpan' => 4, 'deviceSize' => ActiveForm::SIZE_SMALL]
]);
?>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=  Yii::t('fe', 'Pencarian Rujukan') ?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <!-- div ppk rujukan -->
        <div class="ppk_rujukan col-md-6">
            <?= $form->field($model, 'kode_ppkrujuk')
            ->dropDownList([],
                [
                    'id'=>'kode_ppkrujuk',
                    'class'=>'select2PpkRujukan',
                    'prompt'=>'— PILIH —',
                ]
            )->label(Yii::t('fe', 'PPK Rujuk'));
            ?>
        </div>
    
        <!-- div tanggal rujukan -->
        <div class="tanggal-rencana-kunjungan col-md-6">
            <?php //$form->field($model, 'tanggal_rencana_kunjungan', [
            //     'inputOptions'=>['id'=>'tanggal-rencana-kunjungan', 'class'=> 'pickadate-w-month','data-mask' => '99-99-9999'],
            //     'addon' => [
            //         'append' => ['content'=>'<i id="btnDatePick" class="fa fa-calendar"></i>'],
            //     ]
            // ]);
            ?>
            <?= $form->field($model, 'tanggal_rencana_kunjungan', [
                'addon' => [
                    'append' => [
                        'content' => '<i class="fa fa-calendar"></i>'
                    ]
                ]
            ])->textInput([
                'id' => 'tanggal-rencana-kunjungan',
                'class' => 'form-control input-sm pickadate-w-month',
                'placeholder' => $model->getAttributeLabel('tanggal_rencana_kunjungan'),
                'autocomplete' => 'off',
                'readonly' => false
            ])->label($model->getAttributeLabel('tanggal_rencana_kunjungan')) ?>
        </div>
    
        <div class="col-md-6" style="margin-left:0%!important;">
            <div class="row">
                <div class="col-md-2 group-rujukan-penuh">
                    <?= Html::button('<b><i class="fa fa-search"></i></b>'.Yii::t('fe', 'Cari'), [
                    'class' => 'btn btn-info btn-labeled btn-xs',
                    'id' => 'btn-cari-rujukan'
                    ]) ?>
                </div>
                <div class="col-md-2">
                    <?= Html::button('<b><i class="fa fa-refresh"></i></b>'.Yii::t('fe', 'Batal'), [
                    'class' => 'btn btn-danger btn-labeled btn-xs',
                    'id' => 'btn-batal-rujukan',
                    'data-dismiss'=> 'modal'
                ]) ?>
                </div>
                <div class="col-md-2 group-rujukan-partial">
                    <?= Html::button('<b><i class="fa fa-floppy-o"></i></b>'.Yii::t('fe', 'Simpan'), [
                        'class' => 'btn btn-success btn-labeled btn-xs',
                        'id' => 'btn-simpan-rujuk-partial',
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <hr>

    <div class="content-rujukan">
        <div class="panel-toolbar clearfix">
            <?= Html::button('<b><i class="fa fa-user-md"></i></b>'.Yii::t('fe', 'Spesialis/SubSpesialis'), [
                'class' => 'btn btn-primary btn-labeled btn-xs',
                'id' => 'btn-cari-spesialis'
            ]) ?>
        </div>
        <hr>
        <div class="row">
            <div class="advanced-filter"></div>
        </div>
        <table id="tb-spesialis" class="table table-striped table-condensed table-hover" style="width:100%">
            <thead>
                <tr class="bg-inverse">
                    <th><?= Yii::t("fe", "No") ?></th>
                    <!-- <th><?= Yii::t("fe", "Detail") ?></th> -->
                    <th><?= Yii::t("fe", "Nama Spesialis/Sub") ?></th>
                    <th><?= Yii::t("fe", "Kapasitas") ?></th>
                    <th><?= Yii::t("fe", "Jml.Rujukan") ?></th>
                    <th><?= Yii::t("fe", "Presentase") ?></th>
                    <th></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<?php
$this->registerJs("
    var table;
    var draw = 0;

    function generateTable() {
        table = $('#tb-spesialis').DataTable({
            displayLength: 10,
            filter: true,
            select: {
                style: 'os',
                selector: 'tr'
            },
            sorting: [[0, 'asc']], 
            destroy: true,
            processing: true,
            serverSide: false,
            ajax:'/pendaftaran/rujukan-bpjs/rujukan-spesialistik?ppk='+ppkRujukan+'&tgl='+$('#tanggal-rencana-kunjungan').val(),
            columns:
            [
                {
                    title: 'No',
                    data: 'rowNum',
                    searchable: false,
                    orderable: false
                },
                // {
                //     title: 'Detail',
                //     data: 'detail',
                //     searchable: false,
                //     orderable: false
                // },
                {title: '" . (\Yii::t("fe", "Nama Spesialis/Sub")) . "', data: 'namaSpesialis', name: 'namaSpesialis'},
                {title: '" . (\Yii::t("fe", "Kapasitas")) . "', data: 'kapasitas', name: 'kapasitas'},
                {title: '" . (\Yii::t("fe", "Jml.Rujukan")) . "', data: 'jumlahRujukan',searchable: true},
                {title: '" . (\Yii::t("fe", "Persentase")) . "', data: 'persentase',searchable: true},
                {
                    title: '',
                    data: 'selectedSpesialis',
                    searchable: false,
                    orderable: false
                },
                {
                    title: 'kodeSpesialis',
                    data: 'kodeSpesialis',
                    searchable: false,
                    orderable: false,
                    visible: false
                },
            ],
        });
        
        $('.dataTables_filter').hide();
    }

    $('#btn-cari-rujukan').click(function (e) { 
        e.preventDefault();
        if(!ppkRujukan){
            docoNotification('error', 'Proses Gagal!', 'PPK rujuk tidak boleh kosong');
            return false;
        }

        if($('#rujukan').val() == 1) {
            $('#btn-cari-spesialis').prop('hidden',true)
            $('.content-rujukan').prop('hidden', true)
            $('.group-rujukan-partial').show()
        } else {
            $('#btn-cari-spesialis').prop('hidden',false)
            $('.content-rujukan').prop('hidden', false)
            $('.group-rujukan-partial').hide()
        }

        if(draw > 0) {
            // table.destroy();
            table.clear().draw()
            $.ajax({
                url: '/pendaftaran/rujukan-bpjs/rujukan-spesialistik?ppk='+ppkRujukan+'&tgl='+$('#tanggal-rencana-kunjungan').val(),
                type: 'GET',
                dataType: 'JSON',
                success: function (res) {
                    table.rows.add(res.data); 
                    table.columns.adjust().draw(); 
                },
            });
        } else {
            generateTable();
            draw++;
        }
       
    });

", View::POS_END, 'pencarian_rujukan');
$this->registerJs($this->render('js/modal.js'), View::POS_END);
$this->registerJs($this->render('js/set-tgl.js'), View::POS_END);
?>