<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 09/01/15
 * Time: 14:49
 */
class Clever_SMS_send_sms
{

    private function display_send_sms_form()
    {
        $encoding = (isset($_POST['encodage'])) ? $_POST['encodage'] : 3;
        ?>
        <form method="post" id="send_sms_form" class="clever_sms_page">

            <p>

                <?php if ($this->isTransmitter()) {
                    ?>
                <label for="cnil">Mention CNIL * :</label>
                <input  id="cnil" type="text" name ="cnil" readonly value=" Stop au 36105"><br/>
                    <div id="clever_sms_cnil">
                        <span>Pour être conforme aux lois Informatiques et Libertés du 10/01/1978 et LCEN du 21/06/2004,en personnalisant l'émetteur de l'envoi, la mention 	&#8249;	&#8249; Stop au 36105	&#8250;	&#8250; à été la fin de votre  message pour informer le mobinaute de la possibilité de s'opposer à ce type de message. Ceci vous coûte 14 caractères visible sur le compteur. </span>
                    </div>
                <?php
                }
                ?>
            </p>
            <!--Champ numéros destinataires-->
            <p>
                <label for="recipients">Numéro(s) de téléphone au format international (ex: +33600000000) en les
                    séparant par
                    des points-virgules <<;>> * : </label>
                <textarea name="recipients" cols="80" rows="5"
                          placeholder="Rentrez un ou plusieurs numéros de téléphone au format international en les séparant par des points-virgules <<;>>"
                          required="required"><?php echo @$_POST['recipients'] ?></textarea>
            </p>


            <!--Champ encodage du message-->
            <p>
                <label for="encodage">Encodage du message * : </label>
                <input type="radio" class="choixEncodage" name="encodage"
                       value="3" <?php if ($encoding == 3) echo 'checked' ?> > Latin <i>(Français, Anglais...)</i><br>
                <input type="radio" class="choixEncodage" name="encodage"
                       value="4" <?php if ($encoding == 4) echo 'checked' ?> > Ucs2 <i>(Arabe, Cyrillique ...)</i><br>
            </p>


            <!--Champ texte du message-->
            <p>

                <label for="message">Texte du message * : </label>


            <div>
                <div class="left">
                    <textarea class="messageContent" maxlength="1000" name="message" cols="80" rows="5"
                              placeholder="Rentrez le texte du SMS à envoyer à vos destinataires"
                              required="required"><?php  @$_POST['message'] ?></textarea>

                </div>

                <div id="clever_sms_msg" class="left">
                    <!--Decompte nombre de caractère du message-->
                    <div id="nb_character">
                        <label>Nombre de caractères saisis</label><br/>
                        <span class="clever_sms_big" id="compteur"> 0</span>
                    </div>
                    <br/>
                    <!--Nombre de SMS que comporte le message-->
                    <div id="nb_sms">
                        <label>Nombre de SMS</label><br/>
                        <span class="clever_sms_big" id="nbSMS">0</span>
                    </div>
                </div>

                <div class="clear"></div>


            </div>
            </p>
            <!--Date d'envoi du message-->
            <p>
                <label for="dateSend">Date d'envoi du message (facultatif) : </label>
                <input name="dateSend" class="datetimepicker" type="text" readonly="true"
                       value="<?php echo $_POST['dateSend'] ?>"
                       placeholder="Rentrez l'heure d'envoi du message si différé"/>
            </p>
            <input id="sendSMS" type="submit" name="sendSMS" class="button-primary" value="Envoyer"/>
        </form>
        <i>Les champs marqués d'un * sont obligatoires.</i>
    <?php
    }

