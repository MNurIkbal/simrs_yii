<?php

use yii\web\View;

$classForm = 'form-control input-sm';
$classFormNumber = 'form-control doco-number';
$styleTable = 'text-align:center;font-weight:bold;';
$classCenter = 'text-center';
$styleCells = 'width:800px;margin-top:10px';

?>

<style>
.box-scale {
    margin-top: 10px;
    padding-top: 10px;
    padding-bottom: 10px;
}

.box-scale-header {
    margin-bottom: 3px !important;
}

table, th, td {
  border: 1px solid black;
  border-collapse: collapse;
  padding: 5px;
}
</style>

<div class="row">
    <div class="row">
        <p style="font-weight:bold;margin-left:20px;margin-top:20px;margin-bottom:20px;">Catatan Perkembangan Luka</p>
    </div>
    <div class="col-sm-12">
        <table style="width:100%" class="table-catatan-luka">
            <thead>
                <tr>
                    <th class="<?=$classCenter?>" id="pl">Perkembangan Luka</th>
                    <th class="<?=$classCenter?>" id="tm">Tenaga Medis</th>
                    <th class="<?=$classCenter?>" id="aksi_catatan_luka">Aksi</th>
                </tr>
            </thead>
            <tbody style="margin-bottom:30px;margin-top:30px;">
                <tr>
                    <td>
                        <p style="font-weight:bold;">1. Ukuran Luka</p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-panjang_luka">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-panjang_luka">Panjang</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" id="panjang_luka_0" class="form-control doco-number panjang_luka" name="LukaBakarForm[panjang_luka]">
                                                    <span class="input-group-addon">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-lebar_luka">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-lebar_luka">Lebar</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" id="lebar_luka_0" class="form-control doco-number lebar_luka" name="LukaBakarForm[lebar_luka]">
                                                    <span class="input-group-addon">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-kedalaman_luka">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-kedalaman_luka">Kedalaman</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" style="margin-bottom:10px;" id="kedalaman_luka_0" class="form-control doco-number kedalaman_luka" name="LukaBakarForm[kedalaman_luka]">
                                                    <span class="input-group-addon">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p style="font-weight:bold;">2. Goa/Sinus Metode Arah Jarum Jam (cm)</p>
                        <p>Undermining/Goa</p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-lokasi_goa">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-lokasi_goa">Lokasi</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" class="form-control doco-number lokasi_goa" name="LukaBakarForm[lokasi_goa]">
                                                    <span class="input-group-addon">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-panjang_goa">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-panjang_goa">Panjang</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" class="form-control doco-number panjang_goa" name="LukaBakarForm[panjang_goa]">
                                                    <span class="input-group-addon">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p>Saluran/Sinus</p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-lokasi_sinus">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-lokasi_sinus">Lokasi</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" style="margin-bottom:10px;" class="form-control doco-number lokasi_sinus" name="LukaBakarForm[lokasi_sinus]">
                                                    <span class="input-group-addon">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-panjang_sinus">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-panjang_sinus">Panjang</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" class="form-control doco-number panjang_sinus" name="LukaBakarForm[panjang_sinus]">
                                                    <span class="input-group-addon">cm</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p style="font-weight:bold;">3. Dasar Luka (%)</p>
                        <p>Warna Type Jaringan</p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-hitam">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-hitam">Hitam</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" class="form-control doco-number hitam" name="LukaBakarForm[hitam]">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-kuning">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-kuning">Kuning</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" class="form-control doco-number kuning" name="LukaBakarForm[kuning]">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-merah">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-merah">Merah</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" style="margin-bottom:10px;" class="form-control doco-number merah" name="LukaBakarForm[merah]">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-pink">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-pink">Pink</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="text" class="form-control doco-number pink" name="LukaBakarForm[pink]">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p style="font-weight:bold;">4. Terluka </p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-terluka">
                                            <label style="margin-bottom:10px;" class="control-label has-star col-sm-3" for="lukabakarform-terluka">Menyatu Dengan Dasar Luka</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="radio" class="terluka" name="LukaBakarForm[terluka]" value="1">&nbsp;&nbsp; <span>Ya</span>&nbsp;&nbsp;
                                                    <input type="radio" class="terluka" name="LukaBakarForm[terluka]" value="0">&nbsp;&nbsp; <span>Tidak</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p style="font-weight:bold;">5. Kulit Sekitar Luka </p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-kulit_luka">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-kulit_luka">&nbsp;</label>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <p>
                                                    <input type="radio" class="kulit_luka" name="LukaBakarForm[kulit_luka]" value="1">&nbsp;&nbsp; <span>Utuh</span>&nbsp;&nbsp;
                                                    <input type="radio" class="kulit_luka" name="LukaBakarForm[kulit_luka]" value="2">&nbsp;&nbsp; <span>Edema</span>&nbsp;&nbsp;
                                                    <input type="radio" class="kulit_luka" name="LukaBakarForm[kulit_luka]" value="3">&nbsp;&nbsp; <span>Erythema/Merah</span>&nbsp;&nbsp;
                                                    </p>
                                                    <p>
                                                    <input type="radio" class="kulit_luka" name="LukaBakarForm[kulit_luka]" value="4">&nbsp;&nbsp; <span>Maceration/Lecet</span>&nbsp;&nbsp;
                                                    </p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p style="font-weight:bold;">6. Stadium Luka </p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-tanda_infeksi">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-stadium_luka">&nbsp;</label>
                                            <div class="col-sm-6">
                                                <div class="input-group">
                                                    <input type="radio" class="stadium_luka" name="LukaBakarForm[stadium_luka]" value="1">&nbsp;&nbsp; <span>1</span>&nbsp;&nbsp;
                                                    <input type="radio" class="stadium_luka" name="LukaBakarForm[stadium_luka]" value="2">&nbsp;&nbsp; <span>2</span>&nbsp;&nbsp;
                                                    <input type="radio" class="stadium_luka" name="LukaBakarForm[stadium_luka]" value="3">&nbsp;&nbsp; <span>3</span>&nbsp;&nbsp;
                                                    <input type="radio" class="stadium_luka" name="LukaBakarForm[stadium_luka]" value="4">&nbsp;&nbsp; <span>4</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p style="font-weight:bold;">7. Tanda-Tanda Infeksi </p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-tanda_infeksi">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-tanda_infeksi">&nbsp;</label>
                                            <div class="col-sm-9">
                                                <input type="text" style="margin-bottom:10px;" class="form-control input-sm tanda_infeksi" name="LukaBakarForm[tanda_infeksi]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p style="font-weight:bold;">8. Nyeri Luka </p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-nyeri_luka">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-nyeri_luka">Skala</label>
                                            <div class="col-sm-9">
                                                <input type="text" style="margin-bottom:10px;" class="form-control input-sm nyeri_luka" name="LukaBakarForm[nyeri_luka]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p style="font-weight:bold;">9. Exudate </p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-jumlah_exudate">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-jumlah_exudate">Jumlah</label>
                                            <div class="col-sm-9">
                                                <div class="input-group">
                                                    <input type="text" class="form-control doco-number input-sm jumlah_exudate" name="LukaBakarForm[jumlah_exudate]">
                                                    <span class="input-group-addon">cc</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-warna_exudate">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-warna_exudate">Warna</label>
                                            <div class="col-sm-9">
                                                <input type="text" class="form-control input-sm warna_exudate" name="LukaBakarForm[warna_exudate]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-bau_exudate">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-bau_exudate">Bau</label>
                                            <div class="col-sm-9">
                                                <input type="text" style="margin-bottom:10px;" class="form-control input-sm bau_exudate" name="LukaBakarForm[bau_exudate]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                        <p style="font-weight:bold;">10. Pembersihan Luka/Balutan </p>
                        <p>
                            <div class="col-sm-12">
                                <div class="row">
                                    <div class="col-sm-6">
                                        <div class="form-group highlight-addon field-lukabakarform-pembersihan_luka">
                                            <label class="control-label has-star col-sm-3" for="lukabakarform-pembersihan_luka">&nbsp;</label>
                                            <div class="col-sm-9">
                                                <input type="text" style="margin-bottom:10px;" class="form-control input-sm pembersihan_luka" name="LukaBakarForm[pembersihan_luka]">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </p>
                    </td>
                    <td></td>
                    <td style="text-align: center;">
                        <button type="button" class="btn btn-success addRowCatatanLuka" name="addRowCatatanLuka"
                            id="addRowCatatanLuka">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<?php
