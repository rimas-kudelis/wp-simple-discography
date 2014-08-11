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
        'display'       => 'list'
    ), $atts, 'sidi-discography' );

    if ( $atts['show_song'] === 'false' ) $atts['show_song'] = false; // just to be sure...
    $show_song = (bool) $atts['show_song'];
    if ( $atts['dynamic'] === 'false' ) $atts['dynamic'] = false; // just to be sure...
    $dynamic = (bool) $atts['dynamic'];
    if ( $atts['display'] != 'thumbnail' ) $atts['display'] = 'list';
    if($atts['display'] == 'thumbnail' ) $dynamic=true;
    $show_id=-1;
    if($show_song && $dynamic){
        $show_id=(empty($_GET['alb'])?-1:intval($_GET['alb'], 10));
        $current_url=esc_url_raw(( is_ssl() ? 'https://' : 'http://' ) . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
        $current_url=explode('?',$current_url);
        if(count($current_url)==1){
            $current_url=$current_url[0].'?alb=';
        }else{
            $get_param=explode('&',$current_url[1]);
            foreach($get_param as $key=>$value){
                $value=explode('=',$value);
                if($value[0]=='alb')
                    unset($get_param[$key]);
            }
            unset($value);
            if(count($get_param)==0){
                $current_url=$current_url[0].'?alb=';
            }else{
                $current_url[1]=implode('&',$get_param);
                $current_url=implode('?',$current_url).'&alb=';
            }
        }
    }
    $order_by=array(
        'rand'      =>['orderby'   => 'rand'],
        'title'     =>['orderby'   => 'post_title'],
        'date'      =>['orderby'   => 'post_date'],
        'modified'  =>['orderby'   => 'post_modified'],
        'release'   => array(
            'orderby'   => 'meta_value',
            'meta_key'  => RELEASE)
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
    $return_string='<div class="sidi"><ul class="sidi-'.$atts['display'].'">';
    while ($query->have_posts()) {
        $query->the_post();
        $return_string .='<li id="sidi-'.$query->post->ID.'" class="sidi-album '.($show_id==$query->post->ID || !$dynamic?'discs-show':'discs-hidden').' clearfix">';

        $cover=get_post_meta( $query->post->ID, COVER, true );
        $discs=($show_song?get_post_meta( $query->post->ID, DISCS, true ):null);
        if($atts['display']=='list'){
            if($dynamic&&!empty($discs))
                $return_string .='<div class="sidi-cover"><a class="sidi-cover-link" href="'.$current_url.$query->post->ID.'"><img src="'.(empty($cover['url'])?plugins_url( 'includes/images/no-cover.png' , __FILE__ ):$cover['url']).'" ></a></div>';
            else
                $return_string .='<div class="sidi-cover"><img src="'.(empty($cover['url'])?plugins_url( 'includes/images/no-cover.png' , __FILE__ ):$cover['url']).'" ></div>';
        }else{

            if($atts['display']=='thumbnail'){
                if($show_song)
                    $return_string .='<div class="sidi-cover"><a class="sidi-cover-link" href="'.$current_url.$query->post->ID.'"><img src="'.(empty($cover['url'])?plugins_url( 'includes/images/no-cover.png' , __FILE__ ):$cover['url']).'" ></a>';
                else
                    $return_string .='<div class="sidi-cover"><a href="'.get_permalink().'"><img src="'.(empty($cover['url'])?plugins_url( 'includes/images/no-cover.png' , __FILE__ ):$cover['url']).'" ></a>';
                $return_string .='<div class="sidi-thumbnail-title"><div class="sidi-thumbnail-title-link"><a href="'.get_permalink().'">'.get_the_title().'</a></div>';
                $return_string .='<div class="sidi-thumbnail-arrow">';
                $return_string .='  <span class="arrow"></span>';
                $return_string .='</div>';
                $return_string .='</div>';
                $return_string .='</div>';
            }
        }


        $return_string .='<div class="sidi-content">';
        $return_string .='<div class="sidi-header clearfix">';
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
                    $return_string .='<li class="sidi-track"><span class="sidi-num-track">'.$track['track'].'</span>'.(empty($track['time'])?'':'<span class="sidi-time-track">'.$track['time'].'</span>').'<span class="sidi-title-track'.(empty($track['time'])?' sidi-notime-track':'').'">'.$track['title'].'</span></li>';
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

            $return_string .='<div class="sidi-cover"><img src="'.(empty($cover['url'])?plugins_url( 'includes/images/no-cover.png' , __FILE__ ):$cover['url']).'" ></div>';
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
                        $return_string .='<li class="sidi-track"><span class="sidi-num-track">'.$track['track'].'</span>'.(empty($track['time'])?'':'<span class="sidi-time-track">'.$track['time'].'</span>').'<span class="sidi-title-track'.(empty($track['time'])?' sidi-notime-track':'').'">'.$track['title'].'</span></li>';
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