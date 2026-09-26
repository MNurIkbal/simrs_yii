$('#batal-form').docoForm('submit',{
    success : function(data) {
        $('#modal_backdrop').modal('hide');
        table.draw();
    }
});
