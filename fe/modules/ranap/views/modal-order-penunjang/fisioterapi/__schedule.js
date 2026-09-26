/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-05-29
 */
// NOTE : 
// Code yang ini sama dengan yang rajal nya juga, jadi kalau ada perubahan disini harap di rajal nya juga disamakan (Copy Paste). 
// Kecuali variable getUrlSchedule (Ini disesuaikan)
// Location File modules/rajal/views/modal-order-penunjang/fisioterapi/__schedule.js

var { fieldName } = phpVars;
var arrJadwal = [];
var getUrlSchedule = (params) => `/ranap/pemeriksaan-rawat-inap/get-data-terapi-fisio${params}`

$(function () {
    // Run Initial Function
    $(".pickadate-input").pickadate({
        applyClass: "bg-slate-600",
        cancelClass: "btn-default",
        format: "dd-mm-yyyy",
    });
    $(`.picker__holder`).removeClass(`picker_modal-two`);
    $(`.picker__holder`).addClass(`picker_modal-one`);
    setMinValueDateAllSchedule();

    $(".pickadate-input").on("change", handlerPickerDateInput);

    $(".jam_mulai").on("change", handlerJamMulai);

    $(".jam_selesai").on("change", handlerJamSelesai);
});

function handlerPickerDateInput() {
    var dataKe = $(this).attr("data-ke");
    var nextDataKe = Number(dataKe) + 1;
    var tanggalTerapi = $(this).val();
    if (!tanggalTerapi || typeof tanggalTerapi == 'undefined') return false;
    setMinValueDateAllSchedule();
    setLoadingSchedule();
    var prevDataKe = parseInt(dataKe) - 1;
    var prevSelesai = null;
    var formatedTimesSelesai = null;
    $(`#jamMulai-${dataKe}`).val("08:00");
    $(`#jamSelesai-${dataKe}`).val("09:00");
    if (prevDataKe >= 0) {
        var prevTgl = $(`#schedule-${prevDataKe}`).pickadate("get");
        if (prevTgl == tanggalTerapi) {
            prevMulai = $(`#jamMulai-${prevDataKe}`).val();
            prevSelesai = $(`#jamSelesai-${prevDataKe}`).val();
            formatedTimesSelesai = prevSelesai.toString().split(":");
            let tempMulai = parseInt(formatedTimesSelesai[0]) + 1;
            if (tempMulai >= 24) {
                tempMulai = tempMulai - 24;
            }
            let tempSelesai = parseInt(formatedTimesSelesai[0]) + 2;
            if (tempSelesai >= 24) {
                tempSelesai = tempSelesai - 24;
            }
            $(`#jamMulai-${dataKe}`).val(addLeadingZeros(String(tempMulai)) + ":" + formatedTimesSelesai[1]);
            $(`#jamSelesai-${dataKe}`).val(addLeadingZeros(String(tempSelesai)) + ":" + formatedTimesSelesai[1]);
        }
    }
    // Ambil data semua jadwal untuk validasi
    // Auto Set Date with Interval
    arrJadwal = [];
    for (
        let nextData = 0;
        nextData < maksFrekuensi;
        nextData++
    ) {
        var curTgl = $(`#schedule-${nextData}`).val();
        var curMulai = $(`#jamMulai-${nextData}`).val();
        var curSelesai = $(`#jamSelesai-${nextData}`).val();
        if (curTgl != null && curMulai != null) {
            if (arrJadwal[curTgl] != null) {
                arrJadwal[curTgl].push([curMulai, curSelesai]);
            } else {
                arrJadwal[curTgl] = [[curMulai, curSelesai]];
            }
        }
    }
    setLoadingSchedule(false);
    var scrollTo = document.getElementById(`schedule-${dataKe}`);
    scrollTo.scrollIntoView();
    for (
        let nextData = parseInt(dataKe);
        nextData < maksFrekuensi;
        nextData++
    ) {
        var nextElement = $(`#schedule-${nextData + 1}`);
        var currentDate = $(`#schedule-${dataKe}`).pickadate("get");
        var currentDate = $(`#schedule-${dataKe}`).pickadate("get");
        var namaHari = moment(currentDate, "DD-MM-YYYY").format('dddd');
        $(`#hari-${dataKe}`).val(namaHari);
        if (nextElement.length > 0) {
            var prevVal = $(`#schedule-${dataKe}`).pickadate("get");
            var nextDate = $(`#schedule-${nextData + 1}`).pickadate("get");
            var currentDateMoment = moment(currentDate, "DD-MM-YYYY");
            var nextDateMoment = moment(nextDate, "DD-MM-YYYY");
            let isGreaterThan = currentDateMoment.isSameOrAfter(nextDateMoment)
            let counterClick = 1
            let isDailyBool = isDaily == 0 ? false : true
            if (isGreaterThan && counterClick == 1) {
                counterClick++
                var counter = 1
                var arrDate = []
                var frekuensiIndex = frekuensi - 1
                if (isDailyBool) {
                    let countDaily = 1
                    do {
                        let dailyDay = moment(currentDate, "DD-MM-YYYY").add(countDaily, 'days');
                        let dailyDate = dailyDay.format('DD-MM-YYYY')
                        $(`#schedule-${nextDataKe}`).pickadate("set").set("select", `${dailyDate}`)
                        var newDaily = moment(dailyDate, "DD-MM-YYYY").format('dddd');
                        $(`#hari-${nextDataKe}`).val(newDaily);
                        countDaily++
                        nextDataKe++
                    } while (nextDataKe != frekuensi);
                } else {
                    do {
                        let incrementDay = moment(currentDate, "DD-MM-YYYY").add(counter, 'days');
                        let date = incrementDay.format('DD-MM-YYYY')
                        var dayName = incrementDay.locale("En").format('ddd');
                        if (days.some(item => item == dayName)) {
                            arrDate.push(date)
                        }
                        counter++
                    } while (arrDate.length != frekuensiIndex - dataKe);
                    if (nextDataKe != frekuensi) {
                        let i = 0
                        do {
                            $(`#schedule-${nextDataKe}`).pickadate("set").set("select", `${arrDate[i]}`)
                            var namaHariBaru = moment(arrDate[i], "DD-MM-YYYY").format('dddd');
                            $(`#hari-${nextDataKe}`).val(namaHariBaru);
                            i++
                            nextDataKe++
                        } while (nextDataKe != frekuensi);
                    }
                }
                $(`#jamMulai-${nextData + 1}`).val("08:00");
                $(`#jamSelesai-${nextData + 1}`).val("09:00");
            }
        }
    }
}

