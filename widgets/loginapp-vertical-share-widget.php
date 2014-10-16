<?php
// Exit if called directly
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * This class is responsible for creating Vertical Share widget.
 */
class LoginApp_Vertical_Share_Widget extends WP_Widget {

    /**
     *  constructor for class LoginApp_Vertical_Share_Widget
     */
    function LoginApp_Vertical_Share_Widget() {
        parent::WP_Widget( 'LoginAppVerticalShare' /* unique id */, 'LoginApp - Vertical Sharing' /* title displayed at admin panel */, array('description' => __( 'Share post/page with Facebook, Twitter, Yahoo, Google and many more', 'LoginApp' )) /* Additional parameters */ );
    }

    /**
     * This is rendered widget content
     */
    function widget( $args, $instance ) {
        extract( $args );
        if ( $instance['hide_for_logged_in'] == 1 && is_user_logged_in() ) {
            return;
        }
        echo '<div class="loginRadiusVerticalSharing"></div>';
    }

    /**
     * Everything which should happen when user edit widget at admin panel
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['hide_for_logged_in'] = isset( $new_instance['hide_for_logged_in'] ) ? isset( $new_instance['hide_for_logged_in'] ) : 0 ;
        return $instance;
    }

    /**
     * Widget edit form at admin panel
     */
    function form( $instance ) {
        $defaults = array('hide_for_logged_in' => '1');
        foreach ( $instance as $key => $value ) {
            $instance[$key] = esc_attr( $value );
        }
        $instance = wp_parse_args( ( array ) $instance, $defaults );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>"><?php _e( 'Hide for logged in users:', 'LoginApp' ); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>" name="<?php echo $this->get_field_name( 'hide_for_logged_in' ); ?>" type="text" value='1' <?php if ( $instance['hide_for_logged_in'] == 1 ) echo 'checked="checked"'; ?> />
        </p>
        <?php
    }

}

add_action( 'widgets_init', create_function( '', 'return register_widget(  "LoginApp_Vertical_Share_Widget"  );' ) );
