/* Script Javascript gérant l a dynamicité de toutes les pages. */

/**
 * Génère une couleur au format hexadécimale aléatoire.
 * @returns {string} la couleur au format hexadéimal.
 */
function getRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++ ) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}

/**
 * Créé une boîte à liste avec recherche pour les départements.
 */
function getDepartements() {
    $('.select-dep').select2({
        ajax: {
           url: function (params) {
               return '/SatellysReborn/departement/listeJSON/' +
                      params.term;
           },
           method: 'post',
           processResults: function (data) {
               var json = JSON.parse(data);
               var deps = [];

               if (json != null) {
                   json.forEach(function(dep) {
                       deps.push({
                            id: dep.id,
                            text: dep.nom
                        });
                   });
               }

               return {
                   results: deps
               };
           }
        }
    });
}

/**
 * Créé une boîte à liste avec recherche pour les promotions.
 */
function getPromotions() {
    $('.select-promo').select2({
         ajax: {
             url: function (params) {
                 return '/SatellysReborn/promotion/listeJSON/' +
                        params.term;
             },
             method: 'post',
             processResults: function (data) {
                 var json = JSON.parse(data);
                 var promos = [];

                 if (json != null) {
                     json.forEach(function(promo) {
                         promos.push({
                               id: promo.id,
                               text: promo.nom + " - " + promo.departement.nom
                           });
                     });
                 }

                 return {
                     results: promos
                 };
             }
         }
     });
}

/**
 * Créé une boîte à liste avec recherche pour les groupes.
 */
function getGroupes() {
    $('.select-groupe').select2({
       ajax: {
           url: function (params) {
               return '/SatellysReborn/groupe/listeJSON/' +
                      params.term;
           },
           method: 'post',
           processResults: function (data) {
               var json = JSON.parse(data);
               var groupes = [];

               if (json != null) {
                   json.forEach(function(groupe) {
                       groupes.push({
                           id: groupe.id,
                           text: groupe.nom + " - " + groupe.promo.nom + " - "
                                 + groupe.promo.departement.nom
                       });
                   });
               }

               return {
                   results: groupes
               };
           }
       }
   });
}

/**
 * Créé une boîte à liste avec recherche pour les étudiants.
 */
function getEtudiants() {
    $('.select-etud').select2({
       ajax: {
           url: function (params) {
               return '/SatellysReborn/etudiant/listeJSON/' +
                      params.term;
           },
           method: 'post',
           processResults: function (data) {
               var json = JSON.parse(data);
               var etudiants = [];

               if (json != null) {
                   json.forEach(function(etudiant) {
                       etudiants.push({
                           id: etudiant.id,
                           text: etudiant.nom + " " + etudiant.prenom
                       });
                   });
               }

               return {
                   results: etudiants
               };
           }
       }
   });
}

/**
 * Créé une boîte à liste avec recherche pour les étudiants.
 */
function getEnseignants() {
    $('.select-ens').select2({
      ajax: {
          url: function (params) {
              return '/SatellysReborn/enseignant/listeJSON/' +
                     params.term;
          },
          method: 'post',
          processResults: function (data) {
              var json = JSON.parse(data);
              var enseignants = [];

              if (json != null) {
                  json.forEach(function(enseignant) {
                      enseignants.push({
                         id: enseignant.id,
                         text: enseignant.nom + " " + enseignant.prenom
                     });
                  });
              }

              return {
                  results: enseignants
              };
          }
      }
  });
}

/**
 * Créé une boîte à liste avec recherche pour les matières.
 */
function getMatieres() {
    $('.select-matiere').select2({
         ajax: {
             url: function (params) {
                 return '/SatellysReborn/matiere/listeJSON/' +
                        params.term;
             },
             method: 'post',
             processResults: function (data) {
                 var json = JSON.parse(data);
                 var matieres = [];

                 if (json != null) {
                     json.forEach(function(matiere) {
                         matieres.push({
                              id: matiere.id,
                              text: matiere.nom
                          });
                     });
                 }

                 return {
                     results: matieres
                 };
             }
         }
     });
}

/**
 * Créé une boîte à liste avec recherche pour les non absent à un cours..
 */
function getEtudiantsNonAbsent() {
    var url = window.location.href;
    url = url.split('/');

    $('.select-etud-abs').select2({
       ajax: {
           url: function (params) {
               return '/SatellysReborn/cours/listeNonAbsentJSON/'
                      + url[url.length - 1] + "/" + params.term;
           },
           method: 'post',
           processResults: function (data) {
               var json = JSON.parse(data);
               var etudiants = [];

               if (json != null) {
                   json.forEach(function(etudiant) {
                       etudiants.push({
                           id: etudiant.id,
                           text: etudiant.nom + " " + etudiant.prenom
                       });
                   });
               }

               return {
                   results: etudiants
               };
           }
       }
   });
}

