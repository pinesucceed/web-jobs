<?php
/**
 * Plugin Name: WEB-JOBS
 * Plugin URI: http://www.wantedweb.de/
 * Description: Jobs Rekrutingmodul und Bewerbungsabgben
 * Version: 1.0.0
 * Author: Wanted Web
 * Author URI: http://www.wantedweb.de/
 * License: GPL2
 */
global $jal_db_version,$custom_breadcrumb_table;
global $wpdb;
$jal_db_version = '1.0';
define('PLUGIN_DIR', WP_PLUGIN_DIR . "/" . plugin_basename(dirname(__FILE__)));
function jal_install() {
    global $wpdb;
	$email = get_option('admin_form_email');
    if(empty($email)){
		$admin_form_email = get_option('admin_email');
            
		update_option('admin_form_email', $admin_form_email);
    }
	$emailcontent = get_option('form_user_email');
    if(empty($emailcontent)){
		$content = <<<EOT
            Hello [given_name] [surname],

            We have received Your request . Will contact You soon.

            Thank You
EOT;
            
		update_option('form_user_email', $content);
    }
	$adminemail = get_option('form_admin_email');
	if(empty($adminemail)){ 
	
		$admincontent = 'Hello Admin,

            [given_name] [surname] applied for a Job. Below are the details Of user:
            
            Name : [given_name] [surname]
            
            Telefonnummer : [telephone]
            
            E-Mail  : [email]
            
			Ich will eine Kopie meiner Bewerbung an meine oben angegebene E-Mailadresse erhalten : [send_copy_to_myself]
           
            Thank You';
            
		update_option('form_admin_email', $admincontent);
	}
}
add_action( 'init', 'createCustomPostType' );
register_activation_hook(__FILE__, 'jal_install');
function createCustomPostType(){
    $labels = array(
		'name'               => _x( 'All Jobs', 'jobs', 'web-jobs' ),
		'singular_name'      => _x( 'Job', 'job', 'web-jobs' ),
		'menu_name'          => _x( 'All Jobs','web-jobs' ),
		'name_admin_bar'     => _x( 'Job', 'web-jobs' ),
		'add_new'            => _x( 'Add New', 'job', 'web-jobs' ),
		'add_new_item'       => __( 'Add New Job', 'web-jobs' ),
		'new_item'           => __( 'New Job', 'web-jobs' ),
		'edit_item'          => __( 'Edit Job', 'web-jobs' ),
		'view_item'          => __( 'View Job', 'web-jobs' ),
		'all_items'          => __( 'All Jobs', 'web-jobs' ),
		'search_items'       => __( 'Search Jobs', 'web-jobs' ),
		'parent_item_colon'  => __( 'Parent Jobs:', 'web-jobs' ),
		'not_found'          => __( 'No jobss found.', 'web-jobs' ),
		'not_found_in_trash' => __( 'No jobs found in Trash.', 'web-jobs' )
	);

	$args = array(
		'labels'             => $labels,
                'description'        => __( 'Description.', 'your-plugin-textdomain' ),
		'public'             => true,
		'publicly_queryable' => true,
		'show_ui'            => true,
		'show_in_menu'       => true,
		'query_var'          => true,
		'rewrite'            => array( 'slug' => 'job' ),
		'capability_type'    => 'post',
		'has_archive'        => true,
		'hierarchical'       => true,
		'menu_position'      => null,
		'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )
	);

	register_post_type( 'job', $args );
      
}
function boot_session() {
  session_start();
}
add_action('wp_loaded','boot_session');
function add_drafts_admin_menu_item() {
    add_submenu_page(
    'edit.php?post_type=job',
    'Application form settings', /*page title*/
    'Settings', /*menu title*/
    'manage_options', /*roles and capabiliyt needed*/
    'settings',
    'applicationforms' /*replace with your own function*/
);
   // add_menu_page('Application form settings', 'Application Form Settings', 'manage_options', 'settings','applicationforms');
}

