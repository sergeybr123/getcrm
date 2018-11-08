$(function(){
  $('.datatable').DataTable(
      {
          "language": {
              "url": "//cdn.datatables.net/plug-ins/9dcbecd42ad/i18n/Russian.json"
          },
          "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "Все"]],
          "columnDefs": [ {
              "targets"  : 'no-sort',
              "orderable": false,
              "order": []
          }]
      }
  );
  // $('.datatable').css({'border-collapse':'collapse !important'});
  $('.datatable').attr('style', 'border-collapse: collapse !important');
});
