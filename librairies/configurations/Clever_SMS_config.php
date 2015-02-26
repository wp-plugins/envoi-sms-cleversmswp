<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 08/01/15
 * Time: 11:49
 */
class Clever_SMS_config
{
    private $messages;

    public function __construct(){

        $this->messages=array();
    }

    /**
     * Fonction qui vérifie si Curl est installé.
     * @return bool
     */
    private  function is_curlinstalled()
    {
        if (in_array('curl', get_loaded_extensions())) {
            return true;
        }
        $this->messages[]="L'extension Php Curl doit être activéee";
        return false;
    }

    /**
     * Fonction qui vérifie si le plugin est configuré via le menu réglage.
     * @return bool Retourne True si le plugin est configuré, sinon False.
     */
    private  function is_setting_done()
    {
        $options = get_option('clever_sms_options');
        if ($options != null && !empty($options)) {
            return true;
        }
        $this->messages[]="La configuration du plugin doit être effectuée via le menu réglages";
        return false;
    }

    /**
     * Fonction qui  détermine si la configuration est complète pour faire appel au Web services.
     * @return bool Retourne True si la configuration est complète, sinon False.
     *
     */
    public  function is_configuration_completed()
    {
        //On appelle les deux fonctions distinctement pour remonter tous les messages d'erreurs et non un par un.
        $is_curlinstalled=$this->is_curlinstalled();
        $is_setting_done= $this->is_setting_done();
        return$is_setting_done && $is_curlinstalled;
    }

    /**
     * @return array
     */
    public function getMessages()
    {
        return $this->messages;
    }



}


