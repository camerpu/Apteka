$(document).ready(() =>{
   $.ajax({
      url: "/api/pharmacies",
   }).done((data) => {
      $('#pharmacyTable th').each( function () {
         var title = $(this).text();
         $(this).html( $(this).html() + '<input type="text" class="form-control" style="display:block;" data-name="' + $(this).attr('data-fieldname') + '" data-type="searchInput" placeholder="Szukaj '+title+'" />' );
      } );

      const createdDataTable = $("#pharmacyTable").DataTable({
         searching: true,
         data: data['hydra:member'],
         columns: [
            { "data": "id" },
            { "data": "name" },
            { "data": "postalCode" },
            { "data": "street" },
            { "data": "city" },
            { "data": "longitude" },
            { "data": "latitude" },
         ],
      });

      $("#pharmacyTable").show();
      $("#exportWidget").show();
      $("#spinner-div").hide();
      $('#pharmacyTable_filter').hide();

      createdDataTable.columns().every(function (idx) {
         var that = this,
             header = this.header();
         // prevent propagation
         $('input', header).on('click focus', function (event) {
            return false;
         });

         // use keypress, since the th has a listener on it and fires the redraw
         $('input', header).on('keypress', function (event) {
            if (event.type == 'keypress' && event.keyCode == 13 && that.search() !== this.value) {
               that.search(this.value).draw();
               return false;
            }
         });
      });
   });

});

const APP = {
   downloadFile: () => {
      let whatToExport = $('select[name=whatExport]').val();
      let exportToFormat = $('select[name=exportTo]').val();
      let url = '';
      let base = 'http://127.0.0.1:8000';
      if(whatToExport === 'all') {
         url = new URL('export/all/' + exportToFormat, base);
      }
      else {
         url = new URL('export/filtered/' + exportToFormat, base);
         let params = url.searchParams;
         $('input[data-type=searchInput]').each(function(){
            let currentInput = $(this);
            let val = currentInput.val();
            if(val.length > 0){
               params.set(currentInput.attr('data-name'), val);
            }
         });
      }
      window.open(url);

   },
}