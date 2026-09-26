<?php

/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-05-29
 */

use yii\helpers\Html;
use yii\web\View;

?>
<style>
  #menu{
    margin-bottom: 5px;
  }#renderRange{
    padding-left: 12px;
    font-size: 19px;
    vertical-align: middle;
  }#modal-jadwal-dokter > .modal-dialog{
    width: 50% !important;
  }.picker__button--clear{
     display: none !important;
  }.picker_modal-two {
    top: 3px !important;
  }
</style>
<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-pemeriksaan" data-dismiss="modal">&times;</button>
    <h5 class="modal-title">Lihat Jadwal</h5>
</div>
<div class="modal-body">
    <div class="row">
    <div id="menu">
      <span id="menu-navi">
        <!-- <button type="button" class="btn btn-default btn-sm move-today" id="btnToday">Today</button> -->
        <div class="col-md-4">
            <?php echo Html::textInput("tanggal-jadwal", null,[
                'class' => 'form-control input-sm lihat-jadwal-date',
                'id' => "date-lihat-jadwal",
            ]);
            ?>
        </div>
        <button id="btnPrev" type="button" class="btn btn-sm btn-info">
          <i class="fa fa-arrow-left"></i> 
        </button>
        <button id="btnNext" type="button" class="btn btn-sm btn-info">
            <i class="fa fa-arrow-right"></i> 
        </button>
        <span id="renderRange" class="render-range"></span>
      </span>
    </div>
    <div style="margin:50px" id="loading"></div>
    <div id="calendar" ></div>
</div>
<div class="modal-footer text-left">

</div>
<?php

$this->registerJsVar('pegawaiId', $pegawaiId);
$this->registerJs($this->render('jadwal-terapi.js'), View::POS_END);
?>