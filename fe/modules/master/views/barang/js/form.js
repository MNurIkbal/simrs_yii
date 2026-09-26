$(document).ready(function () {

    $('.satuan-kecil-id, .satuan1-id, .satuan2-id').select2();
    $('.satuan1-id, .satuan2-id').prop('disabled', true);
    $('.isi-satuan1, .isi-satuan2').prop('disabled', true);

    function resetDisabledOptions() {
        $('.satuan-kecil-id, .satuan1-id, .satuan2-id').find('option').prop('disabled', false);
        $('.satuan-kecil-id, .satuan1-id, .satuan2-id').select2();
    }

    $('.satuan-kecil-id').on('select2:select', function(){
        resetDisabledOptions()
        var selectedValue = $(this).val();
        if (selectedValue == null || selectedValue == '' || selectedValue == undefined) {

            $('.isi-satuan1').val('').trigger('change');
            $('.isi-satuan1').prop('disabled', true);
            $('.isi-satuan2').val('').trigger('change');
            $('.isi-satuan2').prop('disabled', true);

            $('.satuan1-id').val('').trigger('change');
            $('.satuan1-id').prop('disabled', true);
            $('.satuan2-id').val('').trigger('change');
            $('.satuan2-id').prop('disabled', true);

        } else {
            $('.satuan1-id').prop('disabled', false);
        }
        disabledOptions();
    });

    $('.satuan1-id').on('select2:select', function(){
        resetDisabledOptions();
        var selectedValue = $(this).val();
        if (selectedValue == null || selectedValue == '' || selectedValue == undefined) {
            $('.isi-satuan1').val('').trigger('change');
            $('.isi-satuan1').prop('disabled', true);
            $('.isi-satuan2').val('').trigger('change');
            $('.isi-satuan2').prop('disabled', true);

            $('.satuan2-id').val('').trigger('change');
            $('.satuan2-id').prop('disabled', true);
        } else {
            $('.isi-satuan1').prop('disabled', false);
            $('.satuan2-id').prop('disabled', false);
        }
        disabledOptions();
    });

    $('.satuan2-id').on('select2:select', function(){
        resetDisabledOptions();
        var selectedValue = $(this).val();
        if (selectedValue == null || selectedValue == '' || selectedValue == undefined) {
            $('.isi-satuan2').val('').trigger('change');
            $('.isi-satuan2').prop('disabled', true);
        } else {
            $('.isi-satuan2').prop('disabled', false);
        }
        disabledOptions();
    });

    function disabledOptions() {
        satuanKecil =  $('.satuan-kecil-id').val();
        satuanBesar1 =  $('.satuan1-id').val();
        satuanBesar2 =  $('.satuan2-id').val();

        $('.satuan-kecil-id').find('option[value="' + satuanBesar1 + '"]').prop('disabled', true);
        $('.satuan-kecil-id').find('option[value="' + satuanBesar2 + '"]').prop('disabled', true);
        $('.satuan-kecil-id').find('option[value=""]').prop('disabled', false);

        $('.satuan1-id').find('option[value="' + satuanKecil + '"]').prop('disabled', true);
        $('.satuan1-id').find('option[value="' + satuanBesar2 + '"]').prop('disabled', true);
        $('.satuan1-id').find('option[value=""]').prop('disabled', false);

        $('.satuan2-id').find('option[value="' + satuanKecil + '"]').prop('disabled', true);
        $('.satuan2-id').find('option[value="' + satuanBesar1 + '"]').prop('disabled', true);
        $('.satuan2-id').find('option[value=""]').prop('disabled', false);

        $('.satuan-kecil-id, .satuan1-id, .satuan2-id').select2();
    }
});

