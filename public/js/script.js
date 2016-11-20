/* Script comportant toutes les fonctions JavaScript du site. */

function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

$(document).ready(function() {

    $('#btnImportICS').click(function(e) {
        e.preventDefault();
        $('#modalConfirmICS').modal();
    });

    $('.select').select2();

    $.post("/SatellysReborn/compte/utilisateurCourant/", function(data) {
        var enseignant = JSON.parse(data);

        $.post("/SatellysReborn/enseignant/cours/" + enseignant.enseignant.id, function(data) {
            cours = JSON.parse(data);
            console.log(cours);
            events = [];

            cours.forEach(function(event) {
                events.push({
                    title: event.matiere.nom + '\n' + /*event.groupes[0].promo.nom + '\n' +*/ event.salle,
                    start: event.jour + "T" + event.debut,
                    end: event.jour + "T" + event.fin,
                    color: getRandomColor(),
                    url: 'http://www.google.fr/'
                });
            });

            /* Affichage de l'emploi du temps */
            $("#emploiTemps").fullCalendar({
                customButtons: {
                    print: {
                        text: 'Imprimer',
                        click: function () {
                            window.print();
                        }
                    }
                },
               defaultView: 'agendaWeek',
               header: {
                   left: 'today',
                   center: 'title',
                   right: 'prev,next,print'
               },
               minTime: '07:30',
               maxTime: '19:00',
               allDaySlot: false,
               weekends: false,
               weekNumbers: true,
                nowIndicator: true,
               aspectRatio: 2.2,
               events: events,
                eventMouseover: function(event, jsEvent, view) {
                    $(jsEvent.target).popover({
                        content: event.title,
                        trigger: 'hover',
                        placement: 'bottom'
                    });
                }
           });
        });
    });
});
