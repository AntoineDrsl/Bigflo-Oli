// MUSIC ICON
var path = document.querySelector('#path-icon');
var pathLength = path.getTotalLength();
//On supprime l'élément au chargement de la page et on le coupe en petits segments
path.style.strokeDasharray = pathLength + ' ' + pathLength;
path.style.strokeDashoffset = pathLength;
window.addEventListener("scroll", function(e) {
 
    //Définition du pourcentage de scroll
    var scrollPercentage = (document.documentElement.scrollTop + document.body.scrollTop) / (document.documentElement.scrollHeight - document.documentElement.clientHeight);

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
});

// SCROLL DISPLAY
ScrollOut({
    targets: 'section',
    onShown: function(el) {
        el.animate([{ opacity: 0 }, { opacity: 1 }], 1000);
    }
});
ScrollOut({
    targets: '.textContent',
    onShown: function(el) {
        $(el).addClass('textAppear');
    },
    onHidden: function(el) {
        $(el).removeClass('textAppear');
    }
});
ScrollOut({
    targets: '.imgContent',
    onShown: function(el) {
        $(el).addClass('imgAppear');
    },
    onHidden: function(el) {
        $(el).removeClass('imgAppear');
    }
});

// GO UP
$('#arrow').on('click', function() {
    $('html, body').animate(
        { scrollTop: $('#navbar').offset().top }, 750
    )
})