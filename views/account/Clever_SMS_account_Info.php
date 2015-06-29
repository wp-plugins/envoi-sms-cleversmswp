<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 31/12/14
 * Time: 14:32
 * Classe qui gère la remontée des informations d'un compte
 */
class Clever_SMS_account_Info
{

    /**
     *
     * Fonction d'affichage de la page d'info
     *
     */
    public function display_account_info()
    {
        $conf= new Clever_SMS_config();
        //Verification de la configuration du plugin et de l'activation des modules de PHP nécessaires
        if($conf->is_configuration_completed()){
            $result=$this->process_request();
            $this->process_response($result);
        }
      else{
          //Afichage des erreurs si le plugin est mal configuré
          Clever_SMS_response_handler::print_errors($conf->getMessages());
      }

    }


    /**
     * Fonction qui envoie la requête de récupération des informations du compte.
     * @return array Tableau contenant la réponse du serveur Web service
     */
    public function process_request(){
        //Instance de la classe qui fait appel aux Web Services
        $api = new Clever_SMS_api();
        //Appel de la fonction de récupération d'information du compte
       return  $api->clever_sms_getUserInfo();

    }


    /**
     * Fonction qui analyse l'entête de la réponse et affiche le resultat
     * @param $result Tableau contenant les informations du compte
     */
    public function process_response($result){

        if (isset($result['status']['http_code'])) {
            $code = $result['status']['http_code'];

            switch ($code) {
                //la requête s'est correctement effectuée
                case '200':
                    $object = json_decode($result['response']);
                    Clever_SMS_response_handler::print_message_success($this,$object);
                    break;
                // Erreur dans les paramètres de la requête
                case '400':
                    Clever_SMS_response_handler::print_message_badRequest();
                    break;
                // Erreur d'authentification aux web services
                case '401':
                    Clever_SMS_response_handler::print_message_unauthorized();
                    break;
                // Erreur d'authentification aux web services
                case '500':
                    Clever_SMS_response_handler::print_message_internalErrors();
                    break;
            }
        }
    }
    /**
     *
     * Fonction qui affiche le corps de la réponse du serveur web service  sous forme de tableau en cas de succès.
     * @param $object Reponse du serveur
     */
    public function print_message_success($object)
    {
        ?>

        <h2>INFORMATIONS DE VOTRE COMPTE</h2>
        <div class="clever_sms_block">
            <table id="clever_sms_accountInfo" >
                <tr>
                    <td><b>Nom :</b></td>
                    <td><?php echo $object->account->name ?></td>
                </tr>
                <tr>
                    <td><b>Prénom :</b></td>
                    <td><?php echo $object->account->lastname ?></td>
                </tr>
                <tr>
                    <td><b>Email :</b></td>
                    <td><?php echo $object->account->email ?></td>
                </tr>
                <tr>
                    <td><b>Téléphone :</b></td>
                    <td><?php echo $object->account->telephone ?></td>
                </tr>
                <tr>
                    <td><b>Crédit :</b></td>
                    <td><?php echo $object->account->credit . ' Unité(s)' ?></td>
                </tr>
                <tr>
                    <td><b>Date expiration :</b></td>
                    <td><?php echo $object->account->date_expiration ?></td>
                </tr>
            </table>
        </div>
        <p>Pour <b>modifier les informations</b> de votre compte ou <b>acheter du crédit</b>, vous pouvez vous connecter à votre
            espace abonné sur le site
            <a href="https://cleversmslight.clever-is.fr">https://cleversmslight.clever-is.fr</a>
        </p>
    <?php

    }

}