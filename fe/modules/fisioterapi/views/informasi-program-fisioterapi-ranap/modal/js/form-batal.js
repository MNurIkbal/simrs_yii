$(document).on("click", "#simpan-batal-program", () => {
   var payload = $("#form-batal").serializeArray()
   $().docoForm('click',{
      url: '/fisioterapi/informasi-program-fisioterapi-ranap/batal-program',
      data: payload,
      confirmMessage: 'Apakah Anda yakin akan membatalkan pemeriksaan ini?',
      success: () => {
         $('#modalProgramTerapi').modal('hide')
         $('#toolbar-cari').click()
      }
   });
});

$(".dataTables_filter").hide();
dateRangeHelper(".startDate");