add_action('admin_menu', 'add_drafts_admin_menu_item');
add_action( 'admin_init', 'add_meta_box_for_date' );
function add_meta_box_for_date(){
     add_meta_box( 'job_date_meta_box',
        'Job date',
        'display_job_date_meta_box',
        'job', 'side', 'low'
    );
     add_action( 'save_post', 'save_job_date', 10, 2 );
}
function display_job_date_meta_box( $job ) {
    $job_date = ( get_post_meta( $job->ID, 'job_date', true ) );
    wp_enqueue_style( 'jquery-ui-datepicker', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.18/themes/smoothness/jquery-ui.css' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'web-jobs', plugins_url( '/js/custom.js' , __FILE__ ) );
    ?>
    <table>
        <tr>
            <td><input type="text" size="30" id="datepicker" name="job_date" value="<?php echo $job_date; ?>" /></td>
        </tr>
    </table>
    <?php
}

function save_job_date( $post_id ,$post) {
        
        
        $post_type = get_post_type_object( $post->post_type );
         if ( !current_user_can( $post_type->cap->edit_post, $post_id ) )
        return $post_id;
         
        $meta_value = get_post_meta( $post_id, 'job_date', true );
        $new_meta_value = ( isset( $_POST['job_date'] ) ? ( $_POST['job_date'] ) : '' );

         /* If a new meta value was added and there was no previous value, add it. */
        if ( $new_meta_value && '' == $meta_value )
          add_post_meta( $post_id,'job_date', $new_meta_value, true );

        /* If the new meta value does not match the old value, update it. */
        elseif ( $new_meta_value && $new_meta_value != $meta_value )
          update_post_meta( $post_id, 'job_date', $new_meta_value );

        /* If there is no new meta value but an old value exists, delete it. */
        elseif ( '' == $new_meta_value && $meta_value )
          delete_post_meta( $post_id, 'job_date', $meta_value );
}
function get($key, $default_value = '') {
    if (isset($_GET[$key])) {
        $value = $_GET[$key];
    } elseif (isset($_POST[$key])) {
        $value = $_POST[$key];
    } else {
        $value = '';
    }
    if (!$value) {
        $value = $default_value;
    }
    return esc_html($value);
}

function alljobs(){
    ob_start();
    global $wpdb;
	$current_page = get_query_var('paged') ? get_query_var('paged'):1;
    $jobs = new WP_Query(
        array(
            'post_type' => 'job',
            'posts_per_page' => 10,
            'post_status' => 'publish',
			"paged" => $current_page
        )
    );
	wp_enqueue_style( 'web-jobs', plugins_url( '/css/form.css' , __FILE__ ));
    if($jobs->have_posts()) : ?>
    <div class="row">
        <?php while($jobs->have_posts()) : $jobs->the_post(); ?>
                <div class="col-sm-12">
                    <div class='job_box'>
                        <h1><a href="<?php the_permalink() ?>"><?php the_title(); ?></a></h1>
                        <?php the_excerpt(); ?>
                        Job Anfang : <?php $job_date = ( get_post_meta( get_the_ID() , 'job_date', true ) ); echo $job_date; ?>
                        <a href="<?php the_permalink() ?>" class='read_more'>MEHR INFO</a>
                    </div>
                </div>
        <?php endwhile;
		?>
		<?php
		$total_pages = $jobs->max_num_pages;
		
		echo paginate_links( array(
"current" => $current_page,
"total" => $total_pages
) );
		?>
    </div>
    <?php endif; wp_reset_query();
    return ob_get_clean();
}
add_shortcode('alljobs', 'alljobs');
function new_excerpt_more($more) {
	global $post;
	if ( 'job' == get_post_type($post->ID) ) {
	return "";
	}else{
	return '...';
	}
}
add_filter('excerpt_more', 'new_excerpt_more');
function applicationforms() {
    global $wpdb;
    $page = get('page');
    $id = get('id');
    if (($page != '') && (($page == 'settings'))) {
		require_once(PLUGIN_DIR . '/' . $page . '.php');
    }
}
/* Filter the single_template with our custom function*/
add_filter('single_template', 'job_template');

function job_template($single) {

    global $wp_query, $post;
    
    /* Checks for single template by post type */
    if ( $post->post_type == 'job' ) {
        if ( file_exists( PLUGIN_DIR . '/job-single.php' ) ) {
            return PLUGIN_DIR . '/job-single.php';
        }
    }

    return $single;

}
?>