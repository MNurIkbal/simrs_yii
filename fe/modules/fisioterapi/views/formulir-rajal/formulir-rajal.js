$(document).ready(function () {
  $('input[name="suspek_penyakit_radio"]').on('change', function() {
    const suspekPenyakitField = $('input[name="FormulirRajalForm[suspek_penyakit]"]');
    if ($(this).val() === 'Tidak') {
      suspekPenyakitField.val('').prop('disabled', true);
    } else {
      suspekPenyakitField.val('').prop('disabled', false).focus();
    }
  });

  const initialRadioValue = $('input[name="suspek_penyakit_radio"]:checked').val();
  const suspekPenyakitField = $('input[name="FormulirRajalForm[suspek_penyakit]"]');
  if (initialRadioValue === 'Tidak') {
    suspekPenyakitField.prop('disabled', true);
  } else {
    suspekPenyakitField.prop('disabled', false);
  }
  
  $("#btn-cetak-formulir-rawat-jalan").on("click", function () {
    if (!$(this).prop("disabled")) {
      window.open(
        "/reports/viewer/formulir-rawat-jalan?pendaftaran_id=" + pendaftaran_id
      );
    }
  });
});

$("#btn-save-formulir").on("click", function (e) {
    let _form = $("#formulir-rajal-form").serializeArray();
    
    const selectedRadio = $('input[name="suspek_penyakit_radio"]:checked');
    const suspekPenyakitValue = selectedRadio.val();
    const suspekPenyakitText = $('input[name="FormulirRajalForm[suspek_penyakit]"]').val();
    
    _form = _form.filter(function(item) {
      return item.name !== 'FormulirRajalForm[suspek_penyakit]';
    });
    
    if (suspekPenyakitValue === 'Tidak') {
    } else {
      _form.push({
        name: 'FormulirRajalForm[suspek_penyakit]',
        value: suspekPenyakitText || ''
      });
    }
    
    const tindakanProsedurValues = [];
    
    const tindakanProsedurSelect = $('select[name="FormulirRajalForm[diag_kfr][]"]');
    if (tindakanProsedurSelect.length > 0) {
      const select2Data = tindakanProsedurSelect.select2('data');
      select2Data.forEach(function(item) {
        tindakanProsedurValues.push(item.text || item.id);
      });
    }
    
    _form = _form.filter(function(item) {
      return item.name !== 'FormulirRajalForm[diag_kfr][]';
    });
    
    tindakanProsedurValues.forEach(function(value) {
      _form.push({
        name: 'FormulirRajalForm[diag_kfr][]',
        value: value
      });
    });
    
    const diagFungsiValues = [];
    
    const diagFungsiSelect = $('select[name="FormulirRajalForm[diag_fungsi][]"]');
    if (diagFungsiSelect.length > 0) {
      const select2Data = diagFungsiSelect.select2('data');
      select2Data.forEach(function(item) {
        diagFungsiValues.push(item.text || item.id);
      });
    }
    
    _form = _form.filter(function(item) {
      return item.name !== 'FormulirRajalForm[diag_fungsi][]';
    });
    
    diagFungsiValues.forEach(function(value) {
      _form.push({
        name: 'FormulirRajalForm[diag_fungsi][]',
        value: value
      });
    });
    
    $(this).docoForm("click", {
      url: `/fisioterapi/formulir-rajal/save`,
      method: "POST",
      data: _form,
      success: function (res) {
        $("#btn-cetak-formulir-rawat-jalan").prop("disabled", false);
        docoNotification('success', res.response.title, res.response.text);
        $("#tab-formulir-rajal").trigger("click");
      },
    });
  });
