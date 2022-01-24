<?php

/**
 *
 * @link              https://drjest.dev
 * @since             1.0.1
 * @package           Wp_Banner_Rotator
 *
 * @wordpress-plugin
 * Plugin Name:       Wordpress Banner Rotator
 * Plugin URI:        https://drjest.dev/wp/wp-banner-rotator
 * Description:       Simple Plugin to show banners on Wordpress website.
 * Version:           1.0.2
 * Author:            Simone Albano
 * Author URI:        https://drjest.dev
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain:       wp-banner-rotator
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
  die;
}

function wpbr_init() {
  $labels = array(
    'name'               => __( 'Banners', 'wpbr' ),
    'singular_name'      => __( 'Banner', 'wpbr' ),
    'menu_name'          => __( 'Banners', 'wpbr' ),
    'name_admin_bar'     => __( 'Banner', 'wpbr' ),
    'add_new'            => __( 'Add New', 'wpbr' ),
    'add_new_item'       => __( 'Add New Banner', 'wpbr' ),
    'new_item'           => __( 'New Banner', 'wpbr' ),
    'edit_item'          => __( 'Edit Banner', 'wpbr' ),
    'view_item'          => __( 'View Banner', 'wpbr' ),
    'all_items'          => __( 'All Banners', 'wpbr' ),
    'search_items'       => __( 'Search Banners', 'wpbr' ),
    'parent_item_colon'  => __( 'Parent Banners:', 'wpbr' ),
    'not_found'          => __( 'No banners found.', 'wpbr' ),
    'not_found_in_trash' => __( 'No banners found in Trash.', 'wpbr' )
  );
  $args = array(
    'labels'  => $labels,
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => true,
    'show_in_menu' => true,
    'has_archive' => false,
    'menu_icon' => 'dashicons-format-video',
    'taxonomies' => array( 'wpbr_banner' ),
    'supports' => array( 'title', 'editor' )
  );
  register_post_type( 'br_banner', $args );

  $labels = array(
    'name'              => _x( 'Banner Groups', 'taxonomy general name', 'wpbr' ),
    'singular_name'     => _x( 'Banner Group', 'taxonomy singular name', 'wpbr' ),
    'search_items'      => __( 'Search Banner Groups', 'wpbr' ),
    'all_items'         => __( 'All Banner Groups', 'wpbr' ),
    'parent_item'       => __( 'Parent Banner Group', 'wpbr' ),
    'parent_item_colon' => __( 'Parent Banner Group:', 'wpbr' ),
    'edit_item'         => __( 'Edit Banner Group', 'wpbr' ),
    'update_item'       => __( 'Update Banner Group', 'wpbr' ),
    'add_new_item'      => __( 'Add New Banner Group', 'wpbr' ),
    'new_item_name'     => __( 'New Banner Group Name', 'wpbr' ),
    'menu_name'         => __( 'Banner Groups', 'wpbr' ),
  );

  register_taxonomy( 'br_banner_group', 'br_banner', array(
    'labels' => $labels,
    'public' => false,
    'publicly_queryable' => false,
    'show_ui' => true,
    'hierarchical' => true,
    'show_in_menu' => true
  ) );
}

function wpbr_register_settings() {
  add_option( 'wpbr_option_name', 'This is my option value.');
  register_setting( 'wpbr_options_group', 'wpbr_option_name', 'wpbr_callback' );
}

function wpbr_options_page()
{
  require dirname( __FILE__ ) . '/inc/options-page.php';
}

function wpbr_register_options_page() {
  add_options_page(__('WP Banner Rotator'), __('WP Banner Rotator'), 'manage_options', 'wpbr', 'wpbr_options_page');
}

add_filter('user_can_richedit', function( $default ){
  if( get_post_type() === 'br_banner')  return false;
  return $default;
});

function wpbr_banner_metaboxes( ) {
  global $wp_meta_boxes;
  add_meta_box('banner_stats_div', __('Stats'), 'wpbr_banner_metaboxes_html', 'br_banner', 'normal', 'high');
}

function wpbr_banner_metaboxes_html() {
  global $post;
  $custom = get_post_custom($post->ID);
  $impressions = isset($custom["impressions"][0]) ? $custom["impressions"][0] : 0;
  $clicks = isset($custom["clicks"][0])      ? $custom["clicks"][0]      : 0;
?>
  <label>Impressions:</label>
  <span data-name="impressions"><?php echo $impressions; ?></span>
  <br>
  <label>Clicks:</label>
  <span data-name="clicks"><?php echo $clicks; ?></span>
<?php
}

function wpbr_admin_head() {
  ?>
  <script type="text/javascript">
    function toClipboard(el) {
      let t = document.createElement('textarea');
      t.value = el.innerText;
      document.body.appendChild(t);
      t.select();
      document.execCommand("copy");
      document.body.removeChild(t);
      let title = el.title;
      el.title = 'Copied';
      setTimeout(function(){
        el.title = title;
      }, 1500);
    }
  </script>
  <?php
}

function wpbr_columns_head($defaults) {
  $defaults['wpbr_group'] = 'Group';
  $defaults['wpbr_impressions'] = 'Impressions';
  $defaults['wpbr_clicks'] = 'Clicks';
  $defaults['wpbr_code'] = 'Code';
  unset($defaults['date']);
  return $defaults;
}

function wpbr_columns_content($column, $post_ID) {
  $custom = get_post_custom($post_ID);
  $terms = wp_get_post_terms( $post_ID, 'br_banner_group' );
  if($terms && count($terms)) {
    $group = $terms[0]->name;
  }
  $impressions = isset($custom["impressions"][0]) ? $custom["impressions"][0] : 0;
  $clicks = isset($custom["clicks"][0])      ? $custom["clicks"][0]      : 0;
  switch ($column) {
    case 'wpbr_group':
      echo $group;
    break;
    case 'wpbr_impressions':
      echo $impressions;
    break;
    case 'wpbr_clicks':
      echo $clicks;
    break;
    case 'wpbr_code':
      $code = '[wpbr id="'.$post_ID.'"]';
      echo '<a href="#" onclick="toClipboard(this);" title="Click to copy">'.$code.'</a>';
    break;
  }  
}

function wpbr_tax_columns($columns) {
  unset($columns['description']);
  $columns['wpbr_rotate_every'] = __('Rotate Every');
  $columns['wpbr_code'] = __('Code');
  return $columns;
}

function wpbr_tax_columns_content( $content, $column, $term_id ) {
  $term = get_term( $term_id );
  $term_meta = get_option( 'taxonomy_term_'.$term_id );
  $rotate_every = $term_meta['rotate_every'] ? intval($term_meta['rotate_every']) : 0;

  $code = '[wpbr group="'.$term->slug.'"]';
  switch ($column) {
    case 'wpbr_rotate_every':
      echo $rotate_every ? $rotate_every : 'Random';
    break;
    case 'wpbr_code':
      echo '<a href="#" onclick="toClipboard(this);" title="Click to copy">'.$code.'</a>';
    break;
  }
}

function br_banner_group_custom_field( $term ) {
  $term_id = $term->term_id;
  $term_meta = get_option( 'taxonomy_term_'.$term_id );
  $rotate_every = $term_meta['rotate_every'] ? intval($term_meta['rotate_every']) : 0;
  ?>
    <tr class="form-field">  
      <th scope="row" valign="top">  
        <label for="rotate_every"><?php _e('Rotate Every'); ?></label>  
      </th>  
      <td>  
        <input type="number" name="term_meta[rotate_every]" id="term_meta[rotate_every]" group="25" style="width:60%;" value="<?php echo $rotate_every; ?>"><br />  
        <span class="description"><?php _e('Rotate Every X Impressions ( 0 means Random )'); ?></span>  
      </td>  
    </tr>  

  <?php
}

function wpbr_save_tax_custom_fields( $term_id ) {  
  if ( isset( $_POST['term_meta'] ) ) {
    $t_id = $term_id;  
    $term_meta = get_option( "taxonomy_term_$t_id" );  
    $cat_keys = array_keys( $_POST['term_meta'] );  
      foreach ( $cat_keys as $key ){  
      if ( isset( $_POST['term_meta'][$key] ) ){  
        $term_meta[$key] = $_POST['term_meta'][$key];  
      }  
    }   
    update_option( "taxonomy_term_$t_id", $term_meta );  
  }  
}

function wpbr_show_banner( $banner, $style = '' ) {
  $id = wp_cache_get('wpbr-banner-id');
  if(!$id) $id = 0;
  $id++;
  wp_cache_set( 'wpbr-banner-id', $id );
  $impressions = get_post_meta( $banner->ID, 'impressions' );
  $impressions = $impressions ? intval($impressions[0]) : 0;
  $impressions++;
  update_post_meta( $banner->ID, 'impressions', $impressions );
  echo '<div class="wpbr-banner"'.($style?'style="'.$style.'" ' : '').' id="wpbr-banner-'.$id.'" data-id="'.$banner->ID.'">';
  echo $banner->post_content;
  echo '</div>';
}

function wpbr_shortcode( $atts ) {
  $style = '';
  if(isset($atts['style'])) {
	  $style = $atts['style'];
  }
  if(isset($atts['id'])) {
    $banner = get_post( $atts['id'] );
    wpbr_show_banner( $banner, $style );
  }
  else if(isset($atts['group'])) {
    $term = get_term_by('slug', $atts['group'], 'br_banner_group');
    $count = $term->count;

    if(!$term->count) return;

    $term_meta = get_option( 'taxonomy_term_'.$term->term_id );
    $every = $term_meta['rotate_every'] ? intval($term_meta['rotate_every']) : 0;
    $args = array(
      'post_type' => 'br_banner',
      'post_status' => 'publish',
      'posts_per_page' => -1,
      'tax_query' => array(
        array(
          'taxonomy' => 'br_banner_group',
          'field' => 'id',
          'terms' => $term->term_id
        )
      )
    );

    if(!$every) {
      // random 
      $args['posts_per_page'] = 1;
      $args['orderby'] = 'rand';
      $the_query = new WP_Query( $args );
      $banner = $the_query->posts[0];
      wpbr_show_banner( $banner, $style );
      return;
    }

    $the_query = new WP_Query( $args );

    $curid = 'wpbr-ads-'.$atts['group'].'-ad';
    $impid = 'wpbr-ads-'.$atts['group'].'-cn';

    $cur = wpbr_cache_get($curid);
    if(!$cur) $cur = 0;
    $imp = wpbr_cache_get($impid);
    if(!$imp) $imp = 0;

    if( ++$imp > $every ) {
      $cur = ($cur+1) % $count;
      $imp = 0;
    }

    wpbr_cache_set( $curid, $cur );
    $r = wpbr_cache_set( $impid, $imp );
    
    $banner = $the_query->posts[$cur];

    wpbr_show_banner( $banner, $style );
  }
}

function wpbr_cache_get($key) {
  if(function_exists('apc_fetch')) {
    return apc_fetch($key);
  }
  return get_transient( $key );
}

function wpbr_cache_set( $key, $val ) {
  if(function_exists('apc_store')) {
    return apc_store($key, $val);
  }
  return set_transient( $key, $val );
}

function wpbr_track() {
  $banner_id = $_POST['banner_id'];
  $clicks = get_post_meta( $banner_id, 'clicks' );
  $clicks = $clicks ? intval($clicks[0]) : 0;
  $clicks++;
  update_post_meta( $banner_id, 'clicks', $clicks );
  wp_die();
}

add_action( 'br_banner_group_edit_form_fields', 'br_banner_group_custom_field', 10, 2 );  

add_filter( 'manage_br_banner_posts_columns', 'wpbr_columns_head' );
add_filter( 'manage_edit-br_banner_group_columns', 'wpbr_tax_columns'); 
add_filter( 'admin_head', 'wpbr_admin_head' );
add_filter( 'manage_br_banner_posts_custom_column', 'wpbr_columns_content', 10, 2 );
add_filter( 'manage_br_banner_group_custom_column',  'wpbr_tax_columns_content', 10, 3 );
add_action( 'edited_br_banner_group', 'wpbr_save_tax_custom_fields', 10, 2 );  

add_action( 'add_meta_boxes', 'wpbr_banner_metaboxes' );
add_action( 'init', 'wpbr_init' );
add_action( 'admin_init', 'wpbr_register_settings' );
add_action( 'admin_menu', 'wpbr_register_options_page' );

add_action( 'wp_enqueue_scripts', function() {
  $url = trailingslashit( plugin_dir_url( __FILE__ ) );
  wp_enqueue_script( 'wpbr_script', $url . 'assets/js/scripts.js', array( 'jquery', 'wp-util' ), '1.0.2', true );
  wp_enqueue_style( 'wpbr_style', $url . 'assets/css/style.css', null, '1.0.2' );
} );
add_action( 'wp_ajax_wpbr_track', 'wpbr_track' );
add_action( 'wp_ajax_nopriv_wpbr_track', 'wpbr_track' );

add_shortcode( 'wpbr', 'wpbr_shortcode' );
