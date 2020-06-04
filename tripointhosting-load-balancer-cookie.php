<?php
/**
 * Plugin Name: tripointhosting-loadbalancer-cookie
 * Description: This plugin will create a cookie to identify admin users that
                can be use to process requests at the load balancer

 * Author:      Byron DeLaMatre
 * License:     GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

// Basic security, prevents file from being loaded directly.
defined('ABSPATH') or die('&nbsp;');

// Define the cookie name, allowing for override capability
defined('TRIPOINT_LOAD_BALANCER_ADMIN_COOKIE') or define('TRIPOINT_LOAD_BALANCER_ADMIN_COOKIE','tripoint_load_balancer_admin');

// https://codex.wordpress.org/Plugin_API/Action_Reference/admin_init
add_filter('admin_init','tripoint_load_balancer_admin_cookie',1);

// https://codex.wordpress.org/Plugin_API/Action_Reference/wp_logout
add_action('wp_logout','tripoint_load_balancer_admin_cookie_delete');

/**
 * If user is in admin, create a cookie. Otherwise, remove the cookie.
 *
 * @return bool Boolean value TRUE
 */
function tripoint_load_balancer_admin_cookie(){

    // if the user is an administrator and cookie not set
    if (current_user_can( 'manage_options')
            && (!isset($_COOKIE[TRIPOINT_LOAD_BALANCER_ADMIN_COOKIE]))){

        //set the cookie path
        if(isset($_ENV['WP_BASE'])
            && !empty($_ENV['WP_BASE'])){

            $cookie_path = $_ENV['WP_BASE'];

        }else{

            $cookie_path = '/';

        }

        //create the cookie
        setcookie(TRIPOINT_LOAD_BALANCER_ADMIN_COOKIE, 1, 0 , $cookie_path);

    //if not an admin and cookie is set
    }elseif( isset($_COOKIE[TRIPOINT_LOAD_BALANCER_ADMIN_COOKIE]) ){

        // remove cookie
        tripoint_load_balancer_admin_cookie_delete();

    }

}

function tripoint_load_balancer_admin_cookie_delete() {

    // if cookie is set
    if( isset($_COOKIE[TRIPOINT_LOAD_BALANCER_ADMIN_COOKIE]) ){

        // remove global
        unset($_COOKIE[TRIPOINT_LOAD_BALANCER_ADMIN_COOKIE]);

        // expire the cookie in browser
        setcookie(TRIPOINT_LOAD_BALANCER_ADMIN_COOKIE,-1,(time()-3600));

    }

}