<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 12/08/2014
 * Time: 15:24
 */

// register SiDi_Discography_Widget widget
function register_SiDi_Discography_Widget() {
    register_widget( 'SiDi_Discography_Widget' );
}
add_action( 'widgets_init', 'register_SiDi_Discography_Widget' );

require_once('classes/SiDi_Widget_Form.php');
/**
 * Adds Foo_Widget widget.
 */
class SiDi_Discography_Widget extends SiDi_Widget_Form {

    protected $values;

    /**
     * Register widget with WordPress.
     */
    function SiDi_Discography_Widget() {

        $this->values = array(
            'display'           => array(
                'label'     => __('Type Display : ', 'sidi'),
                'default'   => 'list',
                'type'      => 'select',
                'values'    => array(
                    'list'          => __('Vertical List', 'sidi'),
                    'thumbnail'     => __('Thumbnail', 'sidi'),
                )
            ),
            'posts_per_page'    => array(
                'label'     => __('Number of Album to Show: ', 'sidi'),
                'default'   => 0,
                'type'      => 'number',
                'attr'      => array(
                    'min' => -1,
                    'max' =>999,
                    'size' =>3
                )
            ),
            'date_format'       => array(
                'label'     => __('Format Date Display: ', 'sidi'),
                'default'   => 'Y',
                'type'      => 'text'),
            'cover_height'    => array(
                'label'     => __('Cover Height: ', 'sidi'),
                'default'   => 150,
                'type'      => 'number',
                'attr'      => array(
                    'min' => 0,
                    'max' =>999,
                    'size' =>3,
                    'step'  =>10,
                )
            ),
            'cover_width'    => array(
                'label'     => __('Cover Widht: ', 'sidi'),
                'default'   => 150,
                'type'      => 'number',
                'attr'      => array(
                    'min' => 0,
                    'max' =>999,
                    'size' =>3,
                    'step'  =>10,
                )
            ),
            'filter'       => array(
                'label'     => __('Category IDs ( 2,6 or 4) : ', 'sidi'),
                'default'   => '',
                'type'      => 'text'),
            'order_by'              => array(
                'label'     => __('Order By: ', 'sidi'),
                'default'   => 'release',
                'type'      => 'select',
                'values'    => array(
                    'date'      => __('Date of Create Album', 'sidi'),
                    'modified'  => __('Date of Modified Album', 'sidi'),
                    'rand'      => __('Random', 'sidi'),
                    'release'   => __('Date of Release', 'sidi'),
                    'title'     => __('Title', 'sidi')
                )
            ),
            'order'             => array(
                'label'     => __('Sort Order : ', 'sidi'),
                'default'   => 'DESC',
                'type'      => 'select',
                'values'    => array(
                    'ASC'      => __('Ascending (1, 2, 3)', 'sidi'),
                    'DESC'      => __('Descending (3, 2, 1)', 'sidi'),
                ),
            ),
            'dynamic'           => array (
                'label'     => __('Dynamic Display : ', 'sidi'),
                'default'   => 1,
                'type'      => 'checkbox',
                'values'    => 1
            ),
            'show_song'         => array (
                'label'     => __('Show the Discs : ', 'sidi'),
                'default'   => 1,
                'type'      => 'checkbox',
                'values'    => 1
            ),
            'show_title'         => array (
                'label'     => __('Show the Titles : ', 'sidi'),
                'default'   => 1,
                'type'      => 'checkbox',
                'values'    => 1
            ),
            'show_all'      => array (
                'label'     => __('Show like to all albums : ', 'sidi'),
                'default'   => 0,
                'type'      => 'checkbox',
                'values'    => 1
            )
        );
        $options = array(
            "classname"     => 'sidi_discography_widget',
            "description"   => __( 'Displays Albums with their thumbnails', 'sidi' )
        );
        parent::SiDi_Widget_Form(
            'sidi_dw', // Base ID
            __('Discography', 'sidi'), // Name
            $options
        );
    }

