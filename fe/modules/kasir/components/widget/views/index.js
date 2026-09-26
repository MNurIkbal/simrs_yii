$(".close-bill").addClass('hidden')
$(document).on("click", ".close-bill", function(e) {
   e.preventDefault();
   var _display = $(this).attr('data-job-order')
   var _is_close_bill = $(this).attr('data-close-bill')
   var _tipeLockBill = 'Lock Bill'
   var _tipeUnLockBill = 'Unlock Bill'
   var header = 'Perhatian !';
   var alasan;

   add = $('#confirm-form').clone().removeClass('hidden');
   add.find('.input-pemakai').addClass('hidden');
   add.find('.input-sandi').addClass('hidden');
   add.append(`
      <div class="row">
         <div class="form-group" style="margin-right:10px;margin-left:10px;margin-bottom:20px;">
            <textarea class="form-control" id="alasan" rows="3" placeholder="Alasan"></textarea>
         </div>
      </div>
      <br/><br/>`)
   add = add.html();

   var messageLockBill = `Apakah Anda yakin akan melakukan ${_tipeLockBill} ?`
   var messageUnLockBill = `Apakah Anda yakin akan melakukan ${_tipeUnLockBill} ? ${add}`
   if(_display == 1) {
      $(document).find('.confirm-message-text').css("margin-top", "40px")
      messageLockBill = `${messageLockBill} </br></br> <span style="font-weight:bold;font-size:14px;">Pasien Memiliki Orderan Tindakan/Obat yang Belum Selesai.</span>`
   }

   var message = (_is_close_bill == 1) ? messageUnLockBill : messageLockBill
   var label = {
      buttons: {
         'Yes': 'button-yes',
         'No': 'button-no'
      }
   };

   $(document).on("change", "#alasan", function(){
      alasan = $(this).val()
   })
   
   var _url = $(this).attr('action')
   $.showQuestionDialog(header, message, label, function (reaction) {
      if (reaction == 'Yes') {
         $().docoForm("click", {
            url: _url,
            data: {
               is_close_bill: _is_close_bill,
               alasan: alasan,
            },
            skipConfirm: true,
            success: function(data) {
               if(typeof data.response.is_close_bill != 'undefined') {
                  var _responseData = data.response
                  var _text = (_responseData.is_close_bill) ? _tipeUnLockBill : _tipeLockBill
                  $(".close-bill").html('<b><i class="fa fa-key"></i></b>' + _text)
                  $(".close-bill").attr('data-close-bill', (_responseData.is_close_bill ? 1 : 0))
               }
            }
         });
      }

      if (reaction == 'No') {
         hideQuestionDialog();
         $('[data-popup="tooltip"]').tooltip();
      }
   });
})