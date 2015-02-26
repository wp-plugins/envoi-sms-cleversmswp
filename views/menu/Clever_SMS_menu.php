<?php

/**
 * Created by PhpStorm.
 * User: clever
 * Date: 30/12/14
 * Time: 15:43
 */
class Clever_SMS_menu
{


    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_admin_menu'));
    }


    public function add_admin_menu()
    {

        add_menu_page("Envoyer vos SMS avec Clever-SMS!", "Clever-SMS", "manage_options", 'clever_sms_envoi', array($this, 'display_send_sms'), ' dashicons-format-chat');
        add_submenu_page('clever_sms_envoi', 'Envoi de SMS', 'Envoi de SMS', 'manage_options', 'clever_sms_envoi', array($this, 'display_send_sms'));
        add_submenu_page('clever_sms_envoi', 'Historique envois', 'Historique envois', 'manage_options', 'clever_sms_historique', array($this, 'display_historic_sms'));
        add_submenu_page('clever_sms_envoi', 'Informations compte', 'Infos. du compte', 'manage_options', 'clever_sms_info_compte', array($this, 'display_account_info'));
        add_submenu_page('clever_sms_envoi', 'Achat crédit', 'Achat de crédit', 'manage_options', 'clever_sms_achat_credit', array($this, 'display_buy_credit'));

    }

    public function display_account_info()
    {
        $accInfo = new Clever_SMS_account_Info();
        $accInfo->display_account_info();
    }

    public function display_send_sms()
    {

        $sendMsg = new Clever_SMS_send_sms();
        $sendMsg->display_send_sms();
    }

    public function display_buy_credit()
    {
        $credit = new Clever_SMS_buy_credit();
        $credit->display_tarif();

    }

    public function display_historic_sms()
    {
        $historic = new Clever_SMS_Historic_sms();
        $historic->display_historic();
    }

    public function menu_html()
    {
        echo '<h1>' . get_admin_page_title() . '</h1>';
        echo '<p>Bienvenue sur la page d\'accueil du plugin</p>';
    }
}