/**
 * @Author: Andri Amirul Sonjaya
 * @Date:   2022-05-29
 */
$(document).ready(function () {
  
  $(`.btn-jadwal-list`).on("click", function () {
    setTimeout(() => {
      $("#modalProgramTerapi").css("z-index", "1041");
    }, 10);
  });
  setTimeout(() => {
    $("#modalProgramTerapi").css("z-index", "1041");
  }, 10);
  
  $("#detail").docoTabel({
    info: false,
    searching: false,
    scrollY: "250px",
    serverSide: false,
    scrollCollapse: true,
    paging: false,
    ordering: false,
  });
});
