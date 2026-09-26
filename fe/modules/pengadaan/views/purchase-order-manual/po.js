$(document).ready(function() {
    $(".addrow").click(function(){
        
        var item_id = "<div class=\"form-group\"><div class=\"col-sm-9\"><div class=\"input-group\">" +
                      "<input type='text' name='PoManualDetailForm[][item_id]' class='form-control input-sm item_id'>" +
                      "<span class=\"input-group-btn\"><button type=\"button\" class=\"btn btn-info add-item\" title=\"Tambah Barang/Obat\"><i class=\"fa fa-plus\"></i></button>" +
                      "</span></div></div></div>";

        var qty = "<div class=\"form-group\"><div class=\"col-sm-9\">" +
                  "<input type='text' name='PoManualDetailForm[][qty]' class='form-control input-sm doco-number'>" +
                  "</span></div></div>";
        
        var data = "<tr><td>"+item_id+"</td><td>"+qty+"</td>" +
                   "<td></td><td></td><td></td><td></td></tr>";

        $("#po").append(data);
    });
});
