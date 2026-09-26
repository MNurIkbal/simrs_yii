<?php
use yii\helpers\Html;
?>
<style>
.button-print {
  text-align: end;
}

.print-makanan {

  .form-control{
    height: 28px;
  }
  .row {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;

    .flex-item {
      padding: 2px 10px;
      flex-shrink: 1;
      flex: 2;
    }

    .flex-item:last-child {
      flex: 1;
      align-self: center;
    }
  }
}
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?= $title; ?></h5>
</div>
<div class="modal-body print-makanan">
    <div class="row">
       <?php if ($with_jumlah == true): ?>
        <div class="form-group flex-item required">
          <label class="text-left control-label"><b><?= Yii::t("fe", "Jumlah") ?></b></label>
          <?= Html::textInput('jumlah', '', ['class' => 'form-control input-sm docoNumberOnly', 'id' => 'jumlah', 'type' => 'number', 'min' => 1]) ?>
          <div class="help-block"></div>
        </div>
      <?php endif; ?>
        <div class="form-group flex-item required">
          <label class="text-left control-label col-sm-5"><b><?= Yii::t("fe", "Waktu") ?></b></label>
          <?=
            Html::dropDownList('waktu', '',
                $waktu,
                [
                    'class' => 'form-control select2',
                    'prompt' => \Yii::t('fe', '-- Pilih --'),
                    'id' => 'waktu'
                ]
            )
          ?>
          <div class="help-block"></div>
        </div>

        <div class="flex-item button-print">
          <div class="btn btn-success btn-block" id="print">Print</div>
        </div>
    </div>
</div>
<script type="text/javascript">
  
    var id = "<?= $id ?>";

    $(function () {
        if (!konfigPrintGizi) {
          $('#waktu').parents('.form-group').remove();
        }

        $("#print").on("click",function() {
          var jumlah = $('#jumlah').val();
          var waktu = $('#waktu').val();
          showPrintLabel(id, jumlah, waktu);
        });
        $(".input-field").on('keyup', function (e) {
          var jumlah = $('#jumlah').val();
          if (e.key === 'Enter' || e.keyCode === 13) {
            showPrintLabel(id, jumlah, waktu);
          }
        });
    });

    function showPrintLabel(pendaftaranId, jumlah, waktu) {
        let validated = true;
        if (pendaftaranId.length <= 0 && pendaftaranId == '') {
            validated = false;
        }

        if (jumlah == '') {
            $('.print-makanan #jumlah').siblings('div.help-block').html('Tidak boleh kosong');
            $('.print-makanan #jumlah').parents('.form-group').addClass('has-error');
            validated = false;
        } else if (jumlah <= 0) {
            $('.print-makanan #jumlah').siblings('div.help-block').html('Tidak boleh kurang dari 1');
            $('.print-makanan #jumlah').parents('.form-group').addClass('has-error');
            validated = false;
        }

        if (waktu == '') {
            $('.print-makanan #waktu').siblings('div.help-block').html('Tidak boleh kosong');
            $('.print-makanan #waktu').parents('.form-group').addClass('has-error');
            validated = false;
        }

        if (validated) {
            let url  = '/gizi/inf-pasien-ranap/print-label-makanan';
            let urlParams = {
              id: pendaftaranId,
              jumlah: jumlah ? jumlah : 1,
            };

            if (waktu) {
              urlParams['waktu'] = waktu;
            }
            window.open(url +'?'+new URLSearchParams(urlParams).toString(), '_blank');
            $("#modal_backdrop").modal("toggle");
        }
    }

    function clearErrorMessage(){
        $('.print-makanan div.help-block').html('');
        $('.print-makanan .form-group.has-error').removeClass('has-error')
    }

</script>