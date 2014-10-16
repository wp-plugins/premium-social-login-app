<?php
// Exit if called directly
if ( !defined( 'ABSPATH' ) ) {
    exit();
}

/**
 * This class is responsible for creating Social Linking widget.
 */
class LoginApp_Social_Linking_Widget extends WP_Widget {

    /**
     * Constructor for class LoginApp_Social_Linking_Widget
     */
    function LoginApp_Social_Linking_Widget() {
        parent::WP_Widget( 'LoginAppSocialLinking' /* unique id */, 'LoginApp - Social Linking' /* title displayed at admin panel */, array('description' => __( 'Link your Social Accounts', 'LoginApp' )) /* Additional parameters */ );
    }

    /**
     *  This is rendered widget content 
     */
    function widget( $args, $instance ) {
        extract( $args );

        echo $before_widget;

        if ( !empty( $instance['title'] ) ) {
            $title = apply_filters( 'widget_title', $instance['title'] );
            echo $before_title . $title . $after_title;
        }
        if ( !empty( $instance['before_widget_content'] ) ) {
            echo $instance['before_widget_content'];
        }
        echo Login_App_Shortcode:: linking_widget_shortcode();

        if ( !empty( $instance['after_widget_content'] ) ) {
            echo $instance['after_widget_content'];
        }
        echo $after_widget;
    }

    /**
     *  Everything which should happen when user edit widget at admin panel 
     */
    function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
        $instance['title'] = strip_tags( $new_instance['title'] );
        $instance['before_widget_content'] = $new_instance['before_widget_content'];
        $instance['after_widget_content'] = $new_instance['after_widget_content'];
        return $instance;
    }

    /**
     * Widget edit form at admin panel 
     */
    function form( $instance ) {
        /* Set up default widget settings. */
        $defaults = array('title' => 'Social Linking', 'before_widget_content' => '', 'after_widget_content' => '');
        foreach ( $instance as $key => $value ) {
            $instance[$key] = esc_attr( $value );
        }
        $instance = wp_parse_args( ( array ) $instance, $defaults );
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'LoginApp' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo $instance['title']; ?>" />
            <label for="<?php echo $this->get_field_id( 'before_widget_content' ); ?>"><?php _e( 'Before widget content:', 'LoginApp' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'before_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'before_widget_content' ); ?>" type="text" value="<?php echo $instance['before_widget_content']; ?>" />
            <label for="<?php echo $this->get_field_id( 'after_widget_content' ); ?>"><?php _e( 'After widget content:', 'LoginApp' ); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id( 'after_widget_content' ); ?>" name="<?php echo $this->get_field_name( 'after_widget_content' ); ?>" type="text" value="<?php echo $instance['after_widget_content']; ?>" />
        </p>
        <?php
    }

}

add_action( 'widgets_init', create_function( '', 'return register_widget ( "LoginApp_Social_Linking_Widget" );' ) );
