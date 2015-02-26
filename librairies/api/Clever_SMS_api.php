<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 30/12/14
 * Time: 17:05
 */
class Clever_SMS_api
{


    public function __construct()
    {

    }

    /**
     * Fonction qui recupère les informations d'un compte
     */
    public function clever_sms_getUserInfo()
    {

        $curl = CurlRequest::curlInitialize('accounts/me', 'GET');
        return CurlRequest::curlExec($curl);
    }

    /**
     *
     * Fonction qui déclenche un push
     * @param $text
     * @param $phone
     * @param null $dateSend
     * @param int $encoding
     * @return mixed
     */
    public function clever_sms_sendPush($text, $phone, $dateSend = null, $encoding = 3)
    {
        $options = get_option('clever_sms_options');
        $oadc = $options['transmitter'];
        $obj = self::buildDataSMS($text, $phone, $dateSend, $oadc, $encoding);
        $curl = CurlRequest::curlInitialize('pushs', 'POST', (json_encode($obj)));
        return CurlRequest::curlExec($curl);
    }


    /**
     * Fonction d'envoi de SMS déclenchée lors d'un nouveau commentaire sur le blog
     */
    public function clever_sms_notifyComment()
    {
        //Récupération des options de notre plugins
        $options = get_option('clever_sms_options');
        //Si l'utilisateur a demandé à être averti en cas de commentaire et s'il a fourni son téléphone,
        //On envoi une notification à son numéro de téléphone
        if (isset($options['notification_comment']) && $options['notification_comment'] == 'on' && isset($options['phone'])) {

            $text = "Un nouveau commentaire vient d'être ajouté sur " . get_bloginfo('name') . ' à ' . date("H:i");
            $oadc = $options['transmitter'];
            $obj = self::buildDataSMS($text, array($options['phone']), null, $oadc);
            //Envoi de la requête au serveur
            $curl = CurlRequest::curlInitialize('pushs', 'POST', (json_encode($obj)));
            CurlRequest::curlExec($curl);
        }
    }


    /**
     * Fonction d'envoi de SMS déclenchée lors d'un nouveau Post sur le blog
     */
    public function clever_sms_notifyPost()
    {
        //Récupération des options de notre plugins
        $options = get_option('clever_sms_options');
        //Si l'utilisateur a demandé à être averti en cas de post et s'il a fourni son téléphone,
        //On envoi une notification à son numéro de téléphone
        if (isset($options['notification_post']) && $options['notification_post'] == 'on' && isset($options['phone'])) {

            $text = "Un nouvel article vient d'être ajouté sur " . get_bloginfo('name') . ' à ' . date("H:i");
            $oadc = $options['transmitter'];
            $obj = self::buildDataSMS($text, array($options['phone']), null, $oadc);
            //Envoi de la requête au serveur
            $curl = CurlRequest::curlInitialize('pushs', 'POST', (json_encode($obj)));
            CurlRequest::curlExec($curl);

        }
    }

    /**
     * Fonction qui construit l'objet SMS
     * @param $text Texte du SMS
     * @param $phone Tableau des destinataires
     * @param null $dateSend Heure d'envoi des SMS
     * @param null $oadc Emetteur de l'envoi
     * @param int $encoding encodage du SMS
     * @return stdClass Objet SMS
     */

    private function buildDataSMS($text, $phone, $dateSend = null, $oadc = null, $encoding = 3)
    {

        $obj = new stdClass;
        $datas = new stdClass;
        $datas->text = $text;
        $datas->sendDate = $dateSend;
        $datas->shortcode = $oadc;
        //Encodage du message (latin dans notre cas)
        $datas->encoding = $encoding;
        //Numéro des destinataires
        $datas->phoneNumbers = $phone;
        $obj->datas = $datas;
        return $obj;

    }


    /**Fonction qui permet d'obtenir les tarifs
     * @return array Tableau des tarifs
     */
    public function clever_sms_getTarifs()
    {


        $curl = CurlRequest::curlInitialize('tarifs', 'GET');
        return CurlRequest::curlExec($curl);

    }

    /**
     * Fonction de récupération des pushs des 7 derniers jours
     * @return array tableau des Pushs des 7 derniers jours
     */
    public function clever_sms_get_currentPushs()
    {

        $curl = CurlRequest::curlInitialize('statistics/currents', 'GET');
        return CurlRequest::curlExec($curl);
    }

} 