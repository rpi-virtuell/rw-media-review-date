<?php
/**
* Plugin Name:      RW Media Review Date
* Plugin URI:       https://github.com/rpi-virtuell/rw-media-review-date
* Description:      Add a review Date to attachments
* Author:           Frank Staude
* Version:          0.0.1
* Licence:          GPLv3
* Author URI:       http://staude.net
* Text Domain:      rw-media-review-date
* Domain Path:      /languages
* GitHub Plugin URI: https://github.com/rpi-virtuell/rw-media-review-date
* GitHub Branch:     master
*/


function rwmrd_attachment_field_date( $form_fields, $post ) {
    $value = get_post_meta( $post->ID, 'rwmrd_review_date', true );
    $form_fields['rwmrd_review_date'] = array(
        'label' => 'Wiedervorlagedatum',
        'input' => 'html',
        'html'  => "<input type='text' value='$value' 
                    name='attachments[{$post->ID}][rwmrd_review_date]' class='rwmrdreviewdate'
                    id='attachments[{$post->ID}][rwmrd_review_date]' />
                    <script> 
                    jQuery(document).ready(function(){
    jQuery( \".rwmrdreviewdate\" ).datepicker({
        changeMonth: true,
        changeYear: true,
        dateFormat: \"dd.mm.yy\",
        showWeek: true,
    });
} )
                    </script>",
        'helps' => 'Datum, wann das Material noch mal bearbeitet werden muss.',
    );
    return $form_fields;
}
add_filter( 'attachment_fields_to_edit', 'rwmrd_attachment_field_date', 10, 2 );


function rwmrd_attachment_field_save( $post, $attachment ) {
	if( isset( $attachment['rwmrd_review_date'] ) ) {
        update_post_meta( $post[ 'ID' ], 'rwmrd_review_date', $attachment['rwmrd_review_date' ] );
    }
    return $post;
}
add_filter( 'attachment_fields_to_save', 'rwmrd_attachment_field_save', 10, 2 );

function rwmrd_scripts(){
    wp_enqueue_style(  'rw-media-review-date-css', plugin_dir_url( __FILE__ ) . 'rw-media-review-date.css' );
    wp_enqueue_script( 'jquery-ui-datepicker' );
}
add_action('admin_enqueue_scripts', 'rwmrd_scripts');


function add_rwmrd_dashboard_widgets() {

    wp_add_dashboard_widget(
        'rwmrd_dashboard_widget',         // Widget slug.
        'Medien zur Wiedervorlage',         // Title.
        'rwmrd_dashboard_widget_function' // Display function.
    );
}
add_action( 'wp_dashboard_setup', 'add_rwmrd_dashboard_widgets' );

/**
 * Create the function to output the contents of our Dashboard Widget.
 */
function rwmrd_dashboard_widget_function() {
    global $wpdb;

    $result = $wpdb->get_results( $wpdb->prepare( "SELECT post_id as ID, `meta_value` as de_date, concat( SUBSTR( meta_value,7,4),SUBSTR( meta_value,4,2),SUBSTR( meta_value,1,2))  as date   FROM  $wpdb->postmeta WHERE  meta_key = 'rwmrd_review_date' and meta_value != '' order by date" ) );

    foreach ( $result as $material ) {

        $url = get_edit_post_link( $material->ID );
        $post = get_post( $material->ID );
        echo $material->de_date . ' <a href="' . $url . '" class="'. apply_filters( 'materialpool-template-material-volumes', 'materialpool-template-material-volumes' ) .'">' . $post->post_title . '</a><br>';
    }

}