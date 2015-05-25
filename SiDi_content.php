<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 21/05/15
 * Time: 10:36
 */
add_filter( 'the_content', 'sidi_the_content_filter', 20 );
function sidi_the_content_filter( $content ) {

    global $post;
    if ( is_single()){
        if (is_singular( PORT_TYPE )) {
            wp_enqueue_style( 'front-style', plugins_url( 'includes/css/front.css' , __FILE__ ), true);
            $id= get_the_ID();
            $return_string ='<div class="sidi"><div id="sidi-'.$id.'" class="sidi-single sidi-list">';

            $cover=get_post_meta( $id, COVER, true );
            $return_string .='<div class="sidi-content clearfix">';
            $return_string .='<div class="sidi-header clearfix">';
            $release=get_post_meta( $id, RELEASE, true );
            if(!empty($release)){
                $return_string .= '<span class="sidi-release">'.esc_html(SiDi_I18N_DateTime::date('Y',$release)).'</span>';
            }
            $return_string .='</div>';

            $cover_size = array(150,150);
            $return_string .='<div class="sidi-cover">'.sidi_get_cover_image(empty($cover['id'])?0:$cover['id'], $cover_size, array('alt'=>__('Cover : ','sidi').get_the_title())).'</div>';
            $return_string .='<div class="sidi-descip">'.$content.'</div>';
            $return_string .='</div>';
            $discs=get_post_meta( $id, DISCS, true );
            if(!empty($discs)){
                $multi_discs=(count($discs)>1)?true:false;
                $return_string .='<div class="sidi-discs">';
                $return_string .='<span class="arrow"></span>';
                foreach($discs as $disk => $tracks){
                    $return_string .='<div class="sidi-disk">';
                    if($multi_discs)
                        $return_string .='<H3 class="sidi-num-disk">'.__('Disk n : ','sidi').$disk.'</H3>';
                    $return_string .='<ul class="sidi-tracks">';
                    foreach($tracks as $key => $track){
                        $return_string .='<li class="sidi-track list-style-none"><span class="sidi-num-track">'.$track['track'].'</span>'.(empty($track['time'])?'':'<span class="sidi-time-track">'.$track['time'].'</span>').'<span class="sidi-title-track'.(empty($track['time'])?' sidi-notime-track':'').'">'.$track['title'].'</span></li>';
                    }
                    $return_string .='</ul></div>';
                }
                $return_string .='</div>';
            }
            $return_string .='</div></div>';
            $content=$return_string;
        }
    }
    return $content;
}


function sidi_get_cover_image($attachement_id, $size=array(150,150), $attr=array()){
    $cover=$attachement_id!=0?wp_get_attachment_image_src($attachement_id, $size):false;
    if($cover==false){
        $cover=array(
            plugins_url( 'includes/images/no-cover.png' , __FILE__ ),
            $size[0],
            $size[1]
        );
    }
    foreach($attr as $key=>&$val)
        $val=$key.'="'.esc_attr($val).'"';
    unset($val);
    $attr= ' '.implode(' ', $attr);
    return '<img width="'.$cover[1].'" height="'.$cover[2].'" src="'.$cover[0].'" class="attachment-'.$cover[1].'x'.$cover[2].'"'.$attr.'/>';
}