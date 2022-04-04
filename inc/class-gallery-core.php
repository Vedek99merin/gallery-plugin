<?php
class GalleyCore {
    public function __construct(){
        $this->init_actions();
        $this->image_sizes();
    }

    public function init_actions(){
        add_action( 'init', [$this, 'post_type_gallery'] );
        add_action( 'add_meta_boxes', [$this, 'property_gallery_add_metabox'] );
        add_action( 'admin_enqueue_scripts', [$this, 'enqueuing_admin_scripts'] );
        add_action( 'wp_ajax_myprefix_get_image', [$this,'myprefix_get_image'] );
        add_action( 'save_post', [$this,'save_post_meta'] );
        add_action( 'wp_enqueue_scripts', [$this,'gallery_plugin_assets'] );
        add_shortcode( 'gallery', [$this, 'gallery_shortcode_html'] );
        add_action( 'admin_notices', array( $this, 'admin_notices' ) );
    }

    function gallery_plugin_assets() {
        wp_enqueue_style( 'gallery-plugin-css', PLUGIN_DIR . '/assets/client/gallery.css' );
        wp_enqueue_script( 'gallery-plugin1', plugins_url( '/assets/client/gallery.js', __FILE__ ) );
    }

    function enqueuing_admin_scripts(){
        wp_enqueue_media();
        wp_enqueue_style('admin-gallery', PLUGIN_DIR . '/assets/admin/gallery.css' );
        wp_enqueue_script('admin-gallery1', PLUGIN_DIR . '/assets/admin/gallery.js', ['jquery'], false, true );
    }
    
    // gallery_shortcode_html
    function gallery_shortcode_html($atts){
        $ids = get_post_meta($atts['id'], 'gallery_attachment_ids', true);
        $ids_array = explode(',', $ids);
        $array = [];
        foreach ($ids_array as $id) {
            $array[] = wp_get_attachment_image($id, $atts['size'], false, ['class'=>'img-ratio']);
        }
        ?>
        <h1 class="title">Gallery</h1>
        <div id="gallery_wrapper-page">

            <?php 
                foreach ($array as $image) {
                    echo $image;
                }
            ?>
        </div>
        <?php
    }

     // ajax for add media
     function myprefix_get_image() {
         $array = [];
         $ids = $_GET['ids'];
         foreach($ids as $id){
            $array[] = wp_get_attachment_image($id);
         }
         $data = array(
            'images'    => $array,
        );
        wp_send_json_success( $data );
    }

    
    //Create custom post type
    public function post_type_gallery(){
        // set up Gallery labels
        $labels = array(
            'name' => 'Gallery',
            'singular_name' => 'Gallery',
            'add_new' => 'Add New Gallery',
            'add_new_item' => 'Add New Gallery',
            'edit_item' => 'Edit Gallery',
            'new_item' => 'New Gallery',
            'all_items' => 'All Gallery',
            'view_item' => 'View Gallery',
            'search_items' => 'Search Gallery',
            'not_found' =>  'No Gallery Found',
            'not_found_in_trash' => 'No Gallery found in Trash', 
            'parent_item_colon' => '',
            'menu_name' => 'Gallery',
        );
        
        // register post type
        $args = array(
            'labels' => $labels,
            'public' => true,
            'has_archive' => true,
            'show_ui' => true,
            'capability_type' => 'post',
            'hierarchical' => false,
            'rewrite' => array('slug' => 'gallery'),
            'query_var' => true,
            'menu_icon' => 'dashicons-randomize',
            'supports' => array(
                'title',
                'page-attributes'
            )
        );
        register_post_type( 'gallery', $args );
        
        // register taxonomy
        register_taxonomy('gallery_category', 'gallery', array('hierarchical' => true, 'label' => 'Category', 'query_var' => true, 'rewrite' => array( 'slug' => 'gallery-category' )));
    }

    //Creating meta box
    function property_gallery_add_metabox(){
        add_meta_box(
            'gallery_attachment_ids',
            'Gallery',
            [$this, 'gallery_html'],
            'gallery', // Change post type name
            'normal',
            'core'
        );
    }
    
    function gallery_html(){
        global $post;
        $ids = get_post_meta($post -> ID, 'gallery_attachment_ids', true);
        $ids_array = explode(',', $ids);
        $array = [];
        foreach ($ids_array as $id) {
            $array[] = wp_get_attachment_image($id);
        }
        // var_dump($ids);
        
        ?>
        
        <div id="gallery_wrapper">
            <div class="use-shortcode">
                <h1>Use shortcode to display gallery</h1>
                <p>[gallery id="<?php echo get_the_ID(); ?>" size="small-gallery"] - size 250x250</p>
                <p>OR</p>
                <p>[gallery id="<?php echo get_the_ID(); ?>" size="medium-gallery"] - size 640x480</p>
            </div>
            <div class="image-container">
                <?php 
                    foreach ($array as $image) {
                        echo $image;
                    }
                ?>
            </div>
        </div>
       
        <div id="add_gallery_single_row">
            <input type="hidden" name="gallery_attachment_ids" id="js_gallery_attachment_ids" value="<?php echo $ids; ?>" class="regular-text" >
            <input type="button" class="button-primary" value="+" title="Add image" id="myprefix_media_manager">
        </div>
        <?php
    }

    
    function save_post_meta($post_id){
        if( isset($_POST['gallery_attachment_ids']) && count($_POST['gallery_attachment_ids']) < 3 ){
            add_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99 );
            return;
        }
        if( isset($_POST['gallery_attachment_ids']) && strlen($_POST['gallery_attachment_ids']) > 0 ){
            update_post_meta(
                $post_id,
                'gallery_attachment_ids',
                $_POST['gallery_attachment_ids']
            );
        }
    }

    function add_notice_query_var($location){
        remove_filter( 'redirect_post_location', array( $this, 'add_notice_query_var' ), 99 );
        return add_query_arg( array( 'ENOUGH_IMAGES' => false ), $location );
    }

    function admin_notices() {
        if ( ! isset( $_GET['ENOUGH_IMAGES'] ) ) {
          return;
        }
        ?>
        <div class="updated">
           <p><?php esc_html_e( 'PIZDA', 'text-domain' ); ?></p>
        </div>
        <?php
    }
	
    // sizes for images
    function image_sizes() {
        add_image_size('medium-gallery', 640, 480, false);
        add_image_size('small-gallery', 250, 250, false);
    }
    


}





?>