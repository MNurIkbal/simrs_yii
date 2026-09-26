var timer;
var skipTimeout = false;
var autoCpptRi = function _autoCpptRi() {
    $.ajax({
        withoutLoading:true,
        url: $("#form-soap").prop("action")+"&auto=true",
        data: $("#form-soap").serializeArray(),
        type: 'POST',
        success: function (data) {

        }
    });
}


$(document).ready(function() {
  var local = sessionStorage.getItem(`suggestsoapranap#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`);
  var suggest_storage = JSON.parse(local)
  var load_suggest = true  
  $('#cpptform-a_diag_utama').parent().hide()
  $('#cpptform-is_icd_x').val(0)
  
  if(local != null){
    $.each( suggest_storage, function() {
      var name = this.name;
      var value = this.value;
      var date = new Date()
      var text = null
      // if(name == 'cpptForm[date]'){
      //   var waktu_terpakai = (date.getTime() - value)/60000
      //     if(waktu_terpakai > time_reset){
      //       skipTimeout = true;
      //       load_suggest = false;
      //       $("#cpptform-tgl_cppt").val(moment( new Date() ).format('DD/MM/YYYY HH:mm:ss'))
      //       $('#cpptform-cppt_id').val(null)
      //       $('#cpptform-subject').val(null)
      //       $('#cpptform-object').val(null)
      //       $('#cpptform-planning').val(null)
      //       $('#cpptform-is_instruksi_pulang').val(null)
      //       $('#cpptform-a_diag_utama').val(null).trigger('change')
      //       $('#cpptform-a_diag_penyerta').val(null).trigger('change')
      //       $('#cpptform-catatan_dokter').val(null)
      //       $('#cpptform-catatan_perawat').val(null)
      //       sessionStorage.removeItem(`suggestsoapranap#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`);
      //     }

      // }
      if(load_suggest) {
        if(this.length > 0){
          $.each(this,function(){
              var split_text_diag_penyerta = this.value.split("_")
              text_diag_penyerta = (split_text_diag_penyerta[1] === undefined) ? split_text_diag_penyerta : split_text_diag_penyerta[1]
              $('#cpptform-a_diag_penyerta').select2("trigger", "select",{
                  data: { id: this.value, text:text_diag_penyerta}
              })
          })
        }else{
          if(name === undefined){
              name = "cpptform[a_diag_penyerta]"
          }
          name = name.toLowerCase();
          if(name == 'cpptform[a_diag_utama]'){
              value = (this.value == 'undefined_') ? '' : this.value
              var split_text = value.split("_")
              text = (split_text[1] === undefined) ? split_text : split_text[1]
              $('#cpptform-a_diag_utama')[0].append(new Option(text,value,false,false))
          }

          if(name == 'cpptform[ruangan_id]'){
              var split_text = this.value.split("_")
              text = (split_text[1] === undefined) ? split_text : split_text[1]
              $('#cpptform-ruangan_id').val(text).trigger('change')
              // $('#cpptform-ruangan_id')[0].append(new Option(text,this.value,false,false))
          }
          
          if(name == 'cpptform-is_instruksi_pulang' && value == 1){
            $('#cpptform-is_instruksi_pulang')[0].parentNode.className ='checked'
            $('#cpptform-is_instruksi_pulang').val(1)
            $('#cpptform-is_instruksi_pulang').prop('checked',true)
          }

          if(name == 'cpptform-is_icd_x' && value == 1){
            // $('#cpptform-a_diag_utama_text').parent().hide()
            $('#cpptform-is_icd_x').val(1)
            $('#cpptform-is_icd_x').prop('checked',true)
          }      
          
          let res = name.replace('[', '-')
          res = res.replace(']', '')
          $(`#${res}`).val(this.value)
          
        }
      }
    });
    
    if ($('#cpptform-is_icd_x').val() == 1) {
      $('#cpptform-a_diag_utama_text').parent().hide()
      $('#cpptform-a_diag_utama').parent().show()
    }else{
      $('#cpptform-a_diag_utama_text').parent().show()
      $('#cpptform-a_diag_utama').parent().hide()
    }
    $('span').removeClass('select2-container--focus')
    $('#form-soap').focus()
  }
  // Remove class
  $('#cpptform-is_instruksi_pulang').uniform()
  $('#cpptform-is_icd_x').uniform()
  $("#btn-back-soap").removeClass("btn-toolbar");
  $("#btn-save-soap").removeClass("btn-toolbar");
  $("#btn-reset-soap").removeClass("btn-toolbar");

  // Store the currently selected tab in the hash value
  $(".tabbable .nav-tabs li a").on("shown.bs.tab", function (event) {
    // Get id
    var id = $(event.target).attr("href").substr(1);
    // Set hash
    window.location.hash = id;
  });

  // Set hash
  var hash = window.location.hash;
  $('.tabbable .nav-tabs a[href="' + hash + '"]').tab("show");

  // Save soap
  $("#btn-save-soap").on("click", function(event){
    // Prevent default
    event.preventDefault();
    clearTimeout(timer);
    var is_instruksi_checked = false;
    if ($("input[name='CpptForm[is_instruksi_pulang]']:checked").val() == 1) {
      is_instruksi_checked = true;
    }
    var tab_resume = $(document).find("#tab-ranap").find("li#tab-resumemedis");
    var diag_utama_length = $('#cpptform-a_diag_utama_text').val().length
    var values = $("#form-soap").serializeArray();
    if(!$('#cpptform-is_icd_x').is(':checked')){
      values.push({name:'CpptForm[a_diag_utama]', value:$('#cpptform-a_diag_utama_text').val()});
      
    }
    if($('#cpptform-is_icd_x').is(':checked')){
      values.push({name:'CpptForm[a_diag_utama_text]', value:$('#cpptform-a_diag_utama').val()});
      diag_utama_length = $('#cpptform-a_diag_utama').val().length
    }
    if($('#cpptform-subject').val().length < 2 || $('#cpptform-object').val().length < 2  || 
      diag_utama_length < 2 || $('#cpptform-planning').val().length < 2 ){
      new PNotify({
        title: "Peringatan",
        text: "bagian S / O / A / P hanya diisi satu karakter, minimal input pada field SOAP adalah 2 karakter",
        addclass: "alert alert-warning alert-arrow-right alert-styled-right",
        type: "warning",
        delay:3000,
        hide:true
      });
      return false;
    }

    values.push({name:'CpptForm[ruangan_order]', value:id_ruangan});

    $(this).docoForm('click', {
      url: $("#form-soap").prop("action"),
      skipConfirm: true,
      data: values,
      method: "POST",
      success: function (response) {
        const { data } = response;

        tableCppt.draw();
        skipTimeout = true;
        $("#cpptform-tgl_cppt").val(moment( new Date() ).format('DD/MM/YYYY HH:mm:ss'))
        $('#cpptform-cppt_id').val(null)
        $('#cpptform-subject').val(null)
        $('#cpptform-object').val(null)
        $('#cpptform-a_diag_utama_text').val(null)
        $('#cpptform-planning').val(null)
        $('#cpptform-is_instruksi_pulang').val(null)
        skipTimeout = true;
        $('#cpptform-a_diag_utama').val(null).trigger('change')
        skipTimeout = true;
        $('#cpptform-a_diag_penyerta').val(null).trigger('change')
        skipTimeout = true;
        $('#cpptform-catatan_dokter').val(null)
        setTimeout(function () {
          sessionStorage.removeItem(`suggestsoapranap#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`);
        }, 500);
        if (data.a_diag_utama.text) {
          $("#patient-history-tab")
            .find(".diagnosa-dokter-text")
            .text(data.a_diag_utama.text);
        }

        docoNotification(
          "success",
          "Proses berhasil",
          "SOAP berhasil disimpan"
        );

        $("#patient-history-tab").trigger("click");
        $("#tab-cppt").trigger("click");
        $("#edited-cppt-id").val("");
        if (data.update_session_data) {
            location.reload();
        }
      },
      complete: () => {
        hideLoader();
      },
    });

  });

  // Save soap auto
  $('#form-soap').on('change keyup', ({delegateTarget}) => {
    if ($('#cpptform-is_icd_x').prop('checked')){
      $(document).ready(function() {
        $('input[name="CpptForm[is_icd_x]').val(1)
        $('#cpptform-a_diag_utama_text').parent().hide()
        // $('#cpptform-a_diag_utama_text').val('')
        $('#cpptform-a_diag_utama').parent().show()
      })
    }else{
      $('input[name="CpptForm[is_icd_x]').val(0)
      $('#cpptform-a_diag_utama_text').parent().show()
      $('#cpptform-a_diag_utama').parent().hide()
      // $('#cpptform-a_diag_utama').val(null).trigger('change')
    }


    skipTimeout = false //penambahan default value skipTimeout = true untuk disable autosave soap
    if ( skipTimeout ) {
      skipTimeout =  false
      return true
    }

    clearTimeout(timer);
    if($("#form-soap").prop("action") == undefined){
      return false;
    }
        
      timer = setTimeout(function () {
        var isInstruksiPulang = ''
        var cpptformIsInstruksiPulangValue = 0
        if ($('#cpptform-is_instruksi_pulang').is(':checked')) {
          isInstruksiPulang = 'checked'
          cpptformIsInstruksiPulangValue = 1
        }

        var IsIcdX = ''
        var IsIcdXValue = 0
        if ( $('#cpptform-is_icd_x').is(':checked') ) {
          IsIcdX = 'checked'
          IsIcdXValue = 1
        }

        let uniformCpptformIsInstruksiPulang = [{name: 'uniform-cpptform-is_instruksi_pulang', value: isInstruksiPulang}];
        let cpptformIsInstruksiPulang = [{name: 'cpptform-is_instruksi_pulang', value: cpptformIsInstruksiPulangValue}];
        let uniformCpptformIsIcdX = [{name: 'uniform-cpptform-is_icd_x', value: IsIcdX}];
        let cpptformIsIcdX = [{name: 'cpptform-is_icd_x', value: IsIcdXValue}];

        let cpptId = [{name: 'cpptId', value: activeEditedCppt}];
        var date = [{name:"cpptForm[date]", value: new Date().getTime()}]
        var diag_penyerta = [$('#cpptform-a_diag_penyerta').serializeArray()]
        var suggest_soap = date.concat(
            cpptId,
            uniformCpptformIsInstruksiPulang,
            cpptformIsInstruksiPulang,
            uniformCpptformIsIcdX,
            cpptformIsIcdX,
            $('#cpptform-ruangan_id').serializeArray(),
            $('#cpptform-subject').serializeArray(),
            $('#cpptform-object').serializeArray(),
            $('#cpptform-a_diag_utama').serializeArray(),
            $('#cpptform-a_diag_utama_text').serializeArray(),
            diag_penyerta,
            $('#cpptform-planning').serializeArray(),
            $('#cpptform-catatan_dokter').serializeArray(),
            $('#cpptform-catatan_perawat').serializeArray(),
            $('#cpptform-instruksi').serializeArray(),
            // $('#uniform-cpptform-is_instruksi_pulang').serializeArray(),
            )
        sessionStorage.setItem(`suggestsoapranap#${pendaftaran_id}#${pegawai_id}#${id_ruangan}`, JSON.stringify(suggest_soap));
    }, 1000);
  })

    if (autofill_diagnose.primary != null) {
        var decodeDiagnose = null
        try {
        decodeDiagnose = autofill_diagnose.primary
        } catch (error) {
        }
        if (decodeDiagnose != null) {
            var newOption = new Option(decodeDiagnose.text, `${decodeDiagnose.id}_${decodeDiagnose.text}`, false, false);
            skipTimeout = true
            $('#cpptform-a_diag_utama').append(newOption).val(`${decodeDiagnose.id}_${decodeDiagnose.text}`).trigger('change')
            skipTimeout = true
        }
    }
    if (autofill_diagnose.secondary != null) {
        var decodeDiagnose = null

        var selectedOption = []
        decodeDiagnose = autofill_diagnose.secondary
        decodeDiagnose.map((diagnose) => {
            if (decodeDiagnose != null) {
                if(diagnose.id != undefined){
                  var newOption = new Option(diagnose.text, `${diagnose.id}_${diagnose.text}`, false, false);
                  $('#cpptform-a_diag_penyerta').append(newOption)
                  selectedOption.push(`${diagnose.id}_${diagnose.text}`)
                }else{
                  var newOption = new Option(diagnose.text, `${diagnose.text}`, false, false);
                  $('#cpptform-a_diag_penyerta').append(newOption)
                  selectedOption.push(`${diagnose.text}`)
                }
            }
        })
        skipTimeout = true
        $('#cpptform-a_diag_penyerta').val(selectedOption).trigger('change')
    }

    if (soapDate != null && soapDate != '') {
        $("#cpptform-tgl_cppt").val(moment(soapDate).format('DD/MM/YYYY HH:mm:ss'))
    }

  // Show hide form
  $("#btn-back-soap").on("click", function (event) {
    // Prevent default
    event.preventDefault();

    $('.tabbable ul li a[href="#view-cppt"]').click();
  });

  // Reset button
  $("#btn-reset-soap").on("click", function (event) {
    // Prevent default
    event.preventDefault();

    // Find form
    var form = $("#form-soap");

    // Reset form
    form[0].reset();
    $("#cpptform-a_diag_utama").select2(null).trigger("change");
    $("#cpptform-is_instruksi_pulang").prop("checked", false);
  });
});
