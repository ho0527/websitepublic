$(document).ready(function() {
    // 原樣板把國家清單寫死在這裡；為了與伺服器端的靜態清單一致，
    // 改為優先複製頁面上的 #guest-country-template（找不到時仍沿用原本的預設值）。
    var origin_countries = '<option value="">choose a country</option><option value="AU">AU - Australia</option><option value="BR">BR - Brasil</option><option value="CA">CA - Canada</option><option value="CH">CH - Switzerland</option><option value="CN">CN - China</option><option value="DE">DE - Germany</option><option value="FR">FR - France</option><option value="IN">IN - India</option>';
    if ($("#guest-country-template").length) {
        origin_countries = $("#guest-country-template").html();
    }
    $(".addguest").click(function(e){ //on add input button click
        e.preventDefault();
        var wrapper_name = e.target.id + "-n";
        var wrapper_name_next_element_num = $("#" + wrapper_name + ' input').length +1;
        var wrapper_origin = e.target.id + "-o";
        var wrapper_origin_next_element_num = $("#" + wrapper_origin + ' select').length +1;
        $("#" + wrapper_name).append('<p><input type="text" id="' + wrapper_name + wrapper_name_next_element_num + '" name="' + wrapper_name + wrapper_name_next_element_num + '" class="form-control"></p>'); //add input box
        $("#" + wrapper_origin).append('<p><select id="' + wrapper_origin + wrapper_origin_next_element_num + '" name="' + wrapper_origin + wrapper_origin_next_element_num + '" class="form-control">' + origin_countries + '</select></p>'); //add select box
    });
});
