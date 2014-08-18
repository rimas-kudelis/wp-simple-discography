<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 13/08/2014
 * Time: 11:03
 */

class SiDi_General_From {

    public function SiDi_General_From(){
        wp_enqueue_script( 'number_script',  plugins_url( 'includes/js/input_number.js', dirname(__FILE__) ), array( 'jquery') );

    }
    public function label($title = "", $for= ""){
        return empty($title)?"":'<label'.(empty($for)?"":' for="'.$for.'"').'>'.$title.'</label>';

    }
    protected function init_block($name, &$id="", &$attr ){
        if(empty($id))
            $id = $name;
        $attr=empty($attr)?'':$this->arrayToStringAttr($attr);
    }
    protected function arrayToStringAttr($attr, $esc=true){

        foreach($attr as $key=>&$val)
            $val=$key.'="'.($esc?esc_attr($val):$val).'"';
        unset($val);
        return ' '.implode(' ', $attr);
    }
    public function text($name ,$id , $value=null, $class="", $title ="", $attr=array() ){
        $this->init_block($name, $id, $attr);
        $return = '<p>';
        $return.= $this->label($title,$id);
        $return.= '<input'.(empty($class)?"":' class="'.$class.'"').' id="'.$id.'" name="'.$name.'" type="text" value="'.(isset($value)?$value:"").'"'.$attr.'>';
        $return.= '</p>';
        return $return;
/*        ?>
<p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label>
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
</p>
<?php*/
    }
    public function select($name ,$id, $options, $value=null, $class="", $title ="", $attr=array() ){

        $this->init_block($name, $id, $attr);

        $return = '<p>';
        $return.= $this->label($title,$id);
        $return.= '<select'.(empty($class)?"":' class="'.$class.'"').' id="'.$id.'" name="'.$name.'" '.$attr.'>';
        foreach( $options as $key => $label )
            $return.= '<option value="'. $key .'" '. selected( $key, $value, false ) .'>'. $label .'</option>';
        $return.= '</select>';
        $return.= '</p>';
        return $return;
    }

    public function checkbox($name ,$id, $options=1, $value=null, $class="", $title ="", $attr=array() ){

        $this->init_block($name, $id, $attr);
        $return = '<p>';
        $return.= '<input'.(empty($class)?"":' class="'.$class.'"').' id="'.$id.'" name="'.$name.'" type="checkbox" value="'.$options.'" '. checked( $options, $value, false ) .'/>';
        $return.= $this->label($title,$id);
        $return.= '</p>';

        return $return;

//        $return ='<p'. $style .'><input '. $class .'id="'. $this->get_field_id( $fieldID ).'" type="checkbox" name="'. $this->get_field_name( $fieldID ) .'" value="1" '. checked( 1, $instance[$fieldID], false ) .'/> <label for="'. $this->get_field_id( $fieldID ) .'">'. $args['label'] .'</label></p>';
//        p>
//<label for="widget-em_calendar-3-long_events">Événements longue durée ?: </label>
//<input id="widget-em_calendar-3-long_events" type="checkbox" value="1" name="widget-em_calendar[3][long_events]">
//</p>
    }

    public function number($name ,$id, $value=null, $class="", $title ="", $attr=array() ){
        $this->init_block($name, $id, $attr);
        $return = '<p>';
        $return.= $this->label($title,$id);
        $return.= '<input'.(empty($class)?"":' class="'.$class.'"').' id="'.$id.'" name="'.$name.'" type="number" value="'.(empty($value)?(empty($attr['min'])?0:$attr['min']):$value).'"'.$attr.'>';
        $return.= '</p>';
        return $return;
    }

} 