<?php

class SiDi_Generate_Form {
    private $id;

    public function SiDi_Generate_Form($id){
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

    public function getDate($key, $title='', $placeholder='YYYY[-mm[-dd]]', $attr=""){
        $html = "";
        if (!empty($title))
            $html .= $this->getLabel($title);
        $date = $this->getValue($key);
        if(!empty($key)){
            $html .= "<p>\n\t";
            $html .= '<input type="text" value="' . esc_attr($date) . '" id="' . $key . '" name="' . $key . '" pattern="[0-9]{4}(-(0[1-9]|1[012])(-(0[1-9]|1[0-9]|2[0-9]|3[01]))?)?" placeholder="' . $placeholder . '" ' . $attr . '>';
            $html .= "</p>\n";
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

    public function getDiscs($id, $title='Disc #', $addDisc='Add New Disc', $delDisc='Delete this Disc', $addTrack='Add New Track', $delTrack='Delete this Track') {
        $html='<div id="'.$id.'" class="sidi-album"><a href="javascript:void(0);" class="sidi-add-disc">'.esc_html($addDisc)."</a>\n";

        $Discs=$this->getValue($id);
        $max_disc=0;
        if(!empty($Discs))
            foreach ($Discs as $key=>$tracks){
                $key=intval($key,10);
                $idDisc=$id.'-'.$key;
                $html.='<div id="'.$idDisc.'" class="sidi-discs" data-id="'.$idDisc.'">'."\n";
                $html.='<p><label><b>'.esc_html($title).'<span>'.$key."</span></b></label></p>\n";
                $html.='<a href="javascript:void(0);" class="sidi-del-disc">'.esc_html($delDisc)."</a>\n";
                $html.='<input type="hidden" value="'.$key.'" name="'.$idDisc.'-0-disc" id="'.$idDisc.'-0-disc" class="sidi-disc-number">';"\n";
                $html.=$this->getTracks($idDisc,$key ,$tracks,'', $addTrack, $delTrack);
                $html.="</div>\n";
                if($max_disc<$key)
                    $max_disc=$key;
            }
        $idDisc=$id.'-0';
        $html.='<div id="sidi-new-disc" class="hidden" data-new="'.$max_disc.'" data-current="'.$max_disc.'">';
        $html.='<div id="'.$idDisc.'" class="sidi-discs">'."\n";
        $html.='<p><label><b>'.esc_html($title)."<span></span></b></label></p>\n";
        $html.='<a href="javascript:void(0);" class="sidi-del-disc">'.esc_html($delDisc)."</a>\n";
        $html.='<input type="hidden" value="" name="'.$idDisc.'-disc" id="'.$idDisc.'-disc" class="sidi-disc-number">';"\n";
        $html.=$this->getTracks($idDisc,0,null,'', $addTrack, $delTrack);
        $html.="</div>\n";
        $html.="</div>\n";
        $html.="</div>\n";

        return $html;
    }

    public function getTracks($id,$num_disc=0,$tracks=array(), $title='', $addTrack='Add New Track', $delTrack='Delete this Track'){
        $html="";
        if (!empty($title))
            $html.=$this->getLabel($title);
        if(!empty($id)){
            $html.='<ul class="sidi-tracks sortable">'."\n\t";
            $max_track=0;
            if(empty($tracks))
                $tracks=json_decode($this->getValue($id));
            if(!empty($tracks)){
                if(isset($tracks['title']))
                    $tracks[]=$tracks;
                foreach($tracks as $key => $track){
                    if(trim(implode("",$track)) && !empty($track['title']) && !empty($track['track'])){
                        $html.="\t\t".$this->getTrack($id,$num_disc,$track, $delTrack )."\n";
                        if($max_track<$track['track'])
                            $max_track=$track['track'];
                    }
                }
            }
            $html.="</ul>\n";
            $html.='<a href="javascript:void(0);" class="sidi-add-track">'.esc_html($addTrack)."</a>\n";
            $html.='<ul class="hidden sidi-new-track" data-new="'.$max_track.'" data-current="'.$max_track.'">';
            $html.="\t\t".$this->getTrack($id, $num_disc,null, $delTrack )."\n";
            $html.="</ul>\n";
        }

        return $html;

    }
    public function getTrack($id, $num_disc=0,$track=array(),$delTrack='' ){
        $track['track']=isset($track['track'])?intval($track['track'],10):0;
        $tabindex=$num_disc.str_pad(($track['track']-1)*2+1,2 ,'0', STR_PAD_LEFT);
        $idTrack=$id.'-'.$track['track'];
        $html='<li id="'.$idTrack.'" class="sidi-track">' ;
        $html.='    <input type="hidden" value="'.$track['track'].'" id="'.$idTrack.'-track" name="'.$idTrack.'-track" class="sidi-track-track" readonly="readonly">';
        $html.='    <span id="'.$idTrack.'-num" class="sidi-track-num">'.$track['track'].'</span>';
        $html.='    <a href="javascript:void(0);" class="sidi-del-track">'.esc_html($delTrack)."</a>\n";
        $track['time']=isset($track['time'])?esc_attr($track['time']):'';
        $html.='    <input type="text" value="'.$track['time'].'" id="'.$idTrack.'-time" name="'.$idTrack.'-time" class="sidi-track-time" tabindex="'.($tabindex+1).'" pattern="[0-9]{1,3}:[0-5][0-9]" placeholder="mmm:ss">';
        $track['title']=isset($track['title'])?esc_attr($track['title']):'';
        $html.='    <div class="sidi-track-cente"r><input type="text" value="'.$track['title'].'" id="'.$idTrack.'-title" name="'.$idTrack.'-title" class="sidi-track-title" tabindex="'.$tabindex.'"></div>';
        $html.='</li>';
        return $html;
    }
}
