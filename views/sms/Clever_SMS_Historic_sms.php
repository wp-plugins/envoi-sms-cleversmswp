<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 21/01/15
 * Time: 11:51
 */
class Clever_SMS_Historic_sms
{

    public function display_historic()
    {


        $conf = new Clever_SMS_config();
        if ($conf->is_configuration_completed()) {
            $result = $this->process_request();

            $this->process_response($result);
        } else {
            Clever_SMS_response_handler::print_errors($conf->getMessages());
        }
    }


    public function process_response($result)
    {

        if (isset($result['status']['http_code'])) {
            $code = $result['status']['http_code'];

            switch ($code) {
                //la requête s'est correctement effectuée
                case '200':
                    $object = json_decode($result['response']);
                    Clever_SMS_response_handler::print_message_success($this, $object);
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
     * Fonction qui affiche la réponse du serveur sous forme de tableau.
     * @param $object Reponse du serveur
     */
    public function print_message_success($objects)
    {

        ?>

        <h2>HISTORIQUE DES ENVOIS DE MOINS DE 7 JOURS</h2>

        &nbsp;
        <table class="clever_sms_table clever_sms_table_max">
            <thead>
            <tr>
                <th>Référence</th>
                <th>Nbre contacts</th>
                <th>Unités</th>
                <th>Date d'envoi</th>
                <th>Téléchargement</th>
                <th>Message</th>
            </tr>
            </thead>
            <tbody>
            <?php

            foreach ($objects->currentPushs as $object) {

                echo '<tr><td>' . $object->reference . '</td>' .
                    '<td style="min-width:100px">' . $object->nb_recipients . '</td>' .
                    '<td>' . $object->units . '</td>' .
                    '<td style="min-width:150px">' . $object->send_date . '</td>' .
                    '<td style="min-width:150px">
                        <a href="https://cleversmslight.clever.fr/uploadsDirectory/'.$objects->directoryName.'/ack/'.$object->reference.'.csv">Envois</a>';
                        if($object->mo!=0){
                            echo  '&nbsp;<a href="https://cleversmslight.clever.fr/uploadsDirectory/'.$objects->directoryName.'/ack/Mo'.$object->reference.'.csv">Réponses</a>';
                        }
                   echo' </td>'.
                    '<td style="min-width:200px">' . $object->text . '</td></tr>';
            }
            ?>
            </tbody>
        </table>
        <br/>
        <u><b>Statuts des SMS envoyés</b></u>
        <p> <span class="clever_sms_color_orange">&nbsp;2&nbsp;</span>: SMS livré à l'opérateur</p>
        <p><span class="clever_sms_color_green">&nbsp;3&nbsp;</span>: SMS reçu sur le téléphone</p>
        <p> <span class="clever_sms_color_red">&nbsp;4&nbsp;</span>:  Erreur dans l'envoi</p>
        <p> <span class="clever_sms_color_blue">&nbsp;5&nbsp;</span>:  Réponse du destinataire</p>
        <p><span class="clever_sms_color_red">&nbsp;6&nbsp;</span>: Message non délivré</p>
        <p><span class="clever_sms_color_red">&nbsp;8&nbsp;</span>:  Doublon dans l'envoi</p>
        Vous pouvez consulter vos historiques de <b>plus de 7 jours</b> en accédant directement à notre site <a
        href="https://cleversmslight.clever.fr">https://cleversmslight.clever.fr</a>.
    <?php

    }


    /**
     * @return array
     */
    public function process_request()
    {
        //Instance de la classe qui fait appel aux Web Services
        $api = new Clever_SMS_api();
        //Appel de la fonction de récupération d'information du compte
        return $api->clever_sms_get_currentPushs();

    }

}