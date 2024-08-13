<?php
/*
Plugin Name: Admin Notice with Links
Description: Displays an admin notice with the titles of the most recent blog posts, excluding posts with even IDs, with links to view and edit each post.
Version: 1.0
Author: Carlo San Juan
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Prevent direct access
}

function custom_styled_recent_posts_notice() {
    // Define the query to fetch recent posts
    $recent_posts = new WP_Query(array(
        'posts_per_page' => 10,
        'post_status' => 'publish',
        'orderby' => 'modified',
        'order' => 'DESC'
    ));

    // Initialize an empty array for post titles
    $filtered_titles = array();

    // Loop through posts and filter by ID
    if ( $recent_posts->have_posts() ) {
        while ( $recent_posts->have_posts() ) {
            $recent_posts->the_post();
            if ( get_the_ID() % 2 !== 0 ) { // Only add posts with odd IDs
                $filtered_titles[] = array(
                    'title' => get_the_title(),
                    'view_link' => get_permalink(),
                    'edit_link' => get_edit_post_link()
                );
            }
        }
        wp_reset_postdata(); // Reset the global post object
    }

    // Construct the notice if titles are available
    if ( ! empty( $filtered_titles ) ) {
        $output = '<div class="custom-admin-notice">';
        $output .= '<strong class="custom-notice-title">Last Modified and Published Blog Post Titles:</strong>';
        $output .= '<ul class="custom-post-list">';
        foreach ( $filtered_titles as $post ) {
            $output .= '<li class="custom-post-item">';
            $output .= '<a href="' . esc_url( $post['view_link'] ) . '" target="_blank">' . esc_html( $post['title'] ) . '</a>';
            $output .= ' | <a href="' . esc_url( $post['edit_link'] ) . '" target="_blank">Edit</a>';
            $output .= '</li>';
        }
        $output .= '</ul>';
        $output .= '</div>';

        echo '<div class="notice notice-info is-dismissible">';
        echo wp_kses_post( $output );
        echo '</div>';
    }
}

function custom_notice_styles() {
    echo '
    <style>
        .custom-admin-notice {
            background: #f1f1f1;
            border-left: 5px solid #0073aa;
            padding: 10px 15px;
            border-radius: 5px;
        }
        .custom-notice-title {
            font-size: 16px;
            color: #0073aa;
            margin-bottom: 10px;
            display: block;
        }
        .custom-post-list {
            margin: 0;
            padding: 0;
            list-style: none;
        }
        .custom-post-item {
            background: #ffffff;
            border: 1px solid #e5e5e5;
            padding: 8px 12px;
            border-radius: 3px;
            margin-bottom: 5px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
            transition: all 0.3s ease;
        }
        .custom-post-item:hover {
            background: #e9f5ff;
            border-color: #0073aa;
        }
        .custom-post-item a {
            color: #0073aa;
            text-decoration: none;
        }
        .custom-post-item a:hover {
            text-decoration: underline;
        }
    </style>
    ';
}

// Hook the function to display the admin notice
add_action( 'admin_notices', 'custom_styled_recent_posts_notice' );

// Hook the function to output custom styles
add_action( 'admin_head', 'custom_notice_styles' );

