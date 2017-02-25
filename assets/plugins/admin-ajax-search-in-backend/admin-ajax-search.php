<?php
/**
 * Plugin Name: Admin Ajax Search
 * Plugin URI: http://www.webdesign-shizzle.de
 * Description: Nutze im Backend von WordPress Ajax um zu suchen. Funktioniert für Beiträge, Seiten, Medien, Kommentare etc.
 * Version: 1.2.1
 * Author: Philipp Noack
 * Author URI: http://www.webdesign-shizzle.de
 * License: GPL2
 */
 /*  Copyright 2014  Philipp Noack  (email : philipp@webdesign-shizzle.de)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

define('AJS', plugin_dir_url(__FILE__));


function AJS_load_admin_scripts() {
	wp_enqueue_style( 'ajs_css', AJS . 'css/style.css');
	wp_enqueue_script( 'ajs_js', AJS . 'scripts.js', 'jQuery' , '1.2.1');
}
add_action( 'admin_enqueue_scripts', 'AJS_load_admin_scripts' );

?>