function handlerJamMulai() {
    let dataKe = $(this).attr("data-ke");
    const dataKeInt = parseInt(dataKe);
    // By Pass Time Validation
    checkValidationTimeCurrentDateOnly({ dataKeInt, isJamSelesaiValidation: false });
}

function handlerJamSelesai() {
    let dataKe = $(this).attr("data-ke");
    const dataKeInt = parseInt(dataKe);
    // By Pass Time Validation
    checkValidationTimeCurrentDateOnly({ dataKeInt, isJamSelesaiValidation: true });
}

function setMinValueDateAllSchedule() {
    for (let currentCounter = 0; currentCounter < frekuensi; currentCounter++) {
        const prevDataKe = currentCounter - 1;
        if (currentCounter != 0) {
            const prevVal = $(`#schedule-${prevDataKe}`).pickadate("get");
            const prevValStringFormat = moment(prevVal, "DD-MM-YYYY").format('YYYY-MM-DD');
            const formatedDate = new Date(prevValStringFormat);
            $(`#schedule-${currentCounter}`)
                .pickadate("set")
                .set("min", [
                    formatedDate.getFullYear(),
                    formatedDate.getMonth(),
                    formatedDate.getDate(),
                ]);
        }
    }
}

function setLoadingSchedule(loading = true) {
    if (loading == true) {
        $("#tbl-schedule").hide();
        $("#loading-schedule").html(`<h3 style="text-align:center;">
          <i class="icon-spinner4 spinner position-center"></i>
          &nbsp;&nbsp;<b> Memuat... </b>
          </h3>`);
    } else {
        $("#tbl-schedule").show();
        $("#loading-schedule").html("");
    }
}

async function getDataSchedule(tglTerapi = null) {
    var terapis = pegawaiId;
    var initialValue = null;
    var params =
        tglTerapi && terapis
            ? `?tgl_penjadwalan_awal=${tglTerapi}&pegawai_id=${terapis}`
            : "";
    await $.ajax({
        type: "GET",
        url: getUrlSchedule(params),
        contentType: "application/json",
        success: function (res) {
            initialValue = res;
        },
    });
    return initialValue;
}

function addLeadingZeros(str, targetLength = 2) {
    return str.padStart(targetLength, '0');
}

