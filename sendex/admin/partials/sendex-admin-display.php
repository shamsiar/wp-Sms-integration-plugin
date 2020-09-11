<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link        http://menusms.com/dongido
 * @since      1.0.0
 *
 * @package    Sendex
 * @subpackage Sendex/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<form method="POST" action='options.php'>
   <?php
         settings_fields($this->plugin_name);
         do_settings_sections('sendex-settings-page');

         submit_button();  
   ?>
</form>