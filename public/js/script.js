/* Script comportant toutes les fonctions JavaScript du site. */

$(document).ready(function() {

    $('#btnImportICS').click(function(e) {
        e.preventDefault();
        $('#modalConfirmICS').modal();
    });

    $('.select').select2();
});