$this->registerJs('
var _catatan_luka = ' . json_encode($model->catatan_luka) . ';
var _pegawaiId = "'.$pegawaiId.'";
var _pegawaiNama = "'.$pegawaiNama.'";

$(document).ready(function(){
    if (_catatan_luka) {
        // $(".table-catatan-luka tbody tr:first").remove();
        let rowDataCatatanLuka;
        let rowCountCatatan = 0;
        $.each(_catatan_luka, function(index, value) {
            rowCountCatatan++;
            let _btnContent = rowCountCatatan === 0
                ? `<button type="button" class="btn btn-success addRowCatatanLuka" name="addRowCatatanLuka">
                        <i class="fa fa-plus"></i>
                    </button>`
                :
                `<button type="button" class="btn btn-danger deleteRowCatatanLuka ms-1">
                    <i class="fa fa-trash"></i>
                </button>`;

            let __terluka = value.terluka
            let __kulit_luka = value.kulit_luka
            let __stadium_luka = value.stadium_luka
            let __assesmentDate = value.tanggal_assesmen
            let __pegawai_id = value.pegawai_assesmen
            let __pegawai_nama = value.pegawai_assesmen_nama

            let __terlukaText = setTerluka(__terluka)
            let __kulitLukaText = setKulitLuka(__kulit_luka)
            let __stadiumLukaText = setStadiumLuka(__stadium_luka)
            let __assesmentDateText = getAssesmentDate(__assesmentDate, true)

            rowDataCatatanLuka = `
                <tr>
                    <td>
                        <div class="row" style="margin-left:10px;">
                            <p style="font-weight:bold;">1. Ukuran Luka</p>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>Panjang : ${value.panjang_luka} cm</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[panjang_luka][]" value="${value.panjang_luka}">
                                </div>
                                <div class="col-sm-6">
                                    <p>Lebar : ${value.lebar_luka} cm</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[lebar_luka][]" value="${value.lebar_luka}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>Kedalaman : ${value.kedalaman_luka} cm</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[kedalaman_luka][]" value="${value.kedalaman_luka}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <p style="font-weight:bold;">2. Goa/Sinus Metode Arah Jarum Jam (cm)</p>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p style="font-weight:bold;">Undermining/Goa</p>
                                    <p>Lokasi : ${value.lokasi_goa} cm</p>
                                    <p>Panjang : ${value.panjang_goa} cm</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[lokasi_goa][]" value="${value.lokasi_goa}">
                                    <input type="hidden" class="form-control" name="LukaBakarForm[panjang_goa][]" value="${value.panjang_goa}">
                                </div>
                                <div class="col-sm-6">
                                    <p style="font-weight:bold;">Saluran/Sinus</p>
                                    <p>Lokasi : ${value.lokasi_sinus} cm</p>
                                    <p>Panjang : ${value.panjang_sinus} cm</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[lokasi_sinus][]" value="${value.lokasi_sinus}">
                                    <input type="hidden" class="form-control" name="LukaBakarForm[panjang_sinus][]" value="${value.panjang_sinus}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <p style="font-weight:bold;">3. Dasar Luka (%)</p>
                            <p>Warna Type Jaringan</p>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>Hitam : ${value.hitam} %</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[hitam][]" value="${value.hitam}">
                                </div>
                                <div class="col-sm-6">
                                    <p>Kuning : ${value.kuning} %</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[kuning][]" value="${value.kuning}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>Merah : ${value.merah} %</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[merah][]" value="${value.merah}">
                                </div>
                                <div class="col-sm-6">
                                    <p>Pink : ${value.pink} %</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[pink][]" value="${value.pink}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <p style="font-weight:bold;">4. Terluka</p>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>Menyatu Dengan Dasar Luka : ${__terlukaText} </p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[terluka][]" value="${__terluka}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <p style="font-weight:bold;">5. Kulit Sekitar Luka</p>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>${__kulitLukaText} </p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[kulit_luka][]" value="${__kulit_luka}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <p style="font-weight:bold;">6. Stadium Luka</p>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>${__stadiumLukaText} </p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[stadium_luka][]" value="${__stadium_luka}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <p style="font-weight:bold;">7. Tanda-Tanda Infeksi</p>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>${value.tanda_infeksi} </p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[tanda_infeksi][]" value="${value.tanda_infeksi}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <p style="font-weight:bold;">8. Nyeri Luka</p>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>Skala : ${value.nyeri_luka} </p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[nyeri_luka][]" value="${value.nyeri_luka}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <p style="font-weight:bold;">9. Exudate</p>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>Jumlah : ${value.jumlah_exudate} cc</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[jumlah_exudate][]" value="${value.jumlah_exudate}">
                                </div>
                                <div class="col-sm-6">
                                    <p>Warna : ${value.warna_exudate} </p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[warna_exudate][]" value="${value.warna_exudate}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>Bau : ${value.bau_exudate} </p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[bau_exudate][]" value="${value.bau_exudate}">
                                </div>
                            </div>
                        </div>
                        <div class="row" style="margin-left:10px;">
                            <p style="font-weight:bold;">10. Pembersihan Luka/Balutan</p>
                            <div class="col-sm-12">
                                <div class="col-sm-6">
                                    <p>${value.pembersihan_luka}</p>
                                    <input type="hidden" class="form-control" name="LukaBakarForm[pembersihan_luka][]" value="${value.pembersihan_luka}">
                                </div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <p>Waktu Input Assesmen : ${__assesmentDateText}</p>
                        <p>Kategori Pegawai/Dokter Nama User/ : ${__pegawai_nama}</p>
                        <input type="hidden" class="form-control" name="LukaBakarForm[tanggal_assesmen][]" value="${__assesmentDate}">
                        <input type="hidden" class="form-control" name="LukaBakarForm[pegawai_assesmen][]" value="${__pegawai_id}">
                        <input type="hidden" class="form-control" name="LukaBakarForm[pegawai_assesmen_nama][]" value="${__pegawai_nama}">
                    </td>
                    <td style="text-align: center;">
                        ${_btnContent}
                    </td>
                </tr>
            `;

            $(".table-catatan-luka tbody").append(rowDataCatatanLuka);
        })
    }

    $("table tbody").on("click", ".addRowCatatanLuka", function () {
        var _today = new Date()
        var _years = _today.getFullYear();
        var _month = _today.getMonth() + 1;
        var _day = _today.getDate();

        if (_day < 10) _day = "0" + _day;
        if (_month < 10) _month = "0" + _month;

        var _todays = _years + "-" + _month + "-" + _day;
        var currentDate = getAssesmentDate(_today, false)

        const $tableCatatan = $(this).closest("table");
        const panjangLuka = $(".panjang_luka").val()
        const lebarLuka = $(".lebar_luka").val()
        const kedalamanLuka = $(".kedalaman_luka").val()
        const lokasiGoa = $(".lokasi_goa").val()
        const panjangGoa = $(".panjang_goa").val()
        const lokasiSinus = $(".lokasi_sinus").val()
        const panjangSinus = $(".panjang_sinus").val()
        const hitam = $(".hitam").val()
        const kuning = $(".kuning").val()
        const merah = $(".merah").val()
        const pink = $(".pink").val()
        const tanda_infeksi = $(".tanda_infeksi").val()
        const nyeri_luka = $(".nyeri_luka").val()
        const jumlah_exudate = $(".jumlah_exudate").val()
        const warna_exudate = $(".warna_exudate").val()
        const bau_exudate = $(".bau_exudate").val()
        const pembersihan_luka = $(".pembersihan_luka").val()
        
        let terluka = $(".terluka:checked").val()
        let kulit_luka = $(".kulit_luka:checked").val()
        let stadium_luka = $(".stadium_luka:checked").val()

        terluka = (terluka && typeof terluka != "undefined") ? terluka : ""
        kulit_luka = (kulit_luka && typeof kulit_luka != "undefined") ? kulit_luka : ""
        stadium_luka = (stadium_luka && typeof stadium_luka != "undefined") ? stadium_luka : ""
        
        let terlukaText = setTerluka(terluka)
        let kulitLukaText = setKulitLuka(kulit_luka)
        let stadiumLukaText = setStadiumLuka(stadium_luka)

        const newRowCatatanLuka = `
            <tr style="margin-bottom:5px;margin-top:30px;">
                <td>
                    <div class="row" style="margin-left:10px;">
                        <p style="font-weight:bold;">1. Ukuran Luka</p>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>Panjang : ${panjangLuka} cm</p>
                            </div>
                            <div class="col-sm-6">
                                <p>Lebar : ${lebarLuka} cm</p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>Kedalaman : ${kedalamanLuka} cm</p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <p style="font-weight:bold;">2. Goa/Sinus Metode Arah Jarum Jam (cm)</p>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p style="font-weight:bold;">Undermining/Goa</p>
                                <p>Lokasi : ${lokasiGoa} cm</p>
                                <p>Panjang : ${panjangGoa} cm</p>
                            </div>
                            <div class="col-sm-6">
                                <p style="font-weight:bold;">Saluran/Sinus</p>
                                <p>Lokasi : ${lokasiSinus} cm</p>
                                <p>Panjang : ${panjangSinus} cm</p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <p style="font-weight:bold;">3. Dasar Luka (%)</p>
                        <p>Warna Type Jaringan</p>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>Hitam : ${hitam} %</p>
                            </div>
                            <div class="col-sm-6">
                                <p>Kuning : ${kuning} %</p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>Merah : ${merah} %</p>
                            </div>
                            <div class="col-sm-6">
                                <p>Pink : ${pink} %</p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <p style="font-weight:bold;">4. Terluka</p>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>Menyatu Dengan Dasar Luka : ${terlukaText} </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <p style="font-weight:bold;">5. Kulit Sekitar Luka</p>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>${kulitLukaText} </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <p style="font-weight:bold;">6. Stadium Luka</p>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>${stadiumLukaText} </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <p style="font-weight:bold;">7. Tanda-Tanda Infeksi</p>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>${tanda_infeksi} </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <p style="font-weight:bold;">8. Nyeri Luka</p>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>Skala : ${nyeri_luka} </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <p style="font-weight:bold;">9. Exudate</p>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>Jumlah : ${jumlah_exudate} cc</p>
                            </div>
                            <div class="col-sm-6">
                                <p>Warna : ${warna_exudate} </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>Bau : ${bau_exudate} </p>
                            </div>
                        </div>
                    </div>
                    <div class="row" style="margin-left:10px;">
                        <p style="font-weight:bold;">10. Pembersihan Luka/Balutan</p>
                        <div class="col-sm-12">
                            <div class="col-sm-6">
                                <p>${pembersihan_luka}</p>
                            </div>
                        </div>
                    </div>

                    <input type="hidden" class="form-control" name="LukaBakarForm[panjang_luka][]" value="${panjangLuka}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[lebar_luka][]" value="${lebarLuka}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[kedalaman_luka][]" value="${kedalamanLuka}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[lokasi_goa][]" value="${lokasiGoa}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[panjang_goa][]" value="${panjangGoa}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[lokasi_sinus][]" value="${lokasiSinus}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[panjang_sinus][]" value="${panjangSinus}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[hitam][]" value="${hitam}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[kuning][]" value="${kuning}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[merah][]" value="${merah}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[pink][]" value="${pink}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[terluka][]" value="${terluka}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[kulit_luka][]" value="${kulit_luka}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[stadium_luka][]" value="${stadium_luka}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[tanda_infeksi][]" value="${tanda_infeksi}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[nyeri_luka][]" value="${nyeri_luka}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[jumlah_exudate][]" value="${jumlah_exudate}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[warna_exudate][]" value="${warna_exudate}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[bau_exudate][]" value="${bau_exudate}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[pembersihan_luka][]" value="${pembersihan_luka}">
                </td>
                <td>
                    <p>Waktu Input Assesmen : ${currentDate}</p>
                    <p>Kategori Pegawai/Dokter Nama User/ : ${_pegawaiNama}</p>
                    <input type="hidden" class="form-control" name="LukaBakarForm[tanggal_assesmen][]" value="${_todays}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[pegawai_assesmen][]" value="${_pegawaiId}">
                    <input type="hidden" class="form-control" name="LukaBakarForm[pegawai_assesmen_nama][]" value="${_pegawaiNama}">
                </td>
                <td style="text-align: center;">
                    <button type="button" class="btn btn-danger deleteRowCatatanLuka ms-1">
                        <i class="fa fa-trash"></i>
                    </button>
                </td>
            </tr>
        `;
        
        $tableCatatan.find("tbody").append(newRowCatatanLuka);
        resetForm()
    });

    $("table tbody").on("click", ".deleteRowCatatanLuka", function () {
        const $tableCatatan = $(this).closest("table");
        $(this).closest("tr").remove();
    });

    function resetForm() {
        $(".panjang_luka").val(null)
        $(".lebar_luka").val(null)
        $(".kedalaman_luka").val(null)

        $(".lokasi_goa").val(null)
        $(".panjang_goa").val(null)
        $(".lokasi_sinus").val(null)
        $(".panjang_sinus").val(null)

        $(".hitam").val(null)
        $(".kuning").val(null)
        $(".merah").val(null)
        $(".pink").val(null)

        $(".terluka").prop("checked", false).trigger("change")
        $(".kulit_luka").prop("checked", false).trigger("change")
        $(".stadium_luka").prop("checked", false).trigger("change")
        $(".tanda_infeksi").val(null).trigger("change")
        $(".nyeri_luka").val(null).trigger("change")
        $(".jumlah_exudate").val(null)
        $(".warna_exudate").val(null).trigger("change")
        $(".bau_exudate").val(null).trigger("change")
        $(".pembersihan_luka").val(null).trigger("change")
    }

    function setTerluka(data) {
        let __terluka = data
        let __terlukaText = ""
        if(__terluka == "1") {
            __terlukaText = "Ya"
        }
        else if(__terluka == "0") {
            __terlukaText = "Tidak"
        }
        else {
            __terlukaText = ""
        }

        return __terlukaText
    }

    function setKulitLuka(data) {
        let __kulit_luka = data
        let __kulitLukaText = ""
        if(__kulit_luka == "1") {
            __kulitLukaText = "Utuh"
        }
        else if(__kulit_luka == "2") {
            __kulitLukaText = "Edema"
        }
        else if(__kulit_luka == "3") {
            __kulitLukaText = "Erythema/Merah"
        }
        else if(__kulit_luka == "4") {
            __kulitLukaText = "Maceration/Lecet"
        }
        else {
            __kulitLukaText = ""
        }

        return __kulitLukaText
    }

    function setStadiumLuka(data) {
        let __stadium_luka = data
        let __stadiumLukaText = ""
        if(__stadium_luka == "1") {
            __stadiumLukaText = "1"
        }
        else if(__stadium_luka == "2") {
            __stadiumLukaText = "2"
        }
        else if(__stadium_luka == "3") {
            __stadiumLukaText = "3"
        }
        else if(__stadium_luka == "4") {
            __stadiumLukaText = "4"
        }
        else {
            __stadiumLukaText = ""
        }

        return __stadiumLukaText
    }

    function getAssesmentDate(date, custom = false) {
        var assesmentDate = years = month = day = ""
        var months = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];

        if(!custom) {
            years = date.getFullYear();
            month = date.getMonth();
            day = date.getDate();
        }
        else {
            assesmentDateSplit = date.split("-")
            years = assesmentDateSplit[0];
            month = parseInt(assesmentDateSplit[1] - 1);
            day = assesmentDateSplit[2];
        }
        
        if (day < 10) day = "0" + day;

        return day + " " + months[month] + " " + years;
    }
})
', View::POS_END);
?>
