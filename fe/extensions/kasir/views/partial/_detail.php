<?php
    use kartik\form\ActiveForm;
    use yii\helpers\Html;
?>
<div class="modal-header">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title ?></h5>
</div>
<hr>
<div class="modal-body">
   <?php 
   $form = ActiveForm::begin([
      'id' => 'login-form', 
      'type' => ActiveForm::TYPE_HORIZONTAL,
      'formConfig' => ['labelSpan' => 3, 'deviceSize' => ActiveForm::SIZE_SMALL]
  ]);
   ?>
   <div class="row">
      <div class="form-group">
         <label for="" class="col-lg-2 control-label">Dokter </label>
         <div class="col-lg-3">
            <div class="form-group">
              : <strong><i><?= $header['nama_pegawai'] ?></i></strong>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="form-group">
         <label for="" class="col-lg-2 control-label">Nama Pasien </label>
         <div class="col-lg-3">
            <div class="form-group">
              : <strong><?= $header['nama_pasien'] ?></strong> 
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="form-group">
         <label for="" class="col-lg-2 control-label">No RM </label>
         <div class="col-lg-3">
            <div class="form-group">
              : <strong><?= $header['no_rekam_medik'] ?></strong>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="form-group">
         <label for="" class="col-lg-2 control-label">No REG </label>
         <div class="col-lg-3">
            <div class="form-group">
              : <strong><?= $header['no_pendaftaran'] ?></strong>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="form-group">
         <label for="" class="col-lg-2 control-label">Instalasi </label>
         <div class="col-lg-3">
            <div class="form-group">
              : <strong><?= $header['instalasi_nama'] ?></strong>
            </div>
         </div>
      </div>
   </div>
   <div class="row">
      <div class="form-group">
         <label for="" class="col-lg-2 control-label">Cara Bayar </label>
         <div class="col-lg-3">
            <div class="form-group">
              : <strong><?= $header['group_carabayar'] ?></strong>
            </div>
         </div>
      </div>
   </div>
   <br>
   <div class="row">
      <table id="detail" class="table table-striped table-condensed table-hover" style="width:100%">
         <thead>
            <tr class="bg-inverse">
               <th width="1">No</th>
               <th><?=\Yii::t("fe", "Komponen Biaya");?></th>
               <th><?=\Yii::t("fe", "Harga");?></th>
               <th><?=\Yii::t("fe", "Unit");?></th>
               <th><?=\Yii::t("fe", "Potongan");?></th>
               <th><?=\Yii::t("fe", "Jumlah");?></th>
            </tr>
         </thead>
         <tbody></tbody>
         <tfoot>
            <tr>
               <th colspan="5" style="text-align: right;"><strong>Total :</strong></th>
               <th></th>
            </tr>
         </tfoot>
      </table>
   </div>
</div>
<hr>
<div class="modal-footer">
   <?= Html::button('<b><i class="fa fa-arrow-left"></i></b>'.Yii::t('fe', ' Kembali'),[
         'class' => 'btn bg-slate',
         'data-dismiss' => 'modal'
   ]) ?>
</div>

<script>
   var id = "<?= $id ?>";
   var dokter_id = "<?= $dokterpenanggungjawab_id ?>";
   var table;
   table = $("#detail").docoTabel({
      filter: false,
      sorting: [[1, "asc"]],
      displayLength: 10,
      processing: true,
      serverSide: true,
      scrollX: true,
      scrollY: false,
      ajax: {
         url: "/kasir/lap-rekap-jasa-dokter/get-data-detail?id=" + id + "&dokter_id=" + dokter_id,
      },
      columns: [
         {
            data: null,
            searchable: false,
            orderable: false,
            render: (data, rowElement, rowData, rowAdditionalData) => {
               var tableInfo = table.page.info()
               return tableInfo.start + rowAdditionalData.row + 1
            }
         },
         {
            title: "Komponen Biaya",
            data: "daftartindakan_nama",
            searchable: false,
            render: (data) => {
               return data == "" || data == null ? "-" : data
            }
         },
         {
            title: "Harga",
            data: "total_jasadokter",
            searchable: false,
            orderable: false,
            className: "text-right",
            render: $.fn.dataTable.render.number(".", ",", 0, "")
         },
         {
            title: "Unit",
            data: "qty",
            searchable: false,
            orderable: false,
            className: "text-right",
            render: $.fn.dataTable.render.number(".", ",", 0, "")
         },
         {
            title: "Potongan",
            data: "diskon",
            searchable: false,
            orderable: false,
            className: "text-right",
            render: $.fn.dataTable.render.number(".", ",", 0, "")
         },
         {
            title: "Jumlah",
            data: "total",
            orderable: false,
            searchable: false,
            className: "text-right",
            render: $.fn.dataTable.render.number(".", ",", 0, "")
         },
      ],
      footerCallback: function (row, data, start, end, display) {
         var api = this.api(), data;
         var numFormat = $.fn.dataTable.render.number( '\.', ',', 0, 'Rp. ' ).display;
         var intVal = function (i) {
            return typeof i === 'string' ?
               i.replace(/[\$,]/g, '') * 1 :
               typeof i === 'number' ?
                  i : 0;
         };
         total = api
            .column(5)
            .data()
            .reduce(function (a, b) {
               return intVal(a) + intVal(b);
            }, 0);

         pageTotal = api
            .column(5, { page: 'current' })
            .data()
            .reduce(function (a, b) {
               return intVal(a) + intVal(b);
            }, 0);

         $(api.column(5).footer()).html(
            "<strong>" + numFormat(total) + "</strong>"
         );
      },
   });
</script>