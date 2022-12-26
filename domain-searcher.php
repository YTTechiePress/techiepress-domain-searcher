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

function add_input_form() {
    $form = '<form>
                <input type="text" value="" name="domain-searcher-input" class="domain-searcher-input" />
                <input type="submit" value="Search" class="domain-searcher-submit" />
            </form>';

    check_availability_xml_data();

    return $form;
}

function check_availability_xml_data() {

    $body = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
    <request cmd="check">
        <domains>
            <domain name="ura.go.ug"></domain>
            <domain name="techiepress.ug"></domain>
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
    
    echo '<pre>';
    var_dump( $domains );
    echo '</pre>';

    return $domains;

}

function convert_xml_to_php_array( $body_response ) {
    
    $formatted_domains = [];

    $xml  = simplexml_load_string( $body_response );
    $json = json_encode($xml);
    $php  = json_decode($json);
    
    foreach ( $php->domains as $domain ) {
        foreach ( $domain as $domain_info ) {
            $domain_log = $domain_info->{'@attributes'};
            
            $name  = $domain_log->name;
            $avail = $domain_log->avail;
            array_push( $formatted_domains, [ 'name' => $name, 'availability' => $avail ] );

        }
    }
    
    return $formatted_domains;

}