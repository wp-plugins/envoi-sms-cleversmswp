<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 22/12/14
 * Time: 15:12
 * Classe qui gère le menu réglage du plugin Clever-SMS
 */
class Clever_SMS_settings
{


    /**
     * Constructeur de la classe
     */
    public function __construct()
    {


        // Ajout de la configuration du plugin coté administrateur
        add_action('admin_menu', array($this, 'clever_sms_admin_add_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('publish_post', array('Clever_SMS_api', 'clever_sms_notifyPost'));
        add_action('comment_post', array('Clever_SMS_api', 'clever_sms_notifyComment'));

    }


    /**
     * Enregistrement des champs dans le groupe d'option clever_sms_options
     */
    public function register_settings()
    {
        register_setting('clever_sms_options', 'clever_sms_options[login]');
        register_setting('clever_sms_options', 'clever_sms_options[password]');
        register_setting('clever_sms_options', 'clever_sms_options[transmitter]');
        register_setting('clever_sms_options', 'clever_sms_options[phone]');
        register_setting('clever_sms_options', 'clever_sms_options[notification_comment]');
        register_setting('clever_sms_options', 'clever_sms_options[notification_post]');
    }


    /**
     * Fonction qui ajoute notre plugin au menu réglage de wordpress
     */
    function clever_sms_admin_add_page()
    {
        add_options_page('Configuration du plugin Envoi-sms-cleversmsWP', 'Clever-SMS', 'manage_options', 'clever_sms', array($this, 'clever_sms_options_page'));
    }

    /**
     *
     *Fonction qui construit le formulaire des options à renseigner
     *
     */
    public function clever_sms_options_page()
    {

        $errors = array();
        $optionsSave = get_option('clever_sms_options');


        if ($_POST != NULL && isset($_POST['saveConfiguration'])) {
            //Recupération des champs après soumission du formulaire
            $options = $_POST["clever_sms_options"];
            $optionsSave = $options;
            $errors = $this->validate_options($options);
            //On verifie su aucune erreur n'existe avant insertion
            if (empty($errors)) {
                //Sauvegarde des options dans la base de données
                $update = update_option("clever_sms_options", $options, '', 'yes');
                //Si la sauvegarde s'est correctement effectuée, au affiche le message de succès
                if ($update) {
                    $optionsSave = get_option('clever_sms_options');
                }

            }

        }
        //Affichage de l'entête du formulaire
        echo '<h2>Configuration de votre plugin <b>Envoi-sms-cleversmsWP</b></h2>
        <p>Si vous ne disposez pas de compte, vous pouvez en créer gratuitement sur le site <a
                href="https://cleversmslight.clever.fr">https://cleversmslight.clever.fr</a></p>';
        //Affichage des erreurs du formulaire
        Clever_SMS_response_handler::print_errors($errors);
        //Affichage du message de creation correcte

        if ($update) {
            Clever_SMS_response_handler::display_success("Les modifications ont été correctement prise en compte");
        }
        //Affichage du formulaire de configuration des options
        $this->display_settings_form($optionsSave);
    }

    /**
     *
     * Fonction qui contrôle les données soumises lors de la configuration des options
     * @param $options Tableau contenant les options soumises
     * @return array Tableau des erreurs du formulaire
     *
     */
    public function validate_options($options)
    {

        $errors = array();

        //validation du champ login obligatoire
        if (!isset($options['login']) || empty($options['login'])) {
            $errors[] = "Le champ <b>Identifiant</b> est obligatoire";
        }

        //validation du champ mot de passe obligatoire
        if (!isset($options['password']) || empty($options['password'])) {
            $errors[] = "Le champ <b>Mot de passe</b> est obligatoire";
        }

        //validation du champ OADC qui ne doit pas excéder 11 caractères
        if (isset($options['transmitter']) && strlen($options['transmitter']) > 11) {
            $errors[] = "Le champ <b>Emetteur</b> ne doit pas excéder 11 caractères";
        }
        //Contrôle du champ téléphone en cas de demande de notification
        if ((isset($options['notification_comment']) || isset($options['notification_post'])) && empty($options['phone'])) {
            $errors[] = "En cochant une case de notification, vous devez renseigner le champ <b>Téléphone</b>";

        }

        //Validation du champ téléphone au qui doit être au format internationnal
        if (isset($options['phone']) && !empty($options['phone']) && !($this->validate_phone_international($options['phone']))) {
            $errors[] = "Le champ <b>Téléphone</b> doit être au format international";
        }
        return $errors;
    }


    /**
     * Fonction qui valide un numéro de téléphone au format international
     * @param $phone
     * @return bool Retourne true si le numéro est valide et false dans le cas contraire.
     */
    public function validate_phone_international($phone)
    {
        $motif = '`^\+[1-9]{1}[0-9]{7,11}$`';
        if (!preg_match($motif, $phone)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Fonction qui affiche le formulaire de réglage des options.
     * @param $optionsSave Options sauvegardées
     */
    public function display_settings_form($optionsSave)
    {

        ?>
        <form id="clever_sms_configure_option" method="post" class="clever_sms_page">
            <?php settings_fields('clever_sms_options'); ?>
            <div class="clever_sms_block">
                <span>Paramètres du compte</span><br/><br/>
                <!--                Champ identifiant-->
                <div class="element">
                    <label for="clever_sms_options_login">Identifiant * : </label>
                    <input id="clever_sms_options_login" type="text" name="clever_sms_options[login]" maxlength="50"
                           placeholder="Rentrez votre identifiant"
                           value="<?php echo @$optionsSave['login'] ?>"/>
                </div>
                <!--                Champ Mot de passe-->
                <div class="element">
                    <label for="clever_sms_options_password">Mot de passe * : </label>
                    <input id="clever_sms_options_password" type="password" name="clever_sms_options[password]"
                           maxlength="50"
                           placeholder="Rentrez votre mot de passe"
                           value="<?php echo @$optionsSave['password'] ?>"/>
                </div>

                <!--                Champ Emetteur-->
                <div class="element">
                    <label for="clever_sms_options_transmitter">Emetteur : </label>
                    <input id="clever_sms_options_transmitter" type="text" name="clever_sms_options[transmitter]"
                           maxlength="11" placeholder="Rentrez le nom de l'expéditeur(max 11 caractères)"
                           value="<?php echo @$optionsSave['transmitter'] ?>"/>
                </div>
            </div>

            <div class="clever_sms_block">
                <span>Paramètres d'envoi de SMS</span><br/><br/>
                <!--                Champ Téléphone-->
                <div class="element">
                    <label for="clever_sms_options_phone">Téléphone : </label>
                    <input id="clever_sms_options_phone" type="text" name="clever_sms_options[phone]"
                           maxlength="15" placeholder="Rentrez votre numéro de téléphone Ex : +3360000000"
                           value="<?php echo @$optionsSave['phone'] ?>"/>
                </div>

                <!--            Notification des commentaires-->
                <div class="element">
                    <label for="clever_sms_options_notification_comment">Etre notifié d'un nouveau commentaire par SMS : </label>
                    <input type="checkbox"
                           name="clever_sms_options[notification_comment]" <?php if (isset($optionsSave['notification_comment']) && $optionsSave['notification_comment'] == 'on') echo 'checked' ?> />
                </div>

                <!--            Notification des POST-->
                <div class="element">
                    <label for="clever_sms_options_notification_post">Etre notifié d'un nouveau post par SMS : </label>
                    <input type="checkbox"
                           name="clever_sms_options[notification_post]" <?php if (isset($optionsSave['notification_post']) && $optionsSave['notification_post'] == 'on') echo 'checked' ?> />
                </div>
            </div>
            <input type="submit" name="saveConfiguration" class="button-primary" value="Modifier la configuration"/>
        </form>

        <i>Les champs marqués d'un * sont obligatoires.</i>
    <?php
    }


}