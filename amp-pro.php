<?php

/*
 * Plugin Name: Amp Pro
 * Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
 * Description: Plugin to extend AMP features and tracking
 * Version: 1.0
 * Author: iruneitoiz
 * Author URI: irune.io
 * License: GPL2
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

require_once 'modules/amp-adsense.php';
class ampProSettings
{

    /**
     * Initialise the filters and actions
     *
     */
    function __construct() {
        add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array($this, 'addSettingsLink') );
        add_action( 'admin_menu', array($this, 'addOptionsPage') );
        add_action( 'admin_init',  array($this, 'registerSettings') );

    }

    /**
     * Filter the plugin's action links to add our settings page.
     *
     * @since 0.0.1
     *
     * @param $links
     *
     * @return array
     */
    function addSettingsLink( $links ) {
        return array_merge( array( 'settings' => '<a href="' . admin_url( 'options-general.php?page=amp-pro.php' ) . '">' . __( 'Settings', 'amp-pro' ) . '</a>' ), $links );
    }


    /**
     * Add options page.
     *
     * @since 0.0.1
     */
    function addOptionsPage() {
        add_options_page( 'AMP Pro', 'AMP Pro', 'manage_options', 'amp-pro.php', array ($this, 'renderOptionsPage') );
    }


    /**
     * Register settings, sections, and fields.
     *
     * @since 0.0.1
     */
    function registerSettings() {

        register_setting( 'amp-pro', 'amp_pro_adsense_settings', array ($this, 'amp_pro_settings_sanitize_callback') );

        //Settings for Advertising
        add_settings_section( 'amp_pro_adsense_settings', __( 'Google Adsense', 'amp-pro' ), array ($this, 'googleAdSenseCallback'), 'amp-pro' );

        add_settings_field( 'amp_pro_adsense_account', __( 'Google AdSense ID: ', 'amp-pro' ), array ($this, 'adSenseAccountCallback'), 'amp-pro', 'amp_pro_adsense_settings' );
        add_settings_field( 'amp_pro_adsense_adslot', __( 'Google AdSense AdSlot: ', 'amp-pro' ), array ($this, 'adSenseAdSlotCallback'), 'amp-pro', 'amp_pro_adsense_settings' );
        add_settings_field( 'amp_pro_adsense_paragraphs', __( 'Paragraphs between ads: ', 'amp-pro' ), array ($this, 'adSenseParagraphsCallback'), 'amp-pro', 'amp_pro_adsense_settings' );

    }


    /**
     * Render options page.
     *
     * @since 0.0.1
     */
    function renderOptionsPage() {
        ?>
        <form action='options.php' method='post' enctype='multipart/form-data'>

            <h1>AMP Analytics & AdSense integration</h1>
            <?php
            settings_fields( 'amp-pro' );
            do_settings_sections( 'amp-pro' );
            submit_button();
            ?>
        </form>
        <?php
    }

    /**
     * Google Analytics section callback.
     *
     * @since 0.0.1
     */
    function googleAnalyticsCallback() {}


    /**
     * Google AdSense section callback.
     *
     * @since 0.0.1
     */
    function googleAdSenseCallback() {}

    /**
     * Google AdSense account callback.
     *
     */
    function adSenseAccountCallback()
    {
        $options = get_option( 'amp_pro_adsense_settings' );

        ?>
        <input type='text' name='amp_pro_adsense_settings[amp_pro_adsense_account]' value='<?php echo $options['amp_pro_adsense_account']; ?>'>
        <?php
    }


    /**
     * Google AdSense AdSlot account callback.
     *
     */
    function adSenseAdSlotCallback()
    {
        $options = get_option( 'amp_pro_adsense_settings' );
        ?>
        <input type='text' name='amp_pro_adsense_settings[amp_pro_adsense_adslot]' value='<?php echo $options['amp_pro_adsense_adslot']; ?>'>
        <?php
    }


    /**
     * How many paragraphs in between ads
     *
     */
    function adSenseParagraphsCallback()
    {
        $options = get_option( 'amp_pro_adsense_settings' );
        ?>
        <input type='text' name='amp_pro_adsense_settings[amp_pro_adsense_paragraphs]' value='<?php echo $options['amp_pro_adsense_paragraphs']; ?>'>
        <?php
    }

    /**
     * Sanitizes settings before they get to the database.
     *
     * @since 0.0.1
     *
     * @param $input array Options array.
     *
     * @return array Sanitized, database-ready options array.
     */
    function amp_pro_adsense_settings_sanitize_callback( $input ) {

        if ( $input['amp_pro_adsense_account'] ) {
            $input['amp_pro_adsense_account'] = sanitize_text_field( $input['amp_pro_adsense_account'] );
        }

        if ( $input['amp_pro_adsense_adslot'] ) {
            $input['amp_pro_adsense_adslot'] = sanitize_text_field( $input['amp_pro_adsense_adslot'] );
        }

        if ( $input['amp_pro_adsense_paragraphs'] ) {
            $input['amp_pro_adsense_paragraphs'] = intval( $input['amp_pro_adsense_paragraphs'] );
        }

        return $input;
    }

}

new ampProSettings();