    /**
     * GEstionnaire de la fonction d'envoi de message
     */
    public function display_send_sms()
    {

        $conf = new Clever_SMS_config();
        if ($conf->is_configuration_completed()) {
            $errors = array();

            if ($_POST != NULL && isset($_POST['sendSMS'])) {

                $errors = $this->validate_send_sms();
                if (empty($errors)) {
                    $api = new Clever_SMS_api();
                    $dateSend = (isset($_POST['dateSend']) && !empty($_POST['dateSend'])) ? $_POST['dateSend'] : null;
                    $cnil=isset($_POST['cnil'])?$_POST['cnil']:'';
                    $result = $api->clever_sms_sendPush($_POST['message'].$cnil, $this->parseRecipients($_POST['recipients']), $dateSend, $_POST['encodage']);
                }
            }
            echo ' <h2>Envoi de SMS à vos destinataires</h2>';
            Clever_SMS_response_handler::print_errors($errors);
            if (isset($result)) {
                $this->process_response($result);
            }
            $this->display_send_sms_form();
        } else {
            Clever_SMS_response_handler::print_errors($conf->getMessages());
        }
    }

    /**
     *
     * Fonction qui affiche la réponse du serveur sous forme de tableau.
     * @param $object Reponse du serveur
     */
    public function print_message_success($object)
    {
        Clever_SMS_response_handler::display_success("Votre envoi a été correctement créé. Identifiant: " . $object->push->reference);

    }

    /**Fonction de validation du formulaire d'envoi de message
     * @return array
     */
    public function validate_send_sms()
    {
        $errors = array();

        //Contrôle de la longueur du message
        mb_internal_encoding("UTF-8");
        if (!isset($_POST['message']) || mb_strlen(trim($_POST['message'])) == 0 || mb_strlen($_POST['message']) > 1000) {
            $errors[] = "Le champ <b> Message</b> est obligatoire, sa longueur ne doit pas excéder 1000 caractères.";
        }


        //Contrôle du champ des numéros destinataires
        if (!isset($_POST['recipients']) || mb_strlen(trim($_POST['recipients'])) == 0) {
            $errors[] = "Le champ <b> Numéros de téléphone</b> est obligatoire.";
        }

        //Contrôle du champ Encodage
        if (!isset($_POST['encodage']) || empty($_POST['encodage'])) {
            $errors[] = "Le champ <b> Encodage</b> est obligatoire.";
        }

        //Contrôle du champ des numéros destinataires
        if (isset($_POST['recipients']) && !empty($_POST['recipients'])) {
            $recipients = $this->parseRecipients($_POST['recipients']);
            $nbRecipients = count($recipients);
            if ($nbRecipients > 5000) {
                $errors[] = "Le nombre de destinataires ne doit pas excéder 5000 contacts.";
            }

        }
        //Contrôle de la date d'envoi différé
        if (isset($_POST['dateSend']) && !empty($_POST['dateSend'])) {
            $date = $_POST['dateSend'];
            if ($date != false) {
                $format = 'Y-m-d H:i:s';
                $date = dateTime::createFromFormat($format, $date);
            }
            if ($date == false) {
                $errors[] = "Le format de la date est incorrect.";
            }
        }
        return $errors;

    }

    public function process_response($result)
    {

        if (isset($result['status']['http_code'])) {
            $code = $result['status']['http_code'];
            switch ($code) {
                //la requête s'est correctement effectuée
                case '201':
                    $object = json_decode($result['response']);
                    Clever_SMS_response_handler::print_message_success($this, $object);
                    break;
                // Erreur dans les paramètres de la requête
                case '400':
                    if (strpos($result['response'], "doesn't have enough credit")) {
                        Clever_SMS_response_handler::display_errors("Votre compte ne dispose pas de suffisament de crédit.");
                    } else {
                        Clever_SMS_response_handler::print_message_badRequest();
                    }
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

    /**Fonction qui parse les numéros saisis par l'utilisateur et les stocke dans un tableau
     * @param $recipients Numéros saisis par l'utilisateur
     * @return array Tableau de numéro retourné après parsage
     */
    private function parseRecipients($recipients)
    {

        $listNumbers = str_replace(",", ";", $recipients);
        $arrayNumbers = array_filter(explode(';', $listNumbers));
        return $arrayNumbers;
    }

    public function isTransmitter()
    {
        $options = get_option('clever_sms_options');
        return (isset($options['transmitter']) && $options['transmitter'] != null);
    }
}