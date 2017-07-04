<?php


class Med_Timer_Widget extends WP_Widget {

    /**
     * Constructor.
     *
     * @since Twenty Fourteen 1.0
     *
     * @return Twenty_Fourteen_Ephemera_Widget
     */
    public function __construct() {
        parent::__construct( 'widget_dm_timer', __( 'Meditation Timer', 'dm' ), array(
            'classname'   => 'widget_dm_timer',
            'description' => __( 'Use this widget to add meditation timer to your website', 'dm' ),
        ) );

    }

    
    public function widget( $args, $instance ) {
        $format = $instance['format'];
        $number = empty( $instance['number'] ) ? 2 : absint( $instance['number'] );
        $title  = apply_filters( 'widget_title', empty( $instance['title'] ) ? $this->format_strings[ $format ] : $instance['title'], $instance, $this->id_base );

        echo do_shortcode('[medtimer class="mtwidget"]');
    }

    /**
     * Deal with the settings when they are saved by the admin.
     *
     * Here is where any validation should happen.
     *
     * @since Twenty Fourteen 1.0
     *
     * @param array $new_instance New widget instance.
     * @param array $instance     Original widget instance.
     * @return array Updated widget instance.
     */
    function update( $new_instance, $instance ) {
        $instance['title']  = strip_tags( $new_instance['title'] );
        $instance['number'] = empty( $new_instance['number'] ) ? 2 : absint( $new_instance['number'] );
        if ( in_array( $new_instance['format'], $this->formats ) ) {
            $instance['format'] = $new_instance['format'];
        }

        return $instance;
    }

    /**
     * Display the form for this widget on the Widgets page of the Admin area.
     *
     * @since Twenty Fourteen 1.0
     *
     * @param array $instance
     * @return void
     */
    function form( $instance ) {
        $title  = empty( $instance['title'] ) ? '' : esc_attr( $instance['title'] );
        $number = empty( $instance['number'] ) ? 2 : absint( $instance['number'] );
        $format = isset( $instance['format'] ) && in_array( $instance['format'], $this->formats ) ? $instance['format'] : 'aside';
        ?>
            <p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php _e( 'Title:', 'dm' ); ?></label>
            <input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>"></p>
        <?php
    }
}

function register_timer_widgets() {
    register_widget( 'Med_Timer_Widget' );
}

add_action( 'widgets_init', 'register_timer_widgets' );