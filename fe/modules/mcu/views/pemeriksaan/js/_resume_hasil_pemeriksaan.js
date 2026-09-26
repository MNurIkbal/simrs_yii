function setAndValidateResumeEditor() {
    var valueData = CKEDITOR.instances.resume_editor.getData().replace(/<[^>]+>/g, '').trim();
    $("#count").html(`${valueData.length} Characters`)
    $("#resumehasilpemeriksaanform-resume_hasil_pemeriksaan_hidden").val(valueData)
}

function setAndValidateTatalaksanaEditor() {
    var valueData = CKEDITOR.instances.tatalaksana_editor.getData().replace(/<[^>]+>/g, '').trim();
    $("#countTatalaksana").html(`${valueData.length} Characters`)
    $("#resumehasilpemeriksaanform-tatalaksana_pemeriksaan_hidden").val(valueData)
}

$(document).ready(function() {
    $('.select2').select2();
    // init value
    setAndValidateResumeEditor();
    setAndValidateTatalaksanaEditor();

    $("#form-resume-hasil").docoForm('submit', {
        skipErrorNotif: false,
        success: function(data) {
            $('.print').attr('disabled', false)
        }
    });

    $("#resume_editor").on("change", function() {
        setAndValidateResumeEditor();
    })

    $("#tatalaksana_editor").on("change", function() {
        setAndValidateTatalaksanaEditor();
    })
});