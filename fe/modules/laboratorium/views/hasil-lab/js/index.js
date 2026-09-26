// Identifier PHP
const { id, feUrl, isFinish, pasienMasukPenunjangId, columnsLabel, isBayar } = phpVars
const urlVerif = `${feUrl}laboratorium/hasil-lab/verifikasi?id=${id}`;
const urlUnverif = `${feUrl}laboratorium/hasil-lab/unverifikasi?id=${id}`;
$('#obat-alkes').prop('disabled', false);
if (isFinish) {
  $('#btn-input').prop('disabled', true);
  $('#btn-ulang').prop('disabled', true);
  $('#obat-alkes').prop('disabled', true);
  $('#verifikasi')
    .addClass('unverifBtn')
    .html("<b><i class='fa fa-key'></i></b> Cabut Verifikasi");
}
if (isBayar) {
  $('#obat-alkes').prop('disabled', true);
}

$('#verifikasi').on('click', function () {
  let url = urlVerif;
  let confirmMessage = `Apa anda yakin ? Data tidak bisa di edit lagi jika sudah terverifikasi`
  if (isFinish) {
    confirmMessage = `Apa anda yakin ? Verifikasi akan dilepas, sehingga bisa menverifikasi ulang`
    url = urlUnverif
  }
  $(this).docoForm("click", {
    url,
    confirmMessage,
    success: () => location.reload()
  });
})


// Datatables
let table;
$(document).on('click', '.data-reload', function () {
  table.draw();
});
function initDataTable() {
  const { sample, namaPemeriksaan, expertise } = columnsLabel
  table = $('#table-hasil-lab').docoTabel({
    filter: true,
    columnDefs: [{
      orderable: false,
      className: 'select-checkbox',
      targets: 0
    }],
    select: {
      style: 'os',
      selector: 'tr'
    },
    sorting: [[2, 'asc']],
    displayLength: 10,
    processing: true,
    serverSide: true,
    ajax: baseUrl + `laboratorium/hasil-lab/get-data?id=${pasienMasukPenunjangId}`,
    columns: [
      {
        data: null,
        searchable: false,
        orderable: false,
        defaultContent: '',
      },
      {
        title: 'No',
        data: 'rowNum',
        searchable: false,
        orderable: false
      },
      { title: sample, data: 'nama_sample', name: 'nama_sample' },
      { title: namaPemeriksaan, data: 'daftartindakan_nama' },
      { title: expertise, data: 'is_expertise' },
    ],
  });
  $('.dataTables_filter').hide();
  $('#btn-ulang').on('click', function () {
    location.reload();
  })
}
$(document).ready(initDataTable)