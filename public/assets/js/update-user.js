$(document).ready(function () {

    //On récupère l'image upload avant son enregistrement pour l'afficher
    function readURL(input) {

        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                console.log(e.target.result)
                $('#avatar').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    //Au changement de l'input, on lance la fonction
    $("#user_avatar").change(function(){
        readURL(this);
    });

});