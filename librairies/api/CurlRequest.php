<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 31/12/14
 * Time: 11:23
 */
class CurlRequest
{

   private static $ressource = "http://webserviceslight.clever.fr/api/";
//   private static $ressource = "http://localhost/WebServiceCLeverSMSLight/web/app_dev.php/api/";



    /**
     *
     *Fonction qui initialise une session de curl et qui fournit un identifiant
     * @param $url Complément de l'URL de la requête
     * @param $method Methode de la requête
     */
    public static function curlInitialize($url, $method,$data_string=null)
    {

        $ch = curl_init( self::$ressource . $url);

        if (strcasecmp ($method,'post' )==0){
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        }
       curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, self::buildHeaderRequest());
        return $ch;
    }


    /**
     *
     * Fonction qui exécute la requête sur le serveur distant
     * @return array Tableau du resultat
     */
    public static function curlExec($ch)
    {

        $response = curl_exec($ch);
        $status = curl_getinfo($ch);
        curl_close($ch);
        return array('response'=>$response, 'status'=>$status);

    }


    /**
     * Fonction qui construit le header de la requête HTTP
     * @return array Header de la requête
     */
    public static function buildHeaderRequest()
    {
        $optionsSave = get_option('clever_sms_options');
        $login = $optionsSave['login'];
        $password = $optionsSave['password'];
        $authorization = base64_encode($login . ':' . $password);
        return array('Content-Type: application/json', 'Accept: application/json', 'Authorization: Basic ' . $authorization);
    }

}