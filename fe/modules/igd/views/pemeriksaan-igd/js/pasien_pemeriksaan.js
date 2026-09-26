/*
* @Author: Sigit
* @Date:   2018-08-13 10:23:23
*/

var pageFormDataValues = [];
var pageFormId = null;

$(document).ready(function() {
    setTimeout(() => {
        $('#tab-asesmen-dpjp a').trigger('click')
    }, 100);

    $(".nav-tabs.nav-tab-periksa a").bind("click", allowChangeMenu)
});

/*
* Trigger docoload menu di periksa pelayanan
*/
function runPeriksaMenuAction (menuId) {
    switch (menuId) {
        case 'tab-formulir-triase':
            $("#content-formulir-triase").docoLoad({
                url: "/igd/pemeriksaan-igd/formulir-triase?id="+pendaftaran_id,
                dataType: 'html',
                success : function(data) {

                }
            });
            break;
        case 'tab-asesmen-keperawatan':
            $("#content-asesmen-keperawatan").docoLoad({
                url: `/igd/pemeriksaan-igd/form-asesmen-keperawatan?id=${pendaftaran_id}`,
                dataType: 'html',
                success : function(data) {

                }
            });
            break;
        case 'tab-asesmen-medis':
            $("#content-asesmen-medis").docoLoad({
                url: "/igd/pemeriksaan-igd/form-asesmen-medis?id="+pendaftaran_id,
                dataType: 'html',
                success : function(data) {

                }
            });
            break;

        case 'tab-nursing-note':
            $("#content-nursing-note").docoLoad({
                url: "/igd/pemeriksaan-igd/nursing-note?id="+pendaftaran_id,
                dataType: 'html',
                success : function(data) {

                }
            });
            break;

        case 'tab-asesmen-dokter':
            $("#content-asesmen-dokter").docoLoad({
                url: "/igd/pemeriksaan-igd/asesmen-dokter?id="+pendaftaran_id,
                dataType: 'html',
                success : function(data) {

                }
            });
            break;

        case 'tab-asesmen-dpjp':
            $("#content-asesmen-dpjp").docoLoad({
                url: "/igd/pemeriksaan-igd/asesmen-dpjp?id="+pendaftaran_id,
                dataType: 'html',
                success : function(data) {

                }
            });
            break;

        case 'tab-implementasi':
            $("#content-implementasi").docoLoad({
                url: "/igd/pemeriksaan-igd/implementasi?id="+pendaftaran_id,
                dataType: 'html',
                success : function(data) {

                }
            });
            break;

        case 'tab-retur':
            $("#content-retur").docoLoad({
                url: "/igd/pemeriksaan-igd/retur-obat?id="+pendaftaran_id,
                dataType: 'html',
                success : function(data) {

                }
            });
            break;

        case 'tab-partograf':
            $("#content-partograf").docoLoad({
              url:"/igd/pemeriksaan-igd/partograf?id=" + pendaftaran_id + "&pasien_id=" + pasien_id,
              dataType: "html",
              success: function (data) {

              },
            });
            break;

        case 'tab-kesimpulan':
            $("#content-kesimpulan").docoLoad({
                url: "/igd/pemeriksaan-igd/kesimpulan?id="+pendaftaran_id,
                dataType: 'html',
                success : function(data) {

                }
            });
            break;

        case 'tab-resume':
            $("#content-resume").docoLoad({
                url: "/igd/pemeriksaan-igd/resume-medis?id="+pendaftaran_id,
                dataType: 'html',
                success : function(data) {

                }
            });
            break;

        case 'tab-upload-dokumen':
            $("#content-upload-dokumen").docoLoad({
                url:"/api/upload-dokumen/tab-upload-dokumen?id="+pendaftaran_id,
                dataType:"html",
                success: function(data){},
            });
            break;

        case 'tab-permintaan-makan':
            $("#content-permintaan-makan").docoLoad({
                url: "/igd/pemeriksaan-igd/permintaan-makan?id="+pendaftaran_id,
                dataType: 'html',
                success : function(data) {
                    $(' .select2 ').select2()
                }
            });
            break;

        case 'tab-cathlab-koroangiografi':
            $("#content-cathlab").docoLoad({
                url: "/igd/pemeriksaan-igd/cathlab?id="+pendaftaran_id+"&tipe=koroangiografi",
                dataType: 'html',
                success : function(data) {
                }
            });
            break;

        case 'tab-cathlab-pci':
            $("#content-cathlab").docoLoad({
                url: "/igd/pemeriksaan-igd/cathlab?id="+pendaftaran_id+"&tipe=pci",
                dataType: 'html',
                success : function(data) {
                }
            });
            break;

        case 'tab-cathlab-dsa':
            $("#content-cathlab").docoLoad({
                url: "/igd/pemeriksaan-igd/cathlab?id="+pendaftaran_id+"&tipe=dsa",
                dataType: 'html',
                success : function(data) {
                }
            });
            break;

        case 'tab-monitoring-ttv':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            }).toString();

            $("#content-monitoring-ttv").docoLoad({
                url: "/igd/pemeriksaan-igd/monitoring-ttv?" + tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-surat-keterangan':
            $("#content-surat-keterangan").docoLoad({
                url: "/igd/pemeriksaan-igd/surat-keterangan?id=" + pendaftaran_id,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-monitoring-ews':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            }).toString();
            
            $("#content-monitoring-ews").docoLoad({
                url: "/igd/pemeriksaan-igd/observasi-ews?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

        case 'tab-sbar':
            tabUrlParams = new URLSearchParams({
                id: (typeof pendaftaran_id != 'undefined' ? pendaftaran_id : ''),
            }).toString();
            
            $("#content-sbar").docoLoad({
                url: "/igd/pemeriksaan-igd/sbar?"+tabUrlParams,
                dataType: 'html',
                success: function (data) {
                }
            });
            break;

      default:
  }
}


function allowChangeMenu(event) {
    let _this = $(this);
    let defaultData = pageFormDataValues;
    let existingData = (pageFormId != null && pageFormId.length > 0) ? pageFormId.serializeArray() : [];
    let isEqualData = isArrayEqual(defaultData, existingData);
    let skipConfirmTabChange = event.data != undefined ? event.data.skipConfirmTabChange : false;
    if (pageFormDataValues.length == 0 || isEqualData || skipConfirmTabChange) {
        pageFormDataValues = [];
        pageFormId = null;
        runPeriksaMenuAction(_this.parents('li').attr('id')); //get parent untuk kasus igd, karena menu id nya ada di li
    } else {
        event.stopPropagation();
        confirmationDialog("Apakah anda yakin untuk meninggalkan halaman ini?", function (condition) {
            if (condition) {
                pageFormDataValues = [];
                pageFormId = null;
                $(_this).trigger('click', {skipConfirmTabChange: true});
            } else {
                return false;
            }
        });
    }
}
