<div class="modal-header" style="border-bottom: 1px solid #bbb;">
    <h5 class="modal-title" style="padding-bottom: 10px"><?= $title ?></h5>
    <button type="button" class="close" id="close-auto-daftar" data-dismiss="modal" aria-label="Close">
        <span aria-hidden="true">&times;</span>
    </button>
</div>
<div class="modal-body" style="max-height: 400px;">
  <div class="table-responsive">
    <table id="reservation-items-datatable" class="table table-bordered table-striped table-condensed table-hover" cellspacing="0" style="width:100%;">
      <thead>
        <tr class="bg-inverse">
          <th width="10%">No</th>
          <th width="30%">Pasien</th>
          <th width="35%">Penjamin</th>
          <th widht="25%">Status</th>
        </tr>
      </thead>
      <tbody>
        <?php
          $no = 0;
          foreach($patientList as $key => $value) {
            $no++;
            ?>
            <tr>
              <td><?=$no?></td>
              <td><?=$value['info_pasien']?></td>
              <td>
                  <select class="form-control select-penjamin" name="penjamin_id[]">
                    <option value="<?=$value['penjamin_id']?>" selected><?=$value['penjamin_nama']?></option>
                  </select>
              </td>
              <td>
                <h2 class="badge badge-info status-badge" id="reservasi-status-<?=$value['no_pendaftaranol']?>"><b><i class="fa fa-spinner fa-spin"></i></b> Dalam Proses</h2>
                <div>
                  <span class="hidden text-danger" id="reservasi-msg-<?=$value['no_pendaftaranol']?>" style="white-space:normal;">Silahkan lakukan pendaftaran secara manual melalui tombol Setujui.</span>
                </div>
              </td>
            </tr>
        <?php
          }
        ?>
      </tbody>
   </table>
  </div>
</div>
<div class="modal-footer">
  <div class="row">
    <div class="col-sm-12">
      <button type="button" id="btn-save-auto-register" class="btn btn-primary">Simpan</button>
    </div>
  </div>
</div>
<script>
  var payloadAutoRegister = `<?=$payload?>`;
  var listPasien = <?=json_encode($patientList, true)?>;

  async function listenStatusReservationUpdate(payload) {
    let config = await $.getJSON("./../../json/setup.json")

    if (config.origin == "true") {
      var socket = io.connect(window.location.origin);
    } else {
      var socket = io.connect(config.ip+':'+config.port);
    }

    $.post('/pendaftaran/reservasi-poliklinik/auto-register-process', {
      randString: `<?=$randString?>`,
      reservationItems: payload
    }, () => {
      socket.on(`${channelListenerName}:<?=$randString?>`, (message) => {
        const _data = $.parseJSON(message);

        if (_data.registration_status) {
          $(`#reservasi-status-${_data.no_pendaftaranol}`).removeClass('badge-info').addClass('badge-success').html(`<b><i class="fa fa-check"></i></b> Pendaftaran Berhasil</span>`)
        } else {
          $(`#reservasi-status-${_data.no_pendaftaranol}`).removeClass('badge-info').addClass('badge-danger').html(`<b><i class="fa fa-times"></i></b> Pendaftaran Gagal</span>`)
          $(`#reservasi-msg-${_data.no_pendaftaranol}`).text(_data.message).removeClass('hidden')
        }
      });
    })
  }

  function validatePayload (penjaminValues) {
      let validateValues = [];
      penjaminValues.forEach(function(penjamin, index){
          if (penjamin == null || penjamin == '') {
              validateValues.push(index);
          }
      });

      return validateValues
  }

  function generateValidateMessage(validateData)
  {
      docoNotification('warning', 'Proses Gagal !', 'Penjamin harus dipilih terlebih dahulu..');
      $('[name="penjamin_id[]"]').each(function(index, data){
          if (validateData.includes(index)) {
              $(this).parent('td').removeClass('has-error');
              $(this).siblings('.help-block').remove();

              $(this).parent('td').addClass('has-error');
              $(this).parent('td').append('<div class="help-block"><i class="fa fa-exclamation-circle" aria-hidden="true"></i><span style="white-space:normal">&nbsp;Penjamin belum dipilih.</span></div>');
          }
      })
      return false;
  }

  $(document).ready(function() {

    var tableAutoRegister = $('#reservation-items-datatable').DataTable({
        responsive: true,
        scrollY: true,
        scrollCollapse: true,
        bDestroy: true,
        sPaginationType: "bootstrap", // full_numbers
        iDisplayStart: 10,
        iDisplayLength: 10,
        bPaginate: false, //hide pagination
        bFilter: false, //hide Search bar
        columns : [
              null,
              null,
              null,
              //hide the fourth column
              {'visible' : false }
        ],
    });

    // fix adjust column datatable
    setTimeout( function () {
        tableAutoRegister.columns.adjust();
    }, 500);

    $('#close-auto-daftar').on('click', (e) => {
      e.preventDefault()

      $("#btn-auto-daftar").prop('disabled', true)
      $("#btn-setujui").prop('disabled', true)
      $("#btn-tolak").prop('disabled', true)
      drawTables()
    })

    $(".select-penjamin").select2InfinityScroll({
      url: "/master/penjamin/get-data-select2",
      callbackData: (param)  => {
          return {
              payload: {
                  ...param,
                  carabayar_id: listPasien[0] != 'undefined' ? listPasien[0]?.carabayar_id : null
              }
          }
      }
    })

    $('.select-penjamin').on('select2:select', function(){
        $(this).parent('td').removeClass('has-error');
        $(this).siblings('.help-block').remove();
    })

    $('#btn-save-auto-register').on('click', function(){
        let arrPayloadAutoRegister = JSON.parse(payloadAutoRegister);
        let penjaminValues = $("[name='penjamin_id[]']")
              .map(function(){
                  return $(this).val() != 'undefined' ?  $(this).val() : '';}
              ).get();
        let validateData = validatePayload(penjaminValues);

        if (validateData.length > 0) {
            console.log(validateData);
            generateValidateMessage(validateData);
            return false;
        }

        arrPayloadAutoRegister.map(function(val, index){
            val.penjamin_id = penjaminValues[index];
            return val;
        })

        tableAutoRegister.column(3).visible(true);
        tableAutoRegister.columns.adjust();
        $(this).attr('disabled', true);
        $("[name='penjamin_id[]']").prop( 'disabled', true);

        listenStatusReservationUpdate(JSON.stringify(arrPayloadAutoRegister));
    });

  })
</script>