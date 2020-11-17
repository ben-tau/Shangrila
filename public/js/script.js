/* ca ne marche pas et je ne sais pas pourquoi */

/* $(document).ready(function(){

// on initialise la valeur de notre futur total 

    var total = 0;

// on récupère la valeur de tous les totaux des lignes de commandes de notre tableau affiché
// on le rajoute au total

    $('#totalCell').each(function(){

        var x = $(this).children().html().replace(",",".");

        if(!isNaN(x) && x!=0)
        {
            total = total + parseFloat(x);
            console.log(total);
        }   
    });

// on l'insère dans le span #totalOrder

    $('#totalOrder').text(total.toFixed(2).replace(".",","));
}); */