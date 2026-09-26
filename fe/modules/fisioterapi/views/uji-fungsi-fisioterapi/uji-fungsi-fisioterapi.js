$(document).ready(function () {
  $("#btn-save-uji").on("click", function (e) {
    let _form = $("#uji-fungsi-fisioterapi-form").serializeArray();
    
    const tindakanProsedurValues = [];
    
    const tindakanProsedurSelect = $('select[name="UjiFungsiFisioterapiForm[tindakan_prosedur][]"]');
    if (tindakanProsedurSelect.length > 0) {
      const select2Data = tindakanProsedurSelect.select2('data');
      select2Data.forEach(function(item) {
        tindakanProsedurValues.push(item.text || item.id);
      });
    }
    
    _form = _form.filter(function(item) {
      return item.name !== 'UjiFungsiFisioterapiForm[tindakan_prosedur][]';
    });
    
    tindakanProsedurValues.forEach(function(value) {
      _form.push({
        name: 'UjiFungsiFisioterapiForm[tindakan_prosedur][]',
        value: value
      });
    });
    
    const diagFungsiValues = [];
    
    const diagFungsiSelect = $('select[name="UjiFungsiFisioterapiForm[diag_fungsi][]"]');
    if (diagFungsiSelect.length > 0) {
      const select2Data = diagFungsiSelect.select2('data');
      select2Data.forEach(function(item) {
        diagFungsiValues.push(item.text || item.id);
      });
    }
    
    _form = _form.filter(function(item) {
      return item.name !== 'UjiFungsiFisioterapiForm[diag_fungsi][]';
    });
    
    diagFungsiValues.forEach(function(value) {
      _form.push({
        name: 'UjiFungsiFisioterapiForm[diag_fungsi][]',
        value: value
      });
    });
    
    $(this).docoForm("click", {
      url: `/fisioterapi/uji-fungsi-fisioterapi/save`,
      method: "POST",
      data: _form,
      success: function (res) {
        $("#btn-cetak-uji-fungsi").prop("disabled", false);
        docoNotification('success', res.response.title, res.response.text);
        $("#tab-uji-fungsi-fisioterapi").trigger("click");
      },
    });
  });
  
  $("#btn-cetak-uji-fungsi").on("click", function () {
    if (!$(this).prop("disabled")) {
      window.open(
        "/reports/viewer/uji-fungsi-fisioterapi?pendaftaran_id=" + pendaftaran_id
      );
    }
  });
});
