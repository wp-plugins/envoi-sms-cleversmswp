
function ConfirmMessage() {
    return confirm("Voulez-vous changer la couleur de fond de page ?");

}

jQuery(document).ready(function(){

//    Gestion du datepicker pour les envois de SMS différés.
    jQuery.timepicker.setDefaults(jQuery.timepicker.regional['fr']);
                        jQuery('.datetimepicker').datetimepicker({

                        dateFormat: 'yy-mm-dd',
                        timeText: 'Date',
                        hourText: 'Heure',
                        minuteText: 'Minute',
                        secondText: 'Seconde',
                        currentText: 'Maintenant',
                        closeText: 'Valider',
                        separator: ' '
                    });


    jQuery('.messageContent').charCount();
    jQuery('.choixEncodage').change(function () {
        jQuery('.messageContent').charCount();
    });


    jQuery('#send_sms_form').submit(function(){

        return confirm("Voulez-vous envoyer les SMS ?");

    });


});

(function($){



    function calcul(longueur_fragment,seuil,length){

        var quotient = length / longueur_fragment;
        var modulo = length % longueur_fragment;
        if (length == 0){
            return 0;
        }
        else if  (length <= seuil){
            return 1;
        }
        else{

            if (modulo == 0) {
                return quotient;
            }
            else{
                return Math.ceil(quotient);
            }
        }
    }
    $.fn.charCount = function(options) {

        var longueur_fragment;
        var seuil;
        var length;
        var settings = $.extend({
            max: 1000,
            longueur_fragment_latin:140,
            seuil_latin:160,
            longueur_fragment_ucs2:63,
            seuil_ucs:70,
            reached: '#FF0000'
        }, options);

        return this.each(function() {
            var obj = $(this);
            value= $('.choixEncodage:checked').val();
            if(value==3){
                longueur_fragment=settings.longueur_fragment_latin;
                seuil=settings.seuil_latin;

            }else if(value==4){
                longueur_fragment=settings.longueur_fragment_ucs2;
                seuil=settings.seuil_ucs;
            }

            var offset;
            if($("#cnil").length) {
                offset=$("#cnil").val().length;
            }else{
                offset=0;
            }

            $('#compteur').html(obj.val().length+offset);

            // $('input[name="clever_appliadminbundle_type[nbMessageCharacters]"]').attr('value',obj.val().length);
            /**************************************/

            length= obj.val().length+offset;
            var nbSMS=calcul(longueur_fragment, seuil, length);
            $('#nbSMS').html(nbSMS);
            // $('input[name="clever_appliadminbundle_type[nbMessages]"]').attr('value',nbSMS);
            /**********************************/
            obj.keyup(function(){
                var text = obj.val().length+offset;
                var remaining = settings.max - text;
                if(value==3){
                    longueur_fragment=settings.longueur_fragment_latin;
                    seuil=settings.seuil_latin;

                }else if(value==4){
                    longueur_fragment=settings.longueur_fragment_ucs2;
                    seuil=settings.seuil_ucs;
                }
                /***********************************/

                length= obj.val().length+offset;
                quotient = length / longueur_fragment;
                modulo = length % longueur_fragment;
                var nbSMS=calcul(longueur_fragment, seuil, length);
                $('#nbSMS').html(nbSMS);
                // $('input[name="clever_appliadminbundle_type[nbMessages]"]').attr('value',nbSMS);

                /**********************************/
                if ( remaining > 0 ) {
                    $('#compteur').html(text);
                    // $('input[name="clever_appliadminbundle_type[nbMessageCharacters]"]').attr('value',text);
                }
                else {
                    $('#compteur').html('<span  style=" font-size:13px;color:'+settings.reached+';"><b>Maximum atteint</b></span>');
                }
            });
        });
    };
})(jQuery);