<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 25/07/2014
 * Time: 12:59
 */
Class SiDi_Generat_Form {
    private $id;

    public function SiDi_Generat_Form($id){
        $this->id=$id;
    }

    public function getValue($key){
        $value = get_post_meta($this->id, $key, $single=true);
        if(empty($value))
            return "";
        return $value;
    }

    public function getLabel($label='', $for=''){
        return '<p><label'.(empty($for)?'':'for="'.$for.'"').'><b>'.esc_html($label)."</b></label></p>\n";
    }

    //inpute text : <input type="text" aria-required="true" size="40" value="" id="$key" name="$key">
    public function getText($key, $title='', $attr=""){
        $html="";
        if (!empty($title))
            $html.=$this->getLabel($title);
        $html.='<p><input type="text" value="'.esc_attr($this->getValue($key)).'" id="'.$key.'" name="'.$key.'" '.$attr.'></p>'."\n";
        return $html;
    }

    //textarea :<textarea name="meta-textarea" id="meta-textarea">value</textarea>
    public function getTextarea($key, $title='', $attr=""){
        $html="";
        if (!empty($title))
            $html.=$this->getLabel($title);
        $html.='<p><textarea id="'.$key.'" name="'.$key.'" '.$attr.'>'.esc_html($this->getValue($key)).'</p>';
        return $html;
    }

    public function getCheckboxs($keysValuesLabels, $title='',$attr=""){
        $html="";
        if (!empty($title))
            $html.=$this->getLabel($title);
        if(!empty($keysValuesLabels)){
            if(!empty($keysValuesLabels['key']))
                $keysValuesLabels[]=$keysValuesLabels;
            $html.="<p>\n";
            $br=false;
            foreach($keysValuesLabels as $key => $checkbox){
                if(!empty($checkbox['key'])){
                    $html.="\t".($br?'<br>':'').$this->getCheckbox($checkbox['key'], empty($checkbox['value'])?1:$checkbox['value'],empty($checkbox['label'])?"":$checkbox['label'], $attr)."\n";
                    $br=true;
                }
            }
            $html.="</p>\n";
        }
        return $html;
    }

    public function getCheckbox($key,$value=1,$label='',$attr="")
    {
        $html='<label for="'.$key.'"><input type="checkbox" value="'.esc_attr($value).'" id="'.$key.'" name="'.$key.'" '.checked($this->getValue($key),$value,false).' '.$attr.'>'.esc_html($label).'</label>';
        return $html;
    }

    public function getRadios($keysValuesLabels, $name, $title='', $attr=""){
        $html="";
        if (!empty($title))
            $html.=$this->getLabel($title);
        if(!empty($keysValuesLabels) && !empty($name)){
            if(!empty($keysValuesLabels['key']))
                $keysValuesLabels[]=$keysValuesLabels;
            $html.="<p>\n";
            $br=false;
            foreach($keysValuesLabels as $key => $checkbox){
                if(!empty($checkbox['key']))
                    $html.="\t".($br?'<br>':'').$this->getRadio($checkbox['key'], $name, empty($checkbox['value'])?"":$checkbox['value'],empty($checkbox['label'])?"":$checkbox['label'], $attr)."\n";
                $br=true;
            }
            $html.="</p>\n";
        }
        return $html;
    }
    public function getRadio($key, $name,$value=1,$label='',$attr=""){
        $html='<label for="'.$key.'"><input type="checkbox" value="'.esc_attr($value).'" id="'.$key.'" name="'.esc_attr($name).'" '.checked($this->getValue($key),$value,false).' '.$attr.'>'.esc_html($label).'</label>';
        return $html;
    }
    public function getSelect($key,$ValuesLabels, $title='', $attr=""){
        $html="";
        if (!empty($title))
            $html.=$this->getLabel($title);
        if(!empty($ValuesLabels) && !empty($key)){
            $html.="<p>\n\t";
            $html.='<select name="'.$key.'" id="'.$key.'" '.$attr.'>'."\n";
            if(isset($ValuesLabels['value']))
                $ValuesLabels[]=$ValuesLabels;
            $value=$this->getValue($key);
            foreach($ValuesLabels as $key => $option){
                $html.="\t\t".'<option value="'.esc_attr($option['value']).'" '.selected( $value, $option['value'] ) .'>'.esc_html($option['label']).'</option>'."\n";
            }
            $html.="\t</select>\n";
            $html.="</p>\n";
        }
        return $html;
    }

    public function getDate($key, $title='', $attr=""){
        $html="";
        if (!empty($title))
            $html.=$this->getLabel($title);
        $date=$this->getValue($key);
        if (!empty($date))
            $date=SiDi_I18N_DateTime::date(get_option( 'date_format' ), $date);
        if(!empty($key)){
            $html.="<p>\n\t";
            $html.='<input type="text" value="'.esc_attr($date).'" id="'.$key.'" name="'.$key.'" class="datepicker" '.$attr.'>';
            $html.="</p>\n";
        }
        return $html;
    }

    // media upload : <a class="thickbox" id="set-post-thumbnail" href="http://ten.batteur.be/wp-admin/media-upload.php?post_id=449&amp;type=image&amp;TB_iframe=1&amp;width=640&amp;height=726" title="Mettre une image à la Une">Mettre une image à la Une</a>
    public function getImageUploader($key,$title="",$button='Choose or Upload an Image',$reset='Delete Image',$attributes="") {
        $html="";
        if (!empty($title))
            $html.=$this->getLabel($title);
        if(!empty($key)){
            $html.="<p>\n\t";
            $html.='<a id="'.$key.'-button" href="javascript:void(0);" title="'.esc_attr($button).'">'."\n";
            $value=$this->getValue($key);
            $hidden=true;
            if($value){
                $hidden=false;
//                $html.="\t\t". '<img src="'.$value['url'].'" alt="'.$value['image_alt'].'" />'."\n";
                $html.="\t\t".  wp_get_attachment_image($value['id'],array(150,150),false,array('alt'=>$value['image_alt']))."\n";
            }
            $html.="\t\t<span".(!$hidden?' style="display: none;"':'').'>'.esc_html($button)."</span>\n";
            $html.="\t</a><br>\n";
            $html.='<a id="'.$key.'-reset" href="javascript:void(0);" title="'.esc_attr($reset).'"'.($hidden?' style="display: none;"':'').'">'.esc_html($reset)."</a>\n";
            $html.='<input type="hidden" name="'.$key.'" id="'.$key.'" value="' .esc_attr(json_encode($this->getValue($key))) . '" />';
            $html.="</p>\n";
        }

        return $html;

    } // end wp_custom_attachment

    public function getDiscs($id, $title='Disc n° :', $addDisk='Add New Disk', $delDisk='Delete this Disk', $addTrack='Add New Track', $delTrack='Delete this Track'){
        $html='<div id="'.$id.'" class="sidi-album"><a href="javascript:void(0);" class="sidi-add-disk">'.esc_html($addDisk)."</a>\n";

        $Discs=$this->getValue($id);
        $max_disk=0;
        if(!empty($Discs))
            foreach ($Discs as $key=>$tracks){
                $key=intval($key,10);
                $idDisk=$id.'-'.$key;
                $html.='<div id="'.$idDisk.'" class="sidi-discs" data-id="'.$idDisk.'">'."\n";
                $html.='<p><label><b>'.esc_html($title).'<span>'.$key."</span></b></label></p>\n";
                $html.='<a href="javascript:void(0);" class="sidi-del-disk">'.esc_html($delDisk)."</a>\n";
                $html.='<input type="hidden" value="'.$key.'" name="'.$idDisk.'-0-disk" id="'.$idDisk.'-0-disk" class="sidi-disk-number">';"\n";
                $html.=$this->getTracks($idDisk,$key ,$tracks,'', $addTrack, $delTrack);
                $html.="</div>\n";
                if($max_disk<$key)
                    $max_disk=$key;
            }
        $idDisk=$id.'-0';
        $html.='<div id="sidi-new-disk" class="hidden" data-new="'.$max_disk.'" data-current="'.$max_disk.'">';
        $html.='<div id="'.$idDisk.'" class="sidi-discs">'."\n";
        $html.='<p><label><b>'.esc_html($title)."<span></span></b></label></p>\n";
        $html.='<a href="javascript:void(0);" class="sidi-del-disk">'.esc_html($delDisk)."</a>\n";
        $html.='<input type="hidden" value="" name="'.$idDisk.'-disk" id="'.$idDisk.'-disk" class="sidi-disk-number">';"\n";
        $html.=$this->getTracks($idDisk,0,null,'', $addTrack, $delTrack);
        $html.="</div>\n";
        $html.="</div>\n";
        $html.="</div>\n";

        return $html;
    }

    public function getTracks($id,$num_disk=0,$tracks=array(), $title='', $addTrack='Add New Track', $delTrack='Delete this Track'){
        $html="";
        if (!empty($title))
            $html.=$this->getLabel($title);
        if(!empty($id)){
            $html.='<ul class="sidi-traks sortable">'."\n\t";
            $max_track=0;
            if(empty($tracks))
                $tracks=json_decode($this->getValue($id));
            if(!empty($tracks)){
                if(isset($tracks['title']))
                    $tracks[]=$tracks;
                foreach($tracks as $key => $track){
                    if(trim(implode("",$track)) && !empty($track['title']) && !empty($track['track'])){
                        $html.="\t\t".$this->getTrack($id,$num_disk,$track, $delTrack )."\n";
                        if($max_track<$track['track'])
                            $max_track=$track['track'];
                    }
                }
            }
            $html.="</ul>\n";
            $html.='<a href="javascript:void(0);" class="sidi-add-track">'.esc_html($addTrack)."</a>\n";
            $html.='<ul class="hidden sidi-new-track" data-new="'.$max_track.'" data-current="'.$max_track.'">';
            $html.="\t\t".$this->getTrack($id, $num_disk,null, $delTrack )."\n";
            $html.="</ul>\n";
        }

        return $html;

    }
    public function getTrack($id, $num_disk=0,$track=array(),$delTrack='' ){
        $track['track']=isset($track['track'])?intval($track['track'],10):0;
        $tabindex=$num_disk.str_pad(($track['track']-1)*2+1,2 ,'0', STR_PAD_LEFT);
        $idTrack=$id.'-'.$track['track'];
        $html='<li id="'.$idTrack.'" class="sidi-track">' ;
        $html.='    <input type="hidden" value="'.$track['track'].'" id="'.$idTrack.'-track" name="'.$idTrack.'-track" class="sidi-track-track" readonly="readonly">';
        $html.='    <span id="'.$idTrack.'-num" class="sidi-track-num">'.$track['track'].'</span>';
        $html.='    <a href="javascript:void(0);" class="sidi-del-track">'.esc_html($delTrack)."</a>\n";
        $track['time']=isset($track['time'])?esc_attr($track['time']):'';
        $html.='    <input type="text" value="'.$track['time'].'" id="'.$idTrack.'-time" name="'.$idTrack.'-time" class="sidi-track-time" tabindex="'.($tabindex+1).'">';
        $track['title']=isset($track['title'])?esc_attr($track['title']):'';
        $html.='    <div class="sidi-track-cente"r><input type="text" value="'.$track['title'].'" id="'.$idTrack.'-title" name="'.$idTrack.'-title" class="sidi-track-title" tabindex="'.$tabindex.'"></div>';
        $html.='</li>';
        return $html;
    }
}


?>