$(document).ready(() => {
    $(".nav-link").on("click", function () {
        let _type = $(this).attr("data-type");
        switch (_type) {
            case "preanesthic":
                showLoader();
                $(".hide-component").hide();
                $(".hide-component").removeClass('hidden');
                $("#component-preanesthic").show();
                setTimeout(function(){
                    hideLoader();
                }, 400);
                break;
            case "intraoperative":
                showLoader();
                $(".hide-component").hide();
                $(".hide-component").removeClass('hidden');
                $("#component-intraoperativ").show();
                setTimeout(function(){
                    hideLoader();
                }, 400);
                break;
            case "anesthetic":
                showLoader();
                $(".hide-component").hide();
                $(".hide-component").removeClass('hidden');
                $("#component-anesthetic").show();
                setTimeout(function(){
                    hideLoader();
                }, 400);
                break;
            case "patient":
                showLoader();
                $(".hide-component").hide();
                $(".hide-component").removeClass('hidden');
                $("#component-patient").show();
                setTimeout(function(){
                    hideLoader();
                }, 400);
                break;
            case "post":
                showLoader();
                $(".hide-component").hide();
                $(".hide-component").removeClass('hidden');
                $("#component-postoperative").show();
                setTimeout(function(){
                    hideLoader();
                }, 400);
                break;
            default:
                break;
        }
    });

    $("#btn-save-preanesthetic").on("click", function () {
        showNotif();
    });

    $("#btn-save-patientcondition").on("click", function () {
        showNotif();
    });

    $("#btn-save-intraoperative").on("click", function () {
        showNotif();
    });

    $("#btn-save-modal").on("click", function () {
        showNotif();
        $("#modal-bedah").modal('hide');
    });

    $(`.btn-jadwal-list`).on("click", function () {
        setTimeout(() => {
            $("#moda-bedah").css("z-index", "1041");
        }, 10);
    });

    function showNotif() {
        (new PNotify({
            title: "Berhasil",
            text: "Berhasil menyimpan data",
            addclass: "alert alert-success alert-arrow-right alert-styled-right",
            type: "success",
            buttons: {
                closer: true,
                labels: { close: "Fechar", stick: "Manter" }
            },
            hide: false,
            history: {
                history: false
            }
        })).get().on("pnotify.confirm", function () {
            // Print
            window.open("/bedah/anestesi-pasien");
        }).on("pnotify.cancel", function () {

        });
    }
});
