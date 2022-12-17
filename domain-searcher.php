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
    return $form;
}