<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 26/01/15
 * Time: 14:45
 */
class Clever_SMS_include
{
    public function __construct()
    {
        include_once plugin_dir_path(__FILE__) . '/views/settings/Clever_SMS_settings.php';
        include_once plugin_dir_path(__FILE__) . '/views/menu/Clever_SMS_menu.php';
        include_once plugin_dir_path(__FILE__) . '/views/account/Clever_SMS_account_Info.php';
        include_once plugin_dir_path(__FILE__) . '/views/account/Clever_SMS_buy_credit.php';
        include_once plugin_dir_path(__FILE__) . '/views/sms/Clever_SMS_send_sms.php';
        include_once plugin_dir_path(__FILE__) . '/views/sms/Clever_SMS_Historic_sms.php';
        include_once plugin_dir_path(__FILE__) . '/librairies/configurations/Clever_SMS_config.php';
        include_once plugin_dir_path(__FILE__) . '/librairies/api/CurlRequest.php';
        include_once plugin_dir_path(__FILE__) . '/librairies/api/Clever_SMS_api.php';
        include_once plugin_dir_path(__FILE__) . '/librairies/handlers/Clever_SMS_response_handler.php';
        include_once plugin_dir_path(__FILE__) . '/Clever_SMS_uninstall.php';
    }
}