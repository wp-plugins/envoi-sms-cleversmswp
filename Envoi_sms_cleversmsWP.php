<?php

/*
Plugin Name: Envoi-sms-cleversmsWP
Plugin URI: http://cleversmslight.fr/solution-sms/sms-wordpress/
Description: Le plugin Envoi-sms-cleversmsWP vous permet d'envoyer des SMS de façon unitaire ou groupée. Elle vous donne la possibilité de personnaliser l'émetteur lors de vos envois, d'envoyer des messages longs de plus de 160 caractères sans troncature, d'encoder vos messages en latin(français..) ou UCS2 (Cyrillique, Chinois, Arabe...). Vous pouvez suivre en temps réél l'acheminement des SMS, gérer les STOP, récupérer les réponses.
Version: 1.0
Author: Clever Technologies
Author URI: http://www.clever.fr
*/

include_once plugin_dir_path(__FILE__) . '/Clever_SMS_include.php';

DEFINE('Clever_SMS_PLUGIN_DIR', basename(dirname(__FILE__)));

class Envoi_sms_cleversmsWP
{

    /**
     * Constructeur de la classe qui fait appel à la fonction d'initialisation de l'extension
     */
    public function __construct()
    {

        $this->init();
    }

    /**
     * Fonction d'initialisation de l'extension
     */
    public function init()
    {
        //Inclusion des fichiers
        new Clever_SMS_include();
        //Ajout des fichiers CSS
        add_action('admin_init', array($this, 'load_CSS'));
        //Ajout des fichiers JS
        add_action('admin_init', array($this, 'load_JS'));
        //Ajout de l'extension dans le menu réglage de wordpress
        new Clever_SMS_settings();
        //Ajout de l'extension dans la barre de menu général
        new Clever_SMS_menu();
        //Gestion de la suppresion de l'extension
        register_uninstall_hook(__FILE__, array('Clever_SMS_uninstall',
            'uninstall'));
    }


    /**
     * Chargement des fichiers JavaScript
     */
    public function load_JS(){
        wp_enqueue_script('timepicker',plugins_url( '/js/jquery-ui-timepicker-addon.js', __FILE__ ), array('jquery', 'jquery-ui-core', 'jquery-ui-slider', 'jquery-ui-datepicker', ) );
        wp_enqueue_script('Clever_SMS_function.js', plugins_url( '/js/functions.js',__FILE__ ));
    }

    /**
     * Chargement des fichiers CSS
     */
    public function load_CSS(){
        wp_enqueue_style('Clever_SMS_style.css',plugins_url( '/css/style.css', __FILE__ ));
        wp_enqueue_style('Clever_SMS_jq_ui.css',plugins_url( '/css/jquery-ui.min.css', __FILE__ ));
    }
}


//************************************Partie Principale***********************************//
new Envoi_sms_cleversmsWP();