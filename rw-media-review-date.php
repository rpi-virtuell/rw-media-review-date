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
        'label' => 'Review Date',
        'input' => 'html',
        'html'  => "<input type='text' value='$value' 
                    name='attachments[{$post->ID}][rwmrd_review_date]' class='rwmrdreviewdate'
                    id='attachments[{$post->ID}][rwmrd_review_date]' />",
        'helps' => 'Date to review media',
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
    wp_enqueue_script( 'jquery-ui-datepicker' );
    wp_enqueue_script( 'rw-media-review-date-js', plugin_dir_url( __FILE__ ) . 'rw-media-review-date.js' );
}
add_action('admin_enqueue_scripts', 'rwmrd_scripts');

