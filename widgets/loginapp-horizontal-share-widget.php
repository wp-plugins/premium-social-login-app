<?php
// Exit if called directly
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * This class is responsible for creating Social Share Horizontal widget.
 */
class LoginApp_Horizontal_Share_Widget extends WP_Widget {

    /**
     * Constructor for class LoginApp_Horizontal_Share_Widget
     */
    function LoginApp_Horizontal_Share_Widget() {
        parent::WP_Widget( 'LoginAppHorizontalShare' /* unique id */, 'LoginApp - Horizontal Sharing' /* title displayed at admin panel */, array('description' => __( 'Share post/page with Facebook, Twitter, Yahoo, Google and many more', 'LoginApp' )) /* Additional parameters */ );
    }

    /**
     * This is rendered widget content
     */
    function widget( $args, $instance ) {
        extract( $args );
        global $loginAppSettings;
        if ( $instance['hide_for_logged_in'] == 1 && is_user_logged_in() ) {
            return;
        }
        if ( isset( $loginAppSettings['horizontal_shareEnable'] ) && $loginAppSettings['horizontal_shareEnable'] == '1' ) {
            echo $before_widget;
            if ( !empty( $instance['before_widget_content'] ) ) {
                echo $instance['before_widget_content'];
            }
            echo '<div class="loginRadiusHorizontalSharing"></div>';

            if ( !empty( $instance['after_widget_content'] ) ) {
                echo $instance['after_widget_content'];
            }
            echo $after_widget;
        } else {
            return;
        }
    }

    /**
     * Everything which should happen when user edit widget at admin panel
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['before_widget_content'] = $new_instance['before_widget_content'];
        $instance['after_widget_content'] = $new_instance['after_widget_content'];
        $instance['hide_for_logged_in'] = isset( $new_instance['hide_for_logged_in'] ) ? $new_instance['hide_for_logged_in'] : 0;

        return $instance;
    }

    /**
     * Widget edit form at admin panel
     */
    function form( $instance ) {
        /* Set up default widget settings. */
        $defaults = array('before_widget_content' => '', 'after_widget_content' => '', 'hide_for_logged_in' => '1');

        foreach ( $instance as $key => $value ) {
            $instance[$key] = esc_attr( $value );
        }

        $instance = wp_parse_args( ( array ) $instance, $defaults );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'before_widget_content' ); ?>"><?php _e( 'Before widget content:', 'LoginApp' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'before_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'before_widget_content' ); ?>" type="text" value="<?php echo $instance['before_widget_content']; ?>" />
            <label for="<?php echo $this->get_field_id( 'after_widget_content' ); ?>"><?php _e( 'After widget content:', 'LoginApp' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'after_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'after_widget_content' ); ?>" type="text" value="<?php echo $instance['after_widget_content']; ?>" />
            <br /><br />
            <label for="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>"><?php _e( 'Hide for logged in users:', 'LoginApp' ); ?></label>
            <input type="checkbox" id="<?php echo $this->get_field_id( 'hide_for_logged_in' ); ?>" name="<?php echo $this->get_field_name( 'hide_for_logged_in' ); ?>" type="text" value='1' <?php if ( $instance['hide_for_logged_in'] == 1 ) echo 'checked="checked"'; ?> />
        </p>
        <?php
    }

}

add_action( 'widgets_init', create_function( '', 'return register_widget( "LoginApp_Horizontal_Share_Widget" );' ) );