/**
 * Créé une boîte à liste avec recherche pour les villes.
 */
function getVilles() {
    $('.select-ville').select2({
      ajax: {
          url: function (params) {
              return '/SatellysReborn/ville/listeJSON/' +
                     params.term;
          },
          method: 'post',
          processResults: function (data) {
              var json = JSON.parse(data);
              var villes = [];

              if (json != null) {
                  json.forEach(function(ville) {
                      villes.push({
                         id: ville.numInsee,
                         text: ville.code_postal + " - " + ville.nom
                     });
                  });
              }

              return {
                  results: villes
              };
          }
      }
  });
}

/**
 * Créé une boîte à liste avec recherche pour les utilisateurs.
 */
function getUtilisateurs() {
    $('.select-desti').select2({
      ajax: {
          url: function (params) {
              return '/SatellysReborn/compte/listeUtilisateursJSON/' +
                     params.term;
          },
          method: 'post',
          processResults: function (data) {
              var json = JSON.parse(data);
              var utils = [];

              if (json != null) {
                  json.forEach(function(util) {
                      utils.push({
                         id: util.email,
                         text: util.login
                     });
                  });
              }

              return {
                  results: utils
              };
          }
      }
  });
}

/**
 * Affiche l'emploi du temps d'un enseignant.
 */
function emploiTemps() {
    // Récupère l'id de l'enseignant dans l'url.
    var url = window.location.href.split('/');
    var id = url[url.length - 1];

    // Récupère les cours.
    $.post('/SatellysReborn/enseignant/coursJSON/' + id, function(data) {
        var cours = JSON.parse(data);

        // Les cours au format pour FullCalendar
        events = [];
        if (cours != null) {
            cours.forEach(function(event) {

                var courant = event.matiere.nom + '\n';

                event.groupes.forEach(function(groupe) {
                    courant += groupe.nom + " - " + groupe.promo.nom + ", ";
                });

                courant += '\n' + event.salle;

                events.push({
                    title: courant,
                    start: event.jour + "T" + event.debut,
                    end: event.jour + "T" + event.fin,
                    color: getRandomColor(),
                    url: '/SatellysReborn/cours/details/' + event.id
                });
            });
        }

        // FullCalendar !
        $('#emploiTemps').fullCalendar({
           customButtons: {
               print: {
                   text: 'Imprimer',
                   click: function() {
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
           maxTime: '19:30',
           allDaySlot: false,
           weekends: false,
           weekNumbers: true,
           nowIndicator: true,
           aspectRatio: 2.1,
           events: events
       });
    });
}

// Point d'entrée.
$(document).ready(function () {
    // LURL courante de la page.
    var url = window.location.href;

    if (url.indexOf("emploiTemps") != -1) {
        emploiTemps();
    }

    getDepartements();
    getPromotions();
    getGroupes();
    getEtudiants();
    getEtudiantsNonAbsent();
    getEnseignants();
    getMatieres();
    getUtilisateurs();
    getVilles();

    $('#btnImportICS').click(function(e) {
        e.preventDefault();
        $('#modalConfirmICS').modal();
    });

    $('#form-cours').submit(function(e) {
        var debut = $('#debut').val();
        var fin = $('#fin').val();

        debut = new Date('01/01/2000 ' + debut);
        fin = new Date('01/01/2000 ' + fin);

        // Date de fin après date de début.
        if (fin - debut <= 0) {
            e.preventDefault();
            $("#fin").tooltip({
                title: "L'heure de fin doit être après"
                       + " l'heure de début du cours",
                placement: 'bottom',
                trigger: 'focys'
            });
            $('#fin').focus();
        }
    });

    $("#mail").click(function (e) {
        e.preventDefault();

        var contenu = $(".fixed-table-container").html();
        var css = "";
        $('link').each(function(elem) {
            css += $('link')[elem].outerHTML;
        });

        var mail = "<!DOCTYPE html><html><head>";
        mail += css;
        mail += "</head><body>";
        mail += contenu + "</body>";

        $.ajax({
            url: '/SatellysReborn/mail/setListe/',
            type: 'post',
            data: { liste : mail },
            success: function() {
                window.location.href = "/SatellysReborn/mail/mail/"
            }
        });
    });
});