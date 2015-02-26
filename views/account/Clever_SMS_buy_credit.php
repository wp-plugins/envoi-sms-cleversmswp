<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 19/01/15
 * Time: 12:05
 * Classe qui affiche les packs de SMS disponibles
 */
class Clever_SMS_buy_credit
{

    /**
     *Fonction d'affichage des tarifs existants
     */
    public function display_tarif()
    {

        $conf = new Clever_SMS_config();
        //Verification de la configuration du plugin et de l'activation des modules de PHP nécessaires
        if ($conf->is_configuration_completed()) {
            $result = $this->process_request();
            $this->process_response($result);
        } else {
            //Afichage des erreurs si le plugin est mal configuré
            Clever_SMS_response_handler::print_errors($conf->getMessages());
        }

    }

    /**
     * Fonction qui récupère les tarifs existants
     * @return array Tableau des tarifs
     */
    public function process_request()
    {
        //Instance de la classe qui fait appel aux Web Services
        $api = new Clever_SMS_api();
        //Appel de la fonction de récupération d'information du compte
        return $api->clever_sms_getTarifs();
    }


    /**
     * Fonction qui analyse l'entête de la réponse et affiche le resultat
     * @param $result Tableau contenant les tarifs
     */
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
     * Fonction d'affichage des tarifs sous forme de tableau
     * @param $objects
     */
    public function print_message_success($objects)
    {

        ?>

        <div class="clever_sms_page">
            <h2>TARIFS POUR ENVOYER DES SMS</h2>
            <?php
            $errors = array();
            if ($_POST != NULL && isset($_POST['nbSMS'])) {
                $errors = $this->validate_estimation_sms();
                if (empty($errors)) {
                    //faire le calcule ici
                    $tarif = $this->estimateSendingSMS($objects);

                }
            }


            Clever_SMS_response_handler::print_errors($errors);?>
            <table class="clever_sms_table clever_sms_table_medium">
                <thead>
                <tr>
                    <th>Quantité SMS</th>
                    <th>Prix unitaire (HT) en €</th>
                    <!--                    <th>Prix Total (HT) en €</th>-->
                </tr>
                </thead>
                <tbody>
                <?php
                foreach ($objects->tarifs as $object) {
                    echo '<tr>' .
                        '<td>' . $object->low . ' à ' . $object->top . '</td>' .
                        '<td>' . str_replace(',000', ',00', number_format($object->price, 3, ',', ' ')) . '</td>' .
//                        '<td>' . str_replace(',000', ',00', number_format($object->top * $object->price, 3, ',', ' ')) . '</td>' .
                        '</tr>';
                }
                ?>
                </tbody>
            </table>
            Si vous souhaitez acheter une quantité supérieure de SMS, vous pouvez contacter le service commercial de
            Clever Technologies au (+33) 01 60 53 60 53

            <br/> <br/>

            <form id="estimation_form" method="post">
                <label for="nbSMS">Estimer le prix de votre campagne de SMS </label>
                <input type="text" name="nbSMS" id="nbSMS" placeholder="Rentrez le nombre de SMS à envoyer"
                       value="<?php echo @$_POST['nbSMS'] ?>"/>
                <input id="sendEstimation" type="submit" name="sendEstimation" class="button-primary"
                       value="Estimer la campagne"/>
            </form>

            <?php
            if (isset($tarif)) {
                $this->display_estimation($tarif, @$_POST['nbSMS']);

            }
            ?>


            <p>
                Procéder à l'achat sécurisé de crédit SMS en accédant directement à notre site <a
                    href="https://cleversmslight.clever.fr">https://cleversmslight.clever.fr</a>.<br/>
                Une fois que vous aurez effectué votre choix de quantité de SMS, vous serez mis en relation avec la
                banque pour effectuer un paiement sécurisé.<br/><br/>


            <div>
                <a href="https://www.cmcicpaiement.fr/fr/fonctionnalites/index.html"><img
                        src="<?php echo WP_PLUGIN_URL . '/' . Clever_SMS_PLUGIN_DIR . '/images/logo-cm-cic.png' ?>">
                </a>
            </div>
            <div style="width: 50%">
                <form action="https://cleversmslight.clever.fr/fr/login_check" method="post">
                    <?php
                    $options = get_option('clever_sms_options');
                    $login = $options['login'];
                    $password = $options['password'];
                    ?>
                    <input type="hidden" name="_username" value="<?php echo $login?>"/>
                    <input type="hidden" name="_password" value="<?php echo $password?>"/>
                    <input type="hidden" name="_nbsms" value="<?php echo  @$_POST['nbSMS']?>"/>

<!--                    <input style="width: 100%" id="sendEstimation" type="button" name="buy" class="button-primary" value="Acheter maintenant" onclick="location.href='https://cleversmslight.clever.fr' "/>-->
                    <input style="width: 100%" id="sendEstimation" type="submit" name="buy" class="button-primary" value="Acheter maintenant"/>
                </form>

            </div>
            </p>
        </div>
    <?php
    }

    /**
     * Validation du formulaire de l'estimation de la campagne
     * @return array Tableau des erreurs
     */
    public function validate_estimation_sms()
    {
        $errors = array();
        if (!isset($_POST['nbSMS']) || !is_numeric($_POST['nbSMS']) || $_POST['nbSMS'] < 500) {
            $errors[] = "Un <b>minimum</b> de 500 SMS est réquis pour l'estimation de la campagne";
        }
        if (isset($_POST['nbSMS']) && $_POST['nbSMS'] > 10000) {
            $errors[] = "Le nombre de SMS est supérieur à 10000. Merci de contacter le service commercial (email: commerciaux@clever.fr, Tél. (+33)1.60.53.60.53).";
        }
        return $errors;
    }


    /**
     * Affichage de la partie estimation
     * @param $object Tranche de tarif concernée
     * @param $nbSMS Nombre de SMS à envoyer
     */
    private function display_estimation($object, $nbSMS)
    {
        echo '<br/><b>Estimation personnalisée de votre campagne</b>';
        ?>
        <table class="clever_sms_table clever_sms_table_medium">
            <thead>
            <tr>
                <th>Quantité SMS</th>
                <th>Prix unitaire (HT) en €</th>
                <th>Prix Total (HT) en €</th>
            </tr>
            </thead>
            <tbody>
            <?php
            echo '<tr>' .
                '<td>' . $nbSMS . '</td>' .
                '<td>' . str_replace(',000', ',00', number_format($object->price, 3, ',', ' ')) . '</td>' .
                '<td>' . str_replace(',000', ',00', round($nbSMS * $object->price, 2)) . '</td>' .
                '</tr>';

            ?>
            </tbody>
        </table>
    <?php

    }

    /**
     * Fonction qui calcule le cout de la campagne à partir des nombres de SMS à
     * @param $objects Liste des tarifs
     * @return mixed Tranche de tarif concernée
     */
    private function estimateSendingSMS($objects)
    {
        $nbSMS = $_POST['nbSMS'];
        foreach ($objects->tarifs as $object) {
            if ($nbSMS >= $object->low && $nbSMS <= $object->top) {
                return $object;
            }
        }

    }
}