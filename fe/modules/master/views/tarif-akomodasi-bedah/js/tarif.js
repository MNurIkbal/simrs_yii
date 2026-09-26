/*
* @Author: rizqi_fitrianto
* @Date:   2018-07-05 09:48:23
 * @Last Modified by: metafiliana
 * @Last Modified time: 2018-08-30 17:14:55
*/
/**
 * Last Modified by: [Dede Herdiana][dede.herdiana@docotel.com]
 * A product of PT. Docotel Teknologi
 * Powered by Sirs
 */

$(document).ready(function(){

    if(Object.keys(opsiPerda).length > 0){
        if ($('.selectPerda').find("option[value='" + opsiPerda.id + "']").length) {
            $('.selectPerda').val(opsiPerda.id).trigger('change');
        } else { 
            var newOption = new Option(opsiPerda.text, opsiPerda.id, true, true);
            $('.selectPerda').append(newOption).trigger('change');
        }
    }
    
    $('.selectPerda').select2({
        placeholder: '',
        minimumInputLength: 1,
        ajax: {
            url: '/master/tarif-tindakan/get-perda',
            dataType: 'json',
            quietMillis: 250,
            data: function(term, page){
                return{
                    q: term,
                    page: page
                }
            },
            processResults: function (data) {
              return {
                results: data.result
              };
            }
        },
        dropdownCssClass: 'bigdrop',
        escapeMarkup: function (m) { return m; },
    });
})



