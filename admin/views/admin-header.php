<?php
/**
 * This function renders plugin settings page header cntaing plugin information and help links
 */
function render_admin_header() {
    ?>
    <div class="header_div">
        <h2>LoginApp <?php _e( 'Social Plugin Settings', 'LoginApp' ) ?></h2>
        <div id="loginAppError" style="background-color: #FFFFE0; border:1px solid #E6DB55; padding:5px; margin-bottom:5px; width: 1024px;">
            <?php _e( 'Please clear your browser cache, if you have trouble loading the plugin interface. For more information', 'LoginApp' ) ?> <a target="_blank" href="http://ish.re/BBR5" >  <?php _e( 'click here', 'LoginApp' ) ?> </a>.
        </div>
    </div>
    <div class="clr"></div>
    <?php
}
