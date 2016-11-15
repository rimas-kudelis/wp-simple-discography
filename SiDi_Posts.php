<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 25/07/2014
 * Time: 09:10
 */
require_once('classes/SiDi_Generate_Form.php');

add_action( 'init', 'sidi_save_post' );

function sidi_save_post() {
    global $typenow, $post;
    if( $typenow === POST_TYPE ) {
        set_post_format($post->id, 'standard' );
    }

    add_action('save_post_'.POST_TYPE, 'sidi_save_disc_meta', 10, 2); // save the custom fields
}

// Add the Album Meta Boxes
function add_album_metaboxes($post_id ) {
    add_action( 'admin_enqueue_scripts', 'sidi_enqueue' );
    add_meta_box('sidi_album_info', __('Album Info','sidi'), 'sidi_album_info', POST_TYPE, 'side', 'high');
    add_meta_box('sidi_album_tracks', __('Album Tracks','sidi'), 'sidi_album_tracks', POST_TYPE, 'normal', 'high');
}

// The Album Info Metabox
function sidi_album_info() {
    global $post;

    wp_enqueue_style( 'admin-style', plugins_url( 'includes/css/admin.css' , __FILE__ ), true);

    wp_nonce_field( plugin_basename( __FILE__ ), 'discometa_noncename' );
    $genForm = new SiDi_Generate_Form($post->ID);
    $html="";
    $html.= $genForm->getDate(RELEASE, __( 'Release Date:', 'sidi' ), __('YYYY[-mm[-dd]]', 'sidi'));
//    $html.= $genForm->getText(AMAZON,__( 'Amazon URL:', 'sidi' ));
    $html.= $genForm->getText(CATALOG, __('Catalog number:', 'sidi'));
    $html.=$genForm->getImageUploader(COVER,__('Cover:', 'sidi'),__('Choose or Upload an Cover','sidi'), __('Delete this Cover','sidi'));
    echo $html;
}

/**
 * Loads the image management javascript
 */
function sidi_enqueue() {
    global $typenow, $post, $wp_locale;
    if( $typenow === POST_TYPE ) {
        wp_register_script( 'admin_script',  plugins_url( 'includes/js/sidi_admin.js', __FILE__ ), array( 'jquery' ,'media-upload') );
        wp_localize_script( 'admin_script', 'meta_image',
            array(
                'title' => __('Choose or Upload a Cover','sidi'),
                'button' => __( 'Use this Cover', 'sidi' ),
                'key' => COVER,
            )
        );
        $aryArgs = array(
            'closeText'         => __( 'Close', 'sidi' ),
            'currentText'       => __( 'Today', 'sidi' ),
            // we must replace the text indices for the following arrays with 0-based arrays
            'monthNames'        => array_values( $wp_locale->month ),
            'monthNamesShort'   => array_values( $wp_locale->month_abbrev ),
            'dayNames'      	=> array_values( $wp_locale->weekday ),
            'dayNamesShort'     => array_values( $wp_locale->weekday_abbrev ),
            'dayNamesMin'       => array_values( $wp_locale->weekday_initial ),
            // the date format must be converted from PHP date tokens to js date tokens
            'dateFormat'        => 'yy-mm-dd',
            // First day of the week from WordPress general settings
            'firstDay'			=> get_option( 'start_of_week' ),
            // is Right to left language? default is false
            'isRTL'				=> (empty($wp_locale->is_rtl)?false:$wp_locale->is_rtl),
        );
//        var_dump($wp_locale);
        wp_localize_script( 'admin_script', 'objectL10n', $aryArgs );
        wp_enqueue_script( 'admin_script' );
    }
}

// The Album tracks Metabox
function sidi_album_tracks() {
    global $post;
    $genForm = new SiDi_Generate_Form($post->ID);
    $html = $genForm->getDiscs(
        DISCS,
        __('Disc #', 'sidi'),
        __('Add New Disc', 'sidi'),
        __('Delete this Disc', 'sidi'),
        __('Add New Track', 'sidi'),
        __('Delete this Track', 'sidi')
        );
    echo $html;
}

