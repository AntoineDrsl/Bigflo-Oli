var path = document.querySelector('#path-icon');

var pathLength = path.getTotalLength();

//On supprime l'élément au chargement de la page et on le coupe en petits segments
path.style.strokeDasharray = pathLength + ' ' + pathLength;
path.style.strokeDashoffset = pathLength;

window.addEventListener("scroll", function(e) {
 
    //Définition du pourcentage de scroll
    var scrollPercentage = (document.documentElement.scrollTop + document.body.scrollTop) / (document.documentElement.scrollHeight - document.documentElement.clientHeight);
    console.log(scrollPercentage)

    //Définition du pourcentage de dessin
    var drawLength = pathLength * scrollPercentage;
    
    //Définition de ce qu'il faut dessiner
    path.style.strokeDashoffset = pathLength - drawLength;
        
    //Quand le pourcentage de scroll atteint 100%, on lie tous les segments, sinon on continue de les couper
    if (scrollPercentage >= 0.99) {
        path.style.strokeDasharray = "none";
    } else {
        path.style.strokeDasharray = pathLength + ' ' + pathLength;
    }

    // if(scrollPercentage <= 0.01) {
    //     $('#arrow').fadeIn()
    //     $('#story-1').fadeIn()
    //     $('#bioTitle').fadeIn()
    // }

    // if(scrollPercentage >= 0.01) {
    //     $('#arrow').fadeOut()
    //     $('#story-1').fadeOut()
    //     $('#bioTitle').fadeOut()
    // }
  
});