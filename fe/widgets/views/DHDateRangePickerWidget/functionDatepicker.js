var { startId, endId, nameId, statusOrdering } = phpVarsGlobal
$(document).on('click', '#btn_add_start_date_custom', function (event) {
  var $startDate = $(`#${startId}`).pickadate({
    editable: true,
    format: 'dd-mm-yyyy',
    formatSubmit: 'dd-mm-yyyy',
    selectMonths: true,
    selectYears: true,
    onClose: function () {
      $('.datepicker').focus();
    }
  });
  var picker_startDate = $startDate.pickadate('picker');
  if (picker_startDate.get('open')) {
    picker_startDate.close();
  } else {
    picker_startDate.open();
  }
  var _endDate = $(`#${endId}`).pickadate('picker');
  try {
    var checked = _endDate.get('open');
  } catch (e) {
    checked = null;
  }
  if (checked) {
    _endDate.close();
  }
  event.stopPropagation();
});

$(document).on('click', '#btn_add_end_date_custom', function (event) {
  var $endDate = $(`#${endId}`).pickadate({
    editable: true,
    format: 'dd-mm-yyyy',
    formatSubmit: 'dd-mm-yyyy',
    selectMonths: true,
    selectYears: true,
    onClose: function () {
      $('.datepicker').focus();
    }
  });
  var picker_endDate = $endDate.pickadate('picker');
  if (picker_endDate.get('open')) {
    picker_endDate.close();
  } else {
    picker_endDate.open();
  }
  var _startDate = $(`#${startId}`).pickadate('picker');
  try {
    var checked = _startDate.get('open');
  } catch (e) {
    checked = null;
  }
  if (checked) {
    _startDate.close();
  }
  event.stopPropagation();
});