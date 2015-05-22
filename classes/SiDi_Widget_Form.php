<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 13/08/2014
 * Time: 12:44
 */
require_once ('SiDi_General_Form.php');

class SiDi_Widget_Form extends WP_Widget {

//    private $WP_Widget;
    private $Gen_Form;
    public $default_class="widefat";

    public function SiDi_Widget_Form($id_base, $name, $widget_options = array(), $control_options = array() ){
        $this->Gen_Form = new SiDi_General_From();
        parent::WP_Widget($id_base, $name, $widget_options, $control_options );
    }
    protected  function init_class($add_class,$class=""){
        return trim($class.' '.$add_class);
    }

    public function text($key, $value=null, $class="", $title ="", $attr=array() ){
        return $this->Gen_Form->text($this->get_field_name( $key ), $this->get_field_id( $key ), $value, $this->init_class($this->default_class,$class), $title, $attr );
    }
    public function select($key, $options, $value=null, $class="", $title ="", $attr=array() ){
        return $this->Gen_Form->select($this->get_field_name( $key ), $this->get_field_id( $key ), $options, $value, $this->init_class($this->default_class, $class), $title, $attr );
    }

    public function checkbox($key, $options=1, $value=null, $class="", $title ="", $attr=array() ){
        return $this->Gen_Form->checkbox($this->get_field_name( $key ), $this->get_field_id( $key ), $options, $value, $this->init_class("checkbox", $class), $title, $attr );
    }

    public function number($key, $value=null, $class="", $title ="", $attr=array() ){
        return $this->Gen_Form->number($this->get_field_name( $key ), $this->get_field_id( $key ), $value, $class, $title, $attr);
    }

    public function get_form($data){
        $return = "";
        foreach($data as $key =>$val){
            $value = empty($val['value'])?null:$val['value'];
            $class = empty($val['class'])?"":$val['class'];
            $title = empty($val['label'])?"":$val['label'];
            $attr  = empty($val['attr'])?array():$val['attr'];
            if(!empty($val['type'])){
                if($val['type']==='text'){
                    $return.= $this->text($key,$value,$class,$title,$attr);
                }elseif($val['type']==='select'){
                    if(!empty($val['values'])){
                        $return.= $this->select($key,$val['values'], $value, $class, $title, $attr );
                    }
                }elseif($val['type']==='checkbox'){
                    $option=empty($val['values'])?1:$val['values'];
                    $return.= $this->checkbox($key,$option, $value, $class, $title, $attr );
                }elseif($val['type']==='number'){
                    $return.= $this->number($key,$value,$class,$title,$attr);
                }
            }
        }
        return $return;
    }
} 