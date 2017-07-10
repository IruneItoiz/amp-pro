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

require_once 'modules/amp-analytics.php';

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
        add_options_page( 'AMP Pro Analytics', 'AMP Pro Analytics', 'manage_options', 'amp-pro.php', array ($this, 'renderOptionsPage') );
    }


    /**
     * Register settings, sections, and fields.
     *
     * @since 0.0.1
     */
    function registerSettings() {

        add_settings_section( 'amp_pro_google_analytics', __( 'Google Analytics', 'amp-pro-analytics' ), array ($this, 'googleAnalyticsCallback'), 'amp-pro' );

        add_settings_field( 'amp_pro_analytics_ga_ua', __( 'Google Analytics ID: <br/><em><a href="https://support.google.com/analytics/answer/1032385?hl=en" target="_blank">Need help finding your tracking ID?</a></em>', 'amp-analytics' ), array ($this, 'gaUACallback'), 'amp-pro', 'amp_pro_google_analytics' );

        add_settings_field( 'amp_pro_analytics_outbound', __( 'Track outbound links?: ', 'amp-analytics' ), array ($this, 'outboundTrackingCallback'), 'amp-pro', 'amp_pro_google_analytics' );

        add_settings_field( 'amp_pro_analytics_amazon', __( 'Track only Amazon links?: ', 'amp-analytics' ), array ($this, 'amazonTrackingCallback'), 'amp-pro', 'amp_pro_google_analytics' );

        register_setting( 'amp_pro_analytics_settings', 'amp_pro_analytics_settings', array ($this, 'amp_analytics_settings_sanitize_callback') );

    }


    /**
     * Render options page.
     *
     * @since 0.0.1
     */
    function renderOptionsPage() {
        ?>
        <form action='options.php' method='post' enctype='multipart/form-data'>

            <h1>AMP Analytics</h1>

            <?php
            settings_fields( 'amp_pro_analytics_settings' );
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
     * Google Analytics UA setting callback.
     *
     * @since 0.0.1
     */
    function gaUACallback() {
        $options = get_option( 'amp_pro_analytics_settings' );
        ?>
        <input type='text' name='amp_pro_analytics_settings[amp_pro_analytics_ga_ua]' value='<?php echo $options['amp_pro_analytics_ga_ua']; ?>'>
        <?php
    }

    /**
     * Event tracking callback.
     *
     * @since 0.0.1
     */
    function outboundTrackingCallback() {
        $options = get_option( 'amp_pro_analytics_settings' );
        ?>
        <input type='checkbox' name='amp_pro_analytics_settings[amp_pro_analytics_outbound]' value='1'
            <?php
            if ($options['amp_pro_analytics_outbound']) echo 'checked';
            ?>
        >
        <?php
    }


    /**
     * Only Amazon Event tracking callback.
     *
     * @since 0.0.1
     */
    function amazonTrackingCallback() {
        $options = get_option( 'amp_pro_analytics_settings' );
        ?>
        <input type='checkbox' name='amp_pro_analytics_settings[amp_pro_analytics_amazon]' value='1'
            <?php
            if ($options['amp_pro_analytics_amazon']) echo 'checked';
            ?>
        >
        <?php


    }

    /**
     * Sanitizes settings before they get to the database.
     *
     * @since 0.0.2
     *
     * @param $input array Options array.
     *
     * @return array Sanitized, database-ready options array.
     */
    function amp_pro_analytics_settings_sanitize_callback( $input ) {

        if ( $input['amp_pro_analytics_ga_ua'] ) {
            $input['amp_pro_analytics_ga_ua'] = sanitize_text_field( $input['amp_pro_analytics_ga_ua'] );
        }

        if ( $input['amp_pro_analytics_outbound'] ) {
            $input['amp_pro_analytics_outbound'] = 1;
        }

        if ( $input['amp_pro_analytics_amazon'] ) {
            $input['amp_pro_analytics_amazon'] = 1;
        }

        return $input;
    }
}

new ampProSettings();

