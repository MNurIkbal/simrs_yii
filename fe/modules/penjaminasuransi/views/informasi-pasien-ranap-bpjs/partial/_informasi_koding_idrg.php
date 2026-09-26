<div class="row m-3">
    <div class="col-md-12">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h6 class="panel-title"><b><?= Yii::t('fe', 'iDRG'); ?></b></h6>
            </div>
            <div class="panel-body">
                <!-- Section IDRG -->
                <div class="row mb-3 mt-3 p-5" style="margin-top: 20px;">
                    <div class="col-md-6">
                        <div>
                            <div style="max-width: 350px; display: flex;" class="mb-3">
                                <div style="width: 100%;">
                                    <label for="idrgDiagnosa">
                                        <b>Diagnosa ICD - 10</b>
                                    </label>
                                    <select name="idrgDiagnosa" id="idrgDiagnosa" class="form-control koreksi-diagnosa" data-type="ICD X" data-type-ina="1" data-button="btn-add-diagnosa-idrg"></select>
                                </div>
                                <div>
                                    <label></label>
                                    <button type="button" class="btn btn-success hidden btn-sm ml-2 mt-1" id="btn-add-diagnosa-idrg">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <table id="idrgDiagnosaTable" class="table table-striped table-hover table-bordered " style="width: 100%;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th style="width:50px !important;">No</th>
                                        <th>Nama Diagnosa</th>
                                        <th style="width: 100px !important;">Primer</th>
                                        <th style="width: 100px !important;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="idrgDiagnosaBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div>
                            <div style="max-width: 350px; display: flex;" class="mb-3">
                                <div style="width: 100%;">
                                    <label for="idrgProcedure">
                                        <b>Prosedur ICD - 9</b>
                                    </label>
                                    <select name="idrgProcedure" id="idrgProcedure" class="form-control koreksi-procedure" data-type="ICD IX" data-button="btn-add-procedure-idrg" data-type-ina="1"></select>
                                </div>
                                <div>
                                    <label for=""></label>
                                    <button type="button" class="btn btn-success hidden btn-sm ml-2 mt-1" id="btn-add-procedure-idrg">
                                        <i class="fa fa-plus"></i>
                                    </button>
                                </div>
                            </div>
                            <table id="idrgProcedureTable" class="table table-striped table-hover table-bordered no-footer" style="width: 100%;">
                                <thead>
                                    <tr class="bg-inverse">
                                        <th style="width: 50px !important;">No</th>
                                        <th>Nama Procedure</th>
                                        <th style="width: 100px !important;">Multiplicity</th>
                                        <th style="width: 100px !important;">Primer</th>
                                        <th style="width: 100px !important;">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-12 hidden section-final-idrg" id="section-final-idrg">
                        <!-- <div style="padding-left: 20px; padding-right: 20px; margin-bottom: 20px">
                            <div style="border-bottom: 2px solid black; max-width: 250px;">
                                <h6 class="m-0">Hasil Grouping iDRG</h6>
                            </div>
                        </div> -->
                        <div class="row" style="margin-bottom: 10px">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h6 class="panel-title"><b><?= Yii::t('fe', 'Hasil Grouping iDRG'); ?></b></h6>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-striped table-hover" style="margin-top: 20px;">
                                            <tr>
                                                <th style="width: 150px"><?= Yii::t('fe', 'Info') ?></th>
                                                <td colspan="4" class="info-idrg-txt">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 150px"><?= Yii::t('fe', 'Jenis Rawat') ?></th>
                                                <td colspan="4" class="jenisrawat-ina-txt">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 150px"><?= Yii::t('fe', 'MDC') ?></th>
                                                <td class="text-left mdc-ina-txt"></td>
                                                <td class="text-center mdcnumber-ina-txt"></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 150px"><?= Yii::t('fe', 'DRG') ?></th>
                                                <td class="text-left drg-ina-txt"></td>
                                                <td class="text-center drgnumber-ina-txt"></td>
                                            </tr>
                                            <tr>
                                                <th style="width: 150px"><?= Yii::t('fe', 'Cost Weight **)') ?></th>
                                                <td colspan="4" class="cost-ina-txt">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 150px"><?= Yii::t('fe', 'NBR **)') ?></th>
                                                <td colspan="4" class="nbr-ina-txt">
                                                </td>
                                            </tr>
                                            <tr>
                                                <th style="width: 150px"><?= Yii::t('fe', 'Status') ?></th>
                                                <td colspan="4" class="status-ina-txt">
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="5" class="status-text-idrg">
                                                    <i style="color: blue;">** ) Catatan: Nilai belum final, sewaktu-waktu bisa berubah</i>
                                                </td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div style="display: flex; justify-content: end">
                            <button type="button" class="btn btn-success m-2" id="btn-grouping-idrg">
                                Grouping iDRG
                            </button>
                            <button type="button" class="btn btn-success m-2" id="btn-final-idrg" disabled>
                                Final iDRG
                            </button>
                            <button type="button" class="btn btn-success m-2" id="btn-edit-idrg" disabled>
                                Update iDRG
                            </button>
                        </div>
                    </div>
                </div>
                <!-- End Section IDRG -->
            </div>
        </div>
    </div>
</div>