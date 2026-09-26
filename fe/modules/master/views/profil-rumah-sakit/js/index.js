// Global Var
var table;

// Event click
$(document).on("click", ".data-reset", function() {
    // Reload table
    table.draw();

    // Disable edit and delete button
    $("#btn-update").prop("disabled", true);
    $("#btn-hapus").prop("disabled", true);
});

// Event click
$(document).on("click", "#example tbody tr", function () {
    // Try catch
    try {
        // Get primary
        primaryKey = table.row(".selected").data().primaryKey
 ? table.row(".selected").data().primaryKey
 : null;
                        // docoNotification('error', errTitle, errMsg);
        
    } catch (e) {
        // Make it false
        primaryKey = false;
        statusPenunjang = false;
        statusPeriksa = false;
    }

    // console.log(primaryKey);
    $("#btn-update").attr("data-target", updateUrl + primaryKey);
    $("#btn-hapus").attr("data-target", hapusUrl + primaryKey);

    // Check class selected
    if ($('#example tr.selected').length == 0) {
        // Disable edit button
        $("#btn-tambah").prop("disabled", false);
        $("#btn-update").prop("disabled", true);
        $("#btn-hapus").prop("disabled", true);
    }
    else {
        // Disable edit button
        $("#btn-tambah").prop("disabled", true);
        $("#btn-update").prop("disabled", false);
        $("#btn-hapus").prop("disabled", false);
        // $("#btn-batal").attr("data-options", 'link');
    }
});