function checkValidationTimeCurrentDateOnly({
    dataKeInt,
    isJamSelesaiValidation = false
}) {
    let currentDateVal = $(`#schedule-${dataKeInt}`).pickadate("get");
    // current time element, depends on isJamSelesaiValidation
    const currentTimeStartElement = $(`#jamMulai-${dataKeInt}`);
    const currentTimeEndElement = $(`#jamSelesai-${dataKeInt}`);
    const currentTimeStartVal = currentTimeStartElement.val();
    const currentTimeEndVal = currentTimeEndElement.val();
    // Resetter Element, depends on isJamSelesaiValidation
    let resetCurrentElement = () => $(`#jamMulai-${dataKeInt}`).val('');
    if (isJamSelesaiValidation) {
        resetCurrentElement = () => $(`#jamSelesai-${dataKeInt}`).val('');
    }
    // Validation and force setVal
    if (isJamSelesaiValidation) {
        if (!currentTimeEndVal) {
            resetCurrentElement();
            docoNotification(
                "error",
                "Proses Gagal",
                "Silahkan Cek Inputan. Waktu mulai tidak boleh kosong"
            );
            return false;
        }
        const isInvalidSameDateTime = currentTimeStartVal > currentTimeEndVal;
        if (isInvalidSameDateTime) {
            resetCurrentElement();
            docoNotification(
                "error",
                "Proses Gagal",
                "Silahkan Cek Inputan. Waktu selesai harus lebih dari waktu mulai"
            );
            return false;
        }
    } else {
        const formatedTimeAwal = currentTimeStartVal.toString().split(":");
        let tempAkhir = parseInt(formatedTimeAwal[0]) + 1;
        if (tempAkhir >= 24) {
            tempAkhir = tempAkhir - 24;
        }
        currentTimeEndElement.val(addLeadingZeros(String(tempAkhir)) + ":" + formatedTimeAwal[1]);
    }
}

