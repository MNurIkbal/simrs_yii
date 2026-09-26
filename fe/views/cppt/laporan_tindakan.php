<?php

use yii\web\View;

?>
<style>
.AnyTime-pkr { z-index: 9999 }

body.modal-open {
    overflow: hidden;
    position: fixed;
}
</style>

<div class="modal-header bg-inverse">
    <button type="button" class="close close-modal-jadwal" data-dismiss="modal">&times;</button>
    <h5 class="modal-title"><?=$title;?></h5>
</div>
<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="row">
                <div class="col-sm-2 form-group">
                    <label>Instruksi</label>
                    <br>
                    <input type="text" name="diagnosa" class="form-control" id="filter-instruksi">
                </div>
                <div class="col-sm-2 form-group">
                    <label>Jenis</label>
                    <br>
                    <input type="text" name="diagnosa" class="form-control" id="filter-jenis">
                </div>
                <div class="form-group new-filter col-md-3 col-xs-6 1">
                    <label>Tanggal Instruksi</label>
                    <br>
                    <div class="input-group" >
                        <input type="text"  id="rangeDemoStart" class="form-control startDateTerapi pickadate range_custom" style="background-color:white;" value="" col-index="3" readonly="">
                        <span class="input-group-addon" style="border-left: 0; border-right: 0;">-</span>
                        <input type="text" id="rangeDemoFinish" readonly="" class="form-control endDateTerapi pickadate range_custom" style="background-color:white;" value="" col-index="3" disabled="true">
                        <input type="text" style="display:none" class="targetDate dateTarget1" col-index="3" value="">
                    </div>
                </div>
                <div class="col-sm-4 form-group">
                    <br/>
                    <button type="button" id="btn-search-filter-terapi" class="btn btn-xs btn-only btn-primary-color btn-reset-filter-terapi" data-toggle="tooltip" title data-original-title="Search">
                        <i class="fa fa-search"></i>
                    </button>
                    <button type="button" id="btn-reset-filter-terapi" class="btn btn-xs btn-only btn-primary-color btn-reset-filter-terapi" data-toggle="tooltip" title data-original-title="Reset Filter">
                        <i class="fa fa-undo"></i>
                    </button>
                </div>
            </div>
            <br>
            <table class="table table-bordered" width="100%" id="tbl-laporan-tindakan" data-href="<?=$url['datatable']?>">
                <thead>
                    <tr class="bg-inverse">
                        <th>No.</th>
                        <th>No. Pendaftaran</th>
                        <th>Jenis</th>
                        <th>Tanggal Instruksi</th>
                        <th>Instruksi</th>
                        <th>Pegawai Pemberi Instruksi</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody></tbody>
                <tfoot>
                    <tr id="menu-action-laporan-tindakan">
                        <th colspan="7">
                            <div class="flex-menu-laporan-tindakan">
                                <div class="list-action-button-laporan-tindakan-table" style="display: flex; justify-content: center; align-items: center;">
                                    <!-- <a href="#" class="action-laporan-tindakan-table" data-event="show">Load more data</a>
                                    <a href="#" class="action-laporan-tindakan-table" data-event="hide" style="visibility: hidden;">Hide more data</a> -->
                                    <button type="button" class="btn btn-xs btn-only btn-primary-color btn-load">Load More Data</button>
                                    <button type="button" class="btn btn-xs btn-only btn-primary-color btn-hide">Hide Data</button>
                                    <input type="hidden" id="filter-laporan-tindakan-limit" value="5">
                                </div>
                                
                            </div>
                        </th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?php
$this->registerJs($this->render('js/laporan_tindakan.js'), View::POS_END);
?>