    /**
     * Front-end display of widget.
     *
     * @see WP_Widget::widget()
     *
     * @param array $args     Widget arguments.
     * @param array $instance Saved values from database.
     */
    public function widget( $args, $instance ) {
//        var_dump( $args, $instance );
        $title = apply_filters( 'widget_title', $instance['title'] );

        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        $shortcode = '[sidi-discography id="'.$args['widget_id'].'"';

        foreach($this->values as $key=>$val){
            if(isset($instance[$key]))
                $shortcode.= ' '.$key.='="'.$instance[$key].'"';
        }
        $shortcode.= ']';

        echo do_shortcode($shortcode);
        echo $args['after_widget'];
    }

    /**
     * Back-end widget form.
     *
     * @see WP_Widget::form()
     *
     * @param array $instance Previously saved values from database.
     */
    public function form( $instance ) {
        if ( isset( $instance[ 'title' ] ) ) {
            $title = $instance[ 'title' ];
        }
        else {
            $title = __( 'New title', 'text_domain' );
        }
        $values=array('title'=>array(
                'label' => __( 'Title:' ),
                'default' => '',
                'type' => 'text'))+$this->values;
        foreach ($values as $key=>&$val)
            $val['value']=isset($instance[ $key])?$instance[ $key]:$val['default'] ;
        unset($val);


//        $form = new SiDi_Widget_Form($this);
//        echo $form->get_form($values);
        echo $this->get_form($values);
    }

    /**
     * Sanitize widget form values as they are saved.
     *
     * @see WP_Widget::update()
     *
     * @param array $new_instance Values just sent to be saved.
     * @param array $old_instance Previously saved values from database.
     *
     * @return array Updated safe values to be saved.
     */
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';

        $instance['posts_per_page']     = ( isset( $new_instance['posts_per_page'] ) )  ? abs(intval($new_instance['posts_per_page'],10)) :0;
        $instance['cover_height']       = ( isset( $new_instance['cover_height'] ) )    ? abs(intval($new_instance['cover_height'],10)) :0;
        $instance['cover_width']        = ( isset( $new_instance['cover_width'] ) )     ? abs(intval($new_instance['cover_width'],10)) :0;
        $instance['date_format']        = ( isset( $new_instance['date_format'] ) )     ? strip_tags( $new_instance['date_format'] ):'Y';
        $instance['filter']             = ( isset( $new_instance['filter'] ) )          ? strip_tags( $new_instance['filter'] ):'';
        $instance['order_by']           = ( ! empty( $new_instance['order_by'] ) )      ? (array_key_exists($new_instance['order_by'], $this->values['order_by']['values'])? $new_instance['order_by'] : $this->values['order_by']['default']) :$this->values['order_by']['default'];
        $instance['order']              = ( ! empty( $new_instance['order'] ) )         ? (array_key_exists($new_instance['order'], $this->values['order']['values'])? $new_instance['order'] : $this->values['order']['default']) :$this->values['order']['default'];
        $instance['dynamic']            = ( isset( $new_instance['dynamic'] ) )         ? abs(intval($new_instance['dynamic'],10)):0;
        $instance['show_song']          = ( isset( $new_instance['show_song'] ) )       ? abs(intval($new_instance['show_song'],10)):0;
        $instance['show_title']         = ( isset( $new_instance['show_title'] ) )      ? abs(intval($new_instance['show_title'],10)):0;
        $instance['show_all']           = ( isset( $new_instance['show_all'] ) )        ? abs(intval($new_instance['show_all'],10)):0;
        $instance['display']            = ( ! empty( $new_instance['display'] ) )       ? (array_key_exists($new_instance['display'], $this->values['display']['values'])? $new_instance['display'] : $this->values['display']['default']) :$this->values['display']['default'];
        return $instance;
    }

} // class Foo_Widget