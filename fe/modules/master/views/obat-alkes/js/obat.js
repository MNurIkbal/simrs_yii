$(document).ready(function(){
    $("#supplier_ids").select2({
        placeholder: " — Pilih Supplier —",
        minimumInputLength: 3, 
        multiple : true,
        ajax : {
            url: '/master/obat-alkes/get-data-supplier',
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                var query = {
                search: params,
            }
            return {
                q: params.term,
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
    
    $("#manufacture_ids").select2({
        placeholder: " — Pilih Manufaktur —",
        minimumInputLength: 3, 
        multiple : true,
        ajax : {
            url: '/master/obat-alkes/get-data-manufaktur',
            dataType: "json",
            quietMillis: 250,
            data: function (params) {
                var query = {
                search: params,
            }
            return {
                q: params.term,
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
})