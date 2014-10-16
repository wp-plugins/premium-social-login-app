<?php

//if uninstall not called from WordPress, exit
if ( !defined( 'WP_UNINSTALL_PLUGIN' ) ) {
    exit();
}

if( !is_multisite()) {
    $loginAppSettings = get_option( 'LoginApp_settings' );
    if ( $loginAppSettings['delete_options'] == 1 ) {
        delete_loginapp_options();
    }
} else {
    global $wpdb;
    $blog_ids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" );
    $original_blog_id = get_current_blog_id();
    foreach ( $blog_ids as $blog_id ) {
        switch_to_blog( $blog_id );
        $loginAppSettings = get_option( 'LoginApp_settings' );
        if ( $loginAppSettings['delete_options'] == 1 ) {
            delete_loginapp_options();
        }
    }
    switch_to_blog( $original_blog_id );
}   

function delete_loginapp_options() {
    global $wpdb;
    delete_option( 'LoginApp_settings' );
    delete_option( 'loginapp_db_version' );
    $wpdb->query( "delete from $wpdb->usermeta where meta_key like 'loginapp%'" );
}