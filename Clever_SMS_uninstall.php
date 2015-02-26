<?php
/**
 * Created by PhpStorm.
 * User: clever
 * Date: 26/01/15
 * Time: 14:21
 */
class Clever_SMS_uninstall{

    /**
     * Fonction appelée lors de la suppression de l'extension
     */
    public static function uninstall()
    {
        delete_option("clever_sms_options");
}
}