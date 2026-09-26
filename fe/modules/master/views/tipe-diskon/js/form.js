$(document).ready(function(){
    let jenislayanan_id = $('#content-kelas').data('jenislayananid');
    $('#content-kelas').docoLoad({
        url: '/master/tipe-diskon/create-detail?stat='+action+'&&jl='+jenislayanan_id,
        dataType: 'html',
        success : function(data) {
            $(" .select2 ").select2();

        }
    });

    $('#tab-kelas').on('click', function(e){
        let jenislayanan_id = $('#content-kelas').data('jenislayananid');
        $('#content-kelas').docoLoad({
            url: '/master/tipe-diskon/create-detail?stat='+action+'&&jl='+jenislayanan_id,
            dataType: 'html',
            success : function(data) {
                $(" .select2 ").select2();
            }
        });
    })

    $('#tab-kategori').on('click', function(e){
        let jenislayanan_id = $('#content-kategori').data('jenislayananid');
        $('#content-kategori').docoLoad({
            url: '/master/tipe-diskon/create-detail?stat='+action+'&&jl='+jenislayanan_id,
            dataType: 'html',
            success : function(data) {
                $(" .select2 ").select2();
            }
        });
    })

    $('#tab-detail').on('click', function(e){
        let jenislayanan_id = $('#content-detail').data('jenislayananid');
        $('#content-detail').docoLoad({
            url: '/master/tipe-diskon/create-detail?stat='+action+'&&jl='+jenislayanan_id,
            dataType: 'html',
            success : function(data) {
                $(" .select2 ").select2();
            }
        });
    })

    $(document).keypress(
        function(event){
          if (event.which == '13') {
            event.preventDefault();
          }
      });
    
})

function resetForm(){
    let _formId = "tipe-diskon-detail-form-"+jenis_layanan;
    let _form = $('#'+_formId);
    _form.trigger('reset');
    _form.find(".max-dijamin").val(docoHelper.convertToRupiah(999999999999));
    $('.layanan-id').val(null).trigger('change');
    $("#btn-simpan").html("<b><i class='fa fa-plus'></i></b> Tambah");
    removeForm();
}


function removeForm(){
    /** remove other form */
    $("form").each(function() {
        let id = "tipe-diskon-detail-form-"+jenis_layanan;
        if (this.id != id && this.id != 'tipe-diskon-form' ){
            $('#'+this.id).remove();
        }
     });
}