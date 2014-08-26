<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 01/08/2014
 * Time: 15:02
 */
//if (is_singular( PORT_TYPE )){
//    wp_enqueue_style( 'front-style', plugins_url( 'css/front.css' , dirname(__FILE__) ), true);
//}
add_action( 'init', 'register_shortcodes');
function register_shortcodes(){
    add_shortcode('sidi-discography', 'sidi_discography_function');
}
//function sidi_discography_function() {
function sidi_discography_function($atts=array()) {
    wp_enqueue_style( 'front-style', plugins_url( 'includes/css/front.css' , __FILE__ ), true);
    wp_enqueue_script( 'jquery-scrollTo', plugins_url( 'includes/js/jquery.scrollTo.min.js', __FILE__ ), array( 'jquery' ) );
    wp_enqueue_script( 'sidi-front', plugins_url( 'includes/js/sidi-front.js', __FILE__ ), array( 'jquery' ) );
    $atts= shortcode_atts( array(
        'posts_per_page' => -1,
        'date_format'   => "Y",
        'order_by'      => 'release',
        'order'         => 'DESC',
        'dynamic'       => 1,
        'show_song'     => 1,
        'show_title'     => 1,
        'display'       => 'list',
        'cover_height'  => 150,
        'cover_width'   => 150,
        'id'            =>''
    ), $atts, 'sidi-discography' );
//var_dump($atts['dynamic'], $atts['show_song']);
    if ( $atts['show_song'] === 'false' ) $atts['show_song'] = false; // just to be sure...
    $show_song = (bool) $atts['show_song'];
    if ( $atts['dynamic'] === 'false' ) $atts['dynamic'] = false; // just to be sure...
    $dynamic = (bool) $atts['dynamic'];
    if ( $atts['show_title'] === 'false' ) $atts['show_title'] = false; // just to be sure...
    $show_title = (bool) $atts['show_title'];
    if ( $atts['order'] != 'ASC' ) $atts['order'] = 'DESC';
    if ( $atts['display'] != 'thumbnail' ) $atts['display'] = 'list';
    if($atts['display'] == 'thumbnail' ) $dynamic=true;

    $cover_height=intval($atts['cover_height'], 10);
    $cover_width=intval($atts['cover_width'], 10);
    $style_cover_img=$style_cover_link=$style_cover=$style_content='';
    if($cover_height!==150 || $cover_width!==150){
        $style_cover=' style="';
        if($cover_height!==150){
            $style_cover.='height: '.$cover_height.'px; ';
            $style_cover_img.='max-height: '.$cover_height.'px; ';
            $style_cover_link=' style="height: '.($cover_height+3).'px;"';
            if($atts['display']=='thumbnail'){
                $complement=21;
                if($show_title)
                    $complement+=20;
                $style_content = ' style="margin-top: '.($cover_height+$complement).'px;"';
            }
        }
        if($cover_width!==150){
            $style_cover.='width: '.$cover_width.'px; ';
            $style_cover_img.='max-width: '.$cover_width.'px; ';
            $style_content = ' style="margin-left: '.($cover_height+20).'px;"';
        }
        $style_cover=rtrim($style_cover).'"';
        $style_cover_img=trim($style_cover_img).'"';
    }
    $show_id=-1;
    $atts['id']=esc_attr($atts['id']);
    $sidi_id=(empty($atts['id'])?'':' id="sidi-'.$atts['id'].'"');
    if($show_song && $dynamic){

        $select_id=(empty($atts['id'])?'':$atts['id'].'_').'alb';
        $show_id=(empty($_GET[$select_id])?-1:intval($_GET[$select_id], 10));
        $current_url=esc_url_raw(( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $current_url=explode('?',$current_url);
        if(count($current_url)==1){
            $current_url=$current_url[0].'?'.$select_id.'=';
        }else{
            $get_param=explode('&',$current_url[1]);
            foreach($get_param as $key=>$value){
                $value=explode('=',$value);
                if($value[0]==$select_id)
                    unset($get_param[$key]);
            }
            unset($value);
            if(count($get_param)==0){
                $current_url=$current_url[0].'?'.$select_id.'=';
            }else{
                $current_url[1]=implode('&',$get_param);
                $current_url=implode('?',$current_url).'&'.$select_id.'=';
            }
        }
    }
    $order_by=array(
        'rand'      => array('orderby'   => 'rand'),
        'title'     => array('orderby'   => 'post_title'),
        'date'      => array('orderby'   => 'post_date'),
        'modified'  => array('orderby'   => 'post_modified'),
        'release'   => array(
            'orderby'   => 'meta_value',
            'meta_key'  => RELEASE
        )
    );
    $query= array( 'post_type' => PORT_TYPE, 'posts_per_page' => $atts['posts_per_page'] );//,
//        'meta_key' => 'post_title',
//        'orderby' => 'meta_value',
//        'order' => 'DESC' ) ;
    $atts['order_by']=explode(' ',$atts['order_by']);
    $order=false;
    $meta_value=false;
    foreach($atts['order_by'] as $key => $value){
        if(isset($order_by[$value])){
            if ($value!='rand')
                $order=true;
            if($order_by[$value]['orderby']=='meta_value'){
                if(!$meta_value){
                    $query['orderby'][]='meta_value';
                    $meta_value=true;
                }
                $query['meta_key'][]=$order_by[$value]['meta_key'];

            }else{
                $query['orderby'][]=$order_by[$value]['orderby'];
            }
        }
    }
    if($order && ($atts['order']==='ASC' || $atts['order']==='DESC'))
        $query['order']= $atts['order'];
    if(isset($query['meta_key']))
        $query['meta_key']=implode(' ',$query['meta_key']);
    if(isset($query['orderby']))
        $query['orderby']=implode(' ', $query['orderby']);

    $query = new WP_Query($query);
    $return_string='<div'.$sidi_id.' class="sidi"><ul class="sidi-'.$atts['display'].'">';
//    var_dump($dynamic&&!empty($discs), $dynamic, $show_song);
    $cover_size=array($cover_width,$cover_height);
    while ($query->have_posts()) {
        $query->the_post();
        $return_string .='<li id="sidi-'.$query->post->ID.'" class="sidi-album list-style-none'.($show_id==$query->post->ID || !$dynamic?' discs-show':' discs-hidden').' clearfix">';

        $cover=get_post_meta( $query->post->ID, COVER, true );
        $discs=($show_song?get_post_meta( $query->post->ID, DISCS, true ):null);
        if($atts['display']=='list'){
            if($dynamic&&!empty($discs))
                $return_string .='<div class="sidi-cover"'.$style_cover.'><a title="'.esc_attr(get_the_title()).'" class="sidi-cover-link" href="'.$current_url.$query->post->ID.'" '.$style_cover_link.'>'.sidi_get_cover_image(empty($cover['id'])?0:$cover['id'], $cover_size, array('style'=>$style_cover_img, 'alt'=>__('Cover : ','sidi').get_the_title())).'</a></div>';
            else{
                if($show_song)
                    $return_string .='<div class="sidi-cover"'.$style_cover.'>'.sidi_get_cover_image(empty($cover['id'])?0:$cover['id'], $cover_size, array('style'=>$style_cover_img, 'alt'=>__('Cover : ','sidi').get_the_title())).'</div>';
                else
                    $return_string .='<div class="sidi-cover"'.$style_cover.'><a title="'.esc_attr(get_the_title()).'" href="'.get_permalink().'" '.$style_cover_link.'>'.sidi_get_cover_image(empty($cover['id'])?0:$cover['id'], $cover_size, array('style'=>$style_cover_img, 'alt'=>__('Cover : ','sidi').get_the_title())).'</a></div>';
            }
        }else{

            if($atts['display']=='thumbnail'){
                if($show_song)
                    $return_string .='<div class="sidi-cover"'.$style_cover.'><a title="'.esc_attr(get_the_title()).'" class="sidi-cover-link" href="'.$current_url.$query->post->ID.'" '.$style_cover_link.'>'.sidi_get_cover_image(empty($cover['id'])?0:$cover['id'], $cover_size, array('style'=>$style_cover_img, 'alt'=>__('Cover : ','sidi').get_the_title())).'</a>';
                else
                    $return_string .='<div class="sidi-cover"'.$style_cover.'><a title="'.esc_attr(get_the_title()).'" href="'.get_permalink().'" '.$style_cover_link.'>'.sidi_get_cover_image(empty($cover['id'])?0:$cover['id'], $cover_size, array('style'=>$style_cover_img, 'alt'=>__('Cover : ','sidi').get_the_title())).'</a>';

                    $return_string .='<div class="sidi-thumbnail-title">';
                if($show_title)
                    $return_string .='<div class="sidi-thumbnail-title-link"><a href="'.get_permalink().'">'.get_the_title().'</a></div>';
                $return_string .='<div class="sidi-thumbnail-arrow">';
                $return_string .='  <span class="arrow"></span>';
                $return_string .='</div>';
                $return_string .='</div>';
                $return_string .='</div>';
            }
        }


        $return_string .='<div class="sidi-content"'.$style_content.'>';
        $return_string .='<div class="sidi-header clearfix">';
        if($show_title)
            $return_string .= '<H2 class="sidi-album-title"><a href="'.get_permalink().'">'.get_the_title().'</a></H2>';
        $release=get_post_meta( $query->post->ID, RELEASE, true );
        if(!empty($release)){
            $return_string .= '<span class="sidi-release">'.esc_html(SiDi_I18N_DateTime::date($atts['date_format'],$release)).'</span>';
        }
        $return_string .='</div>';
        $return_string .='<div class="sidi-excerpt">'. get_the_excerpt().'</div>';
//        $return_string .='<div class="sidi-excerpt">'.preg_replace('/(\[.*?\])/i', '<a href="'.get_permalink().'">$1</a>', get_the_excerpt()).'</div>';
//        $discs=get_post_meta( $query->post->ID, DISCS, true );
        if(!empty($discs) && $show_song){
            $multi_discs=(count($discs)>1)?true:false;
            $return_string .='<div class="sidi-discs">';
            if($atts['display']!='thumbnail')
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
        $return_string .='</div>';
        $return_string .='</li>';
    }
    $return_string.="</ul></div>";
    wp_reset_postdata();
    return $return_string;

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
//            $return_string .= '<H2 class="sidi-album-title"><a href="'.get_permalink().'">'.get_the_title().'</a></H2>';
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