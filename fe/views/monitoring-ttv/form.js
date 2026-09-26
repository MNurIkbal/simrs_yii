$('#btn-save-mon-ttv').on('click', function(){
    var _form = $("#form-monitoring-ttv").serializeArray();
    _form.push({ name: "MonitoringTtvForm[pendaftaran_id]", value: pendaftaran_id});
    _form.push({ name: "MonitoringTtvForm[sumberttv_id]", value: sumberttv_id });
    $(this).docoForm("click", {
      url: `/${modul}${url}/input-ttv?pendaftaran_id=${pendaftaran_id}`,
      method: "POST",
      data: _form,
      success: function (res) {
        // $("#tab-monitoring-ttv").trigger("click");
        $('#modal_backdrop').modal('hide');
        $('#example').DataTable().ajax.reload();
      },
    });
})

$('#btn-edit-mon-ttv').on('click', function () {
  var _form = $("#form-monitoring-ttv").serializeArray();
  _form.push({ name: "MonitoringTtvForm[pendaftaran_id]", value: pendaftaran_id });
  _form.push({ name: "MonitoringTtvForm[vitalsign_id]", value: vitalsign_id });
  _form.push({ name: "MonitoringTtvForm[sumberttv_id]", value: sumberttv_id });
  $(this).docoForm("click", {
    url: `/${modul}${url}/edit-ttv?pendaftaran_id=${pendaftaran_id}`,
    method: "POST",
    data: _form,
    success: function (res) {
      // $("#tab-monitoring-ttv").trigger("click");
      $('#modal_backdrop').modal('hide');
      $('#example').DataTable().ajax.reload();
    },
  });
})

$(document).ready(function(){
    $('.selectJenis, .selectKesadaran').select2();
    $('.doco-number').on('input', function(){
        this.value = this.value.replace(/\D/g, '').slice(0, 3);
        if (this.value === '') {
            this.value = '0';
        }
    })

    $('.suhu, .berat_badan').on('input', function(){
        let val = this.value;
        val = val.replace(/[^0-9.]/g, '');

        let parts = val.split('.');

        if (parts.length > 2) {
            val = parts[0] + '.' + parts[1];
            parts = val.split('.');
        }

        if (parts[0].length > 3) {
            parts[0] = parts[0].slice(0, 3);
        }

        if (parts[1] && parts[1].length > 2) {
            parts[1] = parts[1].slice(0, 2);
        }

        this.value = parts.join('.');
    });
})