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
        if (is_singular( POST_TYPE )) {
            wp_enqueue_style( 'front-style', plugins_url( 'includes/css/front.css' , __FILE__ ), true);
            $id= get_the_ID();
            $return_string ='<div class="sidi"><div id="sidi-'.$id.'" class="sidi-single sidi-list">';

            $cover=get_post_meta( $id, COVER, true );
            $return_string .='<div class="sidi-content clearfix">';
            $return_string .='<div class="sidi-header clearfix">';
            $catalog = get_post_meta( $id, CATALOG, true);
            $release = get_post_meta( $id, RELEASE, true );
            if(!empty($catalog) || !empty($release)){
                $return_string .= '<span class="sidi-release">';
                if (!empty($catalog)) {
                    $return_string .= esc_html($catalog);

                    if (!empty($release)) {
                        $return_string .= ', ';
                    }
                }
                if (!empty($release)) {
                    $return_string .= esc_html(SiDi_Helper::format_release_date($release, 'j M Y', 'M Y', 'Y'));
                }
                $return_string .= '</span>';
            }
            $return_string .='</div>';

            $cover_size = array(150,150);
            $return_string .='<div class="sidi-cover">'.SiDi_Helper::get_cover_image(empty($cover['id'])?0:$cover['id'], $cover_size, array('alt'=>__('Cover : ','sidi').get_the_title())).'</div>';
            $return_string .='<div class="sidi-descip">'.$content.'</div>';
            $return_string .='</div>';
            $discs=get_post_meta( $id, DISCS, true );
            if(!empty($discs)){
                $multi_discs=(count($discs)>1)?true:false;
                $return_string .='<div class="sidi-discs">';
                foreach($discs as $disc => $tracks){
                    $return_string .='<div class="sidi-disc">';
                    if($multi_discs)
                        $return_string .='<H3 class="sidi-num-disc">'.__('Disc #','sidi').$disc.'</H3>';
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