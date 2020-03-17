$(document).ready(function () {

    //On récupère l'image upload avant son enregistrement pour l'afficher
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#articleImg').attr('src', e.target.result);
                $('#articleImg').addClass('hasBorder');
                $('#articleImgContainer').removeClass('hasBorder');
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    //Au changement de l'input, on lance la fonction
    $("#article_image").change(function(){
        readURL(this);
    });

});