// Save the Metabox Data
function sidi_save_disc_meta($post_id, $post) {
    // verify this came from the our screen and with proper authorization,
    // because save_post can be triggered at other times
    if (empty( $_POST['discometa_noncename']) || !wp_verify_nonce( $_POST['discometa_noncename'], plugin_basename(__FILE__) )) {
        return $post->ID;
    }
    // Is the user allowed to edit the post or page?
    if ( !current_user_can( 'edit_post', $post->ID ))
        return $post->ID;
    // OK, we're authenticated: we need to find and save the data
    // We'll put it into an array to make it easier to loop though.
    $sidi_post = $_POST;
    $sidi_meta = get_post_meta($post->ID);

    $is_discs = false;
    foreach($sidi_meta as $key=>$value){
        if(substr($key,0,5) === 'sidi-'){
            if (substr($key,0,10) !== DISCS) {
                if($key === COVER){
                    $sidi_post[$key] = json_decode(stripslashes($sidi_post[$key]),true);
                } elseif($key === RELEASE) {
                    if (!empty($sidi_post[$key])) {
                        if (!SiDi_Helper::validate_release_date($sidi_post[$key])) {
                            // TODO: fail properly
                            unset($sidi_post[$key]);
                            continue;
                        }
                    }
                }
                if (empty($sidi_post[$key])) {
                    delete_post_meta($post->ID, $key);
                    if(isset($sidi_post[$key]))
                        unset($sidi_post[$key]);
                } else {
                    update_post_meta($post->ID, $key, $sidi_post[$key]);
                    unset($sidi_post[$key]);
                }
            } else {
                $is_discs = true;
            }
        }
    }
    $sidi_discs = array();
    $discs_data = array();
    foreach ($sidi_post as $key=>$value){
        if (substr($key,0,5) === 'sidi-' && $value)
            if(substr($key,0,10) != DISCS){
                if ($key === COVER) {
                    $value = json_decode(stripslashes($value),true);
                } elseif($key === RELEASE) {
                    if (!SiDi_Helper::validate_release_date($value)) {
                        // TODO: fail properly
                        continue;
                    }
                }
                add_post_meta($post->ID, $key, $value);
            } else {
                $id = explode('-', $key); // 0: 'sidi' 1:'discs' 2: num disc 3: num track 4 : what
                $id[2] = intval($id[2], 10);
                if($id[2] > 0){
                    $id[3] = intval($id[3], 10);
                    if($id[3] >= 0){
                        $discs_data[$id[2]][$id[3]][$id[4]] = $value;
                    }
                }
            }
    }
    foreach ($discs_data as $key=>$value) {
        if (!empty($value[0]) && !empty($value[0]['disc'])) {
            $tracks = array();
            foreach ($value as $key_track => $track) {
                if(!empty($track['track']) && trim(implode("",$track))!="") {
                    $tracks[$track['track']] = array(
                        'track' => $track['track'],
                        'title' => $track['title'],
                        'time' => (empty($track['time'])?null:$track['time']));
                }
            }
            if (!empty($tracks)) {
                ksort($tracks);
                $sidi_discs[$value[0]['disc']] = $tracks;
            }
        }
    }
    if(!empty($sidi_discs)){
        ksort($sidi_discs);
        if($is_discs)
            update_post_meta($post->ID, DISCS, $sidi_discs);
        else
            add_post_meta($post->ID, DISCS, $sidi_discs);
    }else{
        if($is_discs)
            delete_post_meta($post->ID, DISCS);
    }

    // Always store the release date, even if it's empty.
    // Otherwise due the INNER JOIN of `wp_posts` and `wp_postmeta` even when the meta field is just used
    // for sorting, albums without this field will not be listed at all.
    $reldate = get_post_meta($post->ID, RELEASE);
    if (empty($reldate)) {
        update_post_meta($post->ID, RELEASE, NULL);
    }
}
