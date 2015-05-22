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