function checkValidationTime({
    dataKeInt,
    isJamSelesaiValidation = false
}) {
    let hasNotifiable = false;
    let currentDateVal = $(`#schedule-${dataKeInt}`).pickadate("get");
    // current time element, depends on isJamSelesaiValidation
    let currentTimeElement = $(`#jamMulai-${dataKeInt}`);
    if (isJamSelesaiValidation) {
        currentTimeElement = $(`#jamSelesai-${dataKeInt}`);
    }
    const currentTimeVal = currentTimeElement.val();
    const listScheduleDates = [];
    for (let index = 0; index <= frekuensi; index++) {
        const tempCounterDateVal = $(`#schedule-${index}`).pickadate("get");
        listScheduleDates.push(tempCounterDateVal);
    }
    for (let counterIndexData = 0; counterIndexData <= frekuensi; counterIndexData++) {
        const isIndexLess = dataKeInt > counterIndexData;
        const isIndexGreater = dataKeInt < counterIndexData;
        const isIndexSame = dataKeInt == counterIndexData;
        const tempCounterDateVal = $(`#schedule-${counterIndexData}`).pickadate("get");
        let tempCounterJamMulaiElement = $(`#jamMulai-${counterIndexData}`);
        let tempCounterJamMulaiVal = $(`#jamMulai-${counterIndexData}`).val();
        let tempCounterJamSelesaiElement = $(`#jamSelesai-${counterIndexData}`);
        let tempCounterJamSelesaiVal = $(`#jamSelesai-${counterIndexData}`).val();

        let tempPrevJamMulaiElement = $(`#jamMulai-${counterIndexData - 1}`);
        let tempPrevJamMulaiVal = $(`#jamMulai-${counterIndexData - 1}`).val();
        let tempPrevJamSelesaiElement = $(`#jamSelesai-${counterIndexData - 1}`);
        let tempPrevJamSelesaiVal = $(`#jamSelesai-${counterIndexData - 1}`).val();

        let tempNextJamMulaiElement = $(`#jamMulai-${counterIndexData + 1}`);
        let tempNextJamMulaiVal = $(`#jamMulai-${counterIndexData + 1}`).val();
        let tempNextJamSelesaiElement = $(`#jamSelesai-${counterIndexData + 1}`);
        let tempNextJamSelesaiVal = $(`#jamSelesai-${counterIndexData + 1}`).val();

        // Resetter Element, depends on isJamSelesaiValidation
        let resetCurrentElement = () => $(`#jamMulai-${dataKeInt}`).val('');
        if (isJamSelesaiValidation) {
            resetCurrentElement = () => $(`#jamSelesai-${dataKeInt}`).val('');
        }

        // Tidak Terpakai (onChange Force Add 1 Hour)
        if (tempCounterJamSelesaiVal) {
            let formatedTimes = tempCounterJamSelesaiVal.toString().split(":");
            let tempMulai = parseInt(formatedTimes[0]) + 1;
            if (tempMulai >= 24) {
                tempMulai = tempMulai - 24;
            }
            const tempValForce = addLeadingZeros(String(tempMulai)) + ":" + formatedTimes[1];
        }

        // Check is Duplicate and must be re-check times
        let isDuplicateDate = false;
        let isDuplicateDateLastIndex = false;
        let isDuplicateDateFirstIndex = false;
        let duplicateDateIndexes = [];
        listScheduleDates.filter(function (scheduleItem, scheduleIndex) {
            if (scheduleItem == tempCounterDateVal) {
                duplicateDateIndexes.push(scheduleIndex);
            }
        });
        if (duplicateDateIndexes.length > 1) {
            const tempArrData = [...duplicateDateIndexes];
            const tempLastIndexDuplicate = tempArrData.pop();
            isDuplicateDateFirstIndex = duplicateDateIndexes[0];
            isDuplicateDateLastIndex = tempLastIndexDuplicate == counterIndexData;
            isDuplicateDate = true;
        }
        // End Check

        const validationJamSelesai = () => {
            if (isIndexSame) {
                // Only Same Date
                const isSmallerThanMulai = (currentTimeVal < tempCounterJamMulaiVal);
                if (tempCounterJamMulaiVal && isSmallerThanMulai) {
                    resetCurrentElement();
                    hasNotifiable = true;
                }
            }
        }

        const validationJamMulai = () => {
            if (isIndexSame) {
                // Only Same Date
                const isLargerThanSelesai = (currentTimeVal > tempCounterJamSelesaiVal);
                if (tempCounterJamSelesaiVal && isLargerThanSelesai) {
                    resetCurrentElement();
                    hasNotifiable = true;
                }
            }
        }

        if (currentDateVal == tempCounterDateVal) {
            // Run main Validation
            if (isIndexSame && isDuplicateDate) {
                // Lintas Date (Index Same)
                // Lower Than Prev
                const isNotUndefinedPrev = typeof tempPrevJamMulaiVal != 'undefined' && typeof tempPrevJamSelesaiVal != 'undefined';
                const isLowerThanPrev = (currentTimeVal < tempPrevJamMulaiVal) || (currentTimeVal < tempPrevJamSelesaiVal);
                // Greater Than Next
                const isNotUndefinedNext = typeof tempNextJamMulaiVal != 'undefined' && typeof tempNextJamSelesaiVal != 'undefined';
                const isGreaterThanNext = (currentTimeVal > tempNextJamMulaiVal) || (currentTimeVal > tempNextJamSelesaiVal);
                // Combine 2
                const isLowerWhereClause = isNotUndefinedPrev && isLowerThanPrev && !isDuplicateDateFirstIndex;
                const isGreaterWhereClause = isNotUndefinedNext && isGreaterThanNext && !isDuplicateDateLastIndex;
                if (isLowerWhereClause || isGreaterWhereClause) {
                    resetCurrentElement();
                    hasNotifiable = true;
                }
            }
            if (isIndexLess && isDuplicateDate) {
                const isNotUndefined = typeof tempCounterJamMulaiVal != 'undefined' && typeof tempCounterJamSelesaiVal != 'undefined';
                const isLowerThanPrev = (currentTimeVal < tempCounterJamMulaiVal) || (currentTimeVal < tempCounterJamSelesaiVal);
                if (isNotUndefined && isLowerThanPrev) {
                    resetCurrentElement();
                    hasNotifiable = true;
                }
            }
            if (isIndexGreater && isDuplicateDate) {
                const isNotUndefined = typeof tempCounterJamMulaiVal != 'undefined' && typeof tempCounterJamSelesaiVal != 'undefined';
                const isBiggerThanNext = (currentTimeVal > tempCounterJamMulaiVal) || (currentTimeVal > tempCounterJamSelesaiVal);
                if (isNotUndefined && isBiggerThanNext) {
                    resetCurrentElement();
                    hasNotifiable = true;
                }
            }
            // Specific Validation For Each Time
            if (isJamSelesaiValidation) {
                validationJamSelesai();
            } else {
                validationJamMulai();
            }
        }
    }
    return hasNotifiable;
}