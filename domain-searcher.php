<?php
/**
 * Plugin Name: TechiePress Domain Searcher
 * Plugin URI:  https://omukiguy.com
 * Author:      TechiePress
 * Author URI:  https://omukiguy.com
 * Description: Enables the search, renewal and registration of UG domains.
 * Version:     0.1.0
 * License:     GPL-2.0+
 * License URL: http://www.gnu.org/licenses/gpl-2.0.txt
 * text-domain: techiepress-domain-searcher
*/

defined( 'ABSPATH' ) or die( 'Unauthorize access!' );

add_shortcode( 'techiepress_domains', 'add_input_form' );
add_action( 'wp_ajax_query_xml_api', 'techiepress_query_xml_api' );
add_action( 'wp_ajax_nopriv_query_xml_api', 'techiepress_query_xml_api' );

function techiepress_query_xml_api() {

    // [action] => query_xml_api
    // [search_text] => techiepress.ug
    error_log( print_r( $_POST, true ) );

    if ( empty( $_POST ) || empty( $_POST['search_text'] ) ) {
        echo json_encode( 'Required POST Items missing' );
        wp_die();
    }

    if ( ! empty( $_POST ) || ! empty( $_POST['search_text'] ) ) {
        $results = check_availability_xml_data( $_POST['search_text'] );
        echo json_encode( $results );
        wp_die();
    }


}

function add_input_form() {
    
    wp_enqueue_script( 'techiepress-xml-form-check', plugin_dir_url( __FILE__ ) . '/assets/js/api-xml-searcher.js', array( 'jquery' ), '1.0.0', true );
    wp_localize_script( 'techiepress-xml-form-check', 'ajax_values', array(
        'ajax_url' => admin_url( 'admin-ajax.php' ),
    ) ); 

    $form = '<form>
                <input type="text" value="" name="domain-searcher-input" id="domain-searcher-input" class="domain-searcher-input" />
                <input type="submit" value="Search" id="domain-searcher-submit" class="domain-searcher-submit" />
                <div id="registration" class="registration"></div>
            </form>';

    // check_availability_xml_data();

    return $form;
}

function check_availability_xml_data( $domain_value ) {

    $body = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
    <request cmd="check">
        <domains>
            <domain name="' . $domain_value . '"></domain>
        </domains>
    </request>';

    $url  = 'https://registry.co.ug/api';
    $args = array(
        'method'  => 'POST',
        'body'    => $body,
        'headers' => array(
            'Content-Type' => 'application/xml'
        ),
    );

    $results       = wp_remote_post( $url, $args );
    $body_response = wp_remote_retrieve_body( $results );

    $domains       = convert_xml_to_php_array( $body_response );

    return $domains;

}

function convert_xml_to_php_array( $body_response ) {
    
    $formatted_domains = [];

    $xml  = simplexml_load_string( $body_response );
    $json = json_encode($xml);
    $php  = json_decode($json);
    
    if ( ! is_array( $php->domains ) ) {
        $domain_log = $php->domains->domain->{'@attributes'};
        array_push( $formatted_domains, [ 'name' => $domain_log->name, 'availability' => $domain_log->avail ] );
    }

    if ( is_array( $php->domains ) ) {
        foreach ( $php->domains as $domain ) {
            foreach ( $domain as $domain_info ) {
                $domain_log = $domain_info->{'@attributes'};
    
                $name  = $domain_log->name;
                $avail = $domain_log->avail;
                array_push( $formatted_domains, [ 'name' => $name, 'availability' => $avail ] );
            }
        }
    }
    
    return $formatted_domains;

}