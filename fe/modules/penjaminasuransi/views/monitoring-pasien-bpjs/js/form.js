$(document).ready(function(){
	$(".selectDiagUtama").select2({
        placeholder: "— Pilih Diagnosa Penyerta (ICD 10) —",
        minimumInputLength: 3, 
        ajax : {
            url: baseUrl+"penjamin-asuransi/monitoring-pasien-bpjs/get-data-diagnosa",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                var query = {
                search: params,
            }
            return {
                q: params.term,
                page:params.page || 1,
                type: "10",
                all_text: 0,
                id_with_text: 1,
                }; 
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { 
                return m; 
            },
        },
    });

    $("#diag_penyerta").select2({
        placeholder: "— Pilih Diagnosa Penyerta (ICD 10) —",
        minimumInputLength: 3, 
        multiple : true,
        // tags : true,
        ajax : {
            url: baseUrl+"penjamin-asuransi/monitoring-pasien-bpjs/get-data-diagnosa",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                var query = {
                search: params,
            }
            return {
                q: params.term,
                page:params.page || 1,
                type: "10",
                all_text: 0,
                id_with_text: 1,
                }; 
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { 
                return m; 
            },
        },
        createTag: function(params) {
            var term = $.trim(params.term);
            if(term === "") { return null; }

            var optionsMatch = false;

            this.$element.find("option").each(function() {
            if(this.value.toLowerCase().indexOf(term.toLowerCase()) > -1) {
                optionsMatch = true;
            }
            });

            if(optionsMatch) {
                return null;
            }
            return {id: term, text: term};
        },
        cache: true
    });

    $.each(callbackDiagPenyerta, function(k,v){
        var option = new Option(v.text, v.id+"_"+v.text, true, true);
        $("#diag_penyerta").append(option).trigger("change");
    });
    
    $("#diag_tindakan").select2({
        placeholder: "— Pilih Tindakan (ICD 9) —",
        minimumInputLength: 3, 
        multiple : true,
        // tags : true,
        ajax : {
            url: baseUrl+"penjamin-asuransi/monitoring-pasien-bpjs/get-data-diagnosa",
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                var query = {
                search: params,
            }
            return {
                q: params.term,
                page:params.page || 1,
                type: "9",
                all_text: 0,
                id_with_text: 1,
                }; 
            },
            processResults: function (data) {
                return {
                    results: data.result
                };
            },
            dropdownCssClass: "bigdrop",
            escapeMarkup: function (m) { 
                return m; 
            },
        },
        createTag: function(params) {
            var term = $.trim(params.term);
            if(term === "") { return null; }

            var optionsMatch = false;

            this.$element.find("option").each(function() {
            if(this.value.toLowerCase().indexOf(term.toLowerCase()) > -1) {
                optionsMatch = true;
            }
            });

            if(optionsMatch) {
                return null;
            }
            return {id: term, text: term};
        },
        cache: true
    });
    
    $.each(callbackDiagTindakan, function(k,v){
        var option = new Option(v.text, v.id+"_"+v.text, true, true);
        $("#diag_tindakan").append(option).trigger("change");
    });
})

$("#form-diagnosa").docoForm("submit",{
    success : function(data) {
        if (data.status == 201)
        this.formInput[0].reset();
        table.draw();
        $('#modal_backdrop').modal('hide');
    }
});