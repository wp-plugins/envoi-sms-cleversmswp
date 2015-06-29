<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 08/01/15
 * Time: 10:42
 */
class Clever_SMS_response_handler
{

    public static function print_message_success($class,$param=null){
        $class->print_message_success($param);
    }

    public static function print_message_unauthorized()
    {
        echo '<div class="clever_sms_errors"><span>Erreur Authentification</span>';
        echo "<p>  Vos paramètres de connexion au service sont incorrects. <br/>
            Veuillez renseigner vos identifiant et mot de passe sur la page de configuration du plugin en respectant la casse.<br/>
            Si votre mot de passe est perdu, vous pouvez le ré-initialiser à l'adresse suivante <a href=\"https://cleversmslight.clever-is.fr/fr/reinitialiser-mot-de-passe\">https://cleversmslight.clever-is.fr/fr/reinitialiser-mot-de-passe</a>
            </p>";
        echo '</div>';
    }


    public static function print_message_badRequest()
    {
        echo '<div class="clever_sms_errors"><span>Paramètres incorrects</span> <p>Les paramètres de la requête sont incorrects.</p> </div>';
    }

    public static function print_message_internalErrors()
    {
        echo '<div class="clever_sms_errors"><span>Erreur du service</span> <p>Une erreur est survenue lors du traitement de votre requête. <br/>
        Veuillez réitérer votre demande ultérieument ou contacter le service technique par mail sur support@clever.fr.
        </p> </div>';
    }

    public static function print_errors($errors){
        if (!empty ($errors)) {

            echo '<div class="clever_sms_errors"><span>Erreurs</span><ul>';
            foreach ($errors as $error) {
                echo '<li>' . $error . '</li>';
            }
            echo ' </ul></div>';
        }

    }


    public static function display_errors($message)
    {
        echo '<div class="clever_sms_errors">' . $message . '</div>';
    }

    /**
     *
     * Fonction d'affichage du message de succès
     * @param $message Message à afficher
     */
    public static function display_success($message)
    {
        echo '<div class="clever_sms_success">' . $message . '</div>';
    }


}