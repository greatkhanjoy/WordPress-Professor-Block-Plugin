<?php
/*
*Plugin Name: Proffesor Plugin | Greatkhanjoy
*Plugin URI: https://greatkhanjoy.wordpress.com/
*Author: Greatkhanjoy
*Author URI: https://greatkhanjoy.me/
*Description: WordPress Professors Plugin
*Version: 1.0.0
*License: GPLv2 or later
*Text Domain: professor-greatkhanjoy
*Domain Path: /languages/
*/

// Exit if Accessed Directly
if (!defined('ABSPATH')) {
    exit;
}

// Load Functions
require_once(plugin_dir_path(__FILE__) . '/inc/generateProfessorHTML.php');


class Professor_Greatkhanjoy
{
    public function __construct()
    {
        add_action('init', array($this, 'custom_post_type'));
        add_action('init', array($this, 'onInit'));
        add_action('after_setup_theme', array($this, 'after_plugin_setup'));
        add_action('rest_api_init', array($this, 'prof_HTML'));
    }

    // Activate Plugin
    public function activate()
    {
        // Generate a CPT
        $this->custom_post_type();

        //Editor Assets
        $this->onInit();

        $this->after_plugin_setup();
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    // Deactivate Plugin
    public function deactivate()
    {
        // Flush rewrite rules
        flush_rewrite_rules();
    }

    // Custom Post Type
    public function custom_post_type()
    {
        register_post_type('professor', [
            'public' => true,
            'menu_icon' => 'dashicons-welcome-learn-more',
            'supports' => ['title', 'editor', 'thumbnail', 'custom-fields'],
            'show_in_rest' => true,
            'publicly_queryable' => true,
            'has_archive' => true,
            'rewrite' => ['slug' => 'professors'],
            'labels' => [
                'name' => 'Professors',
                'add_new_item' => 'Add New Professor',
                'edit_item' => 'Edit Professor',
                'all_items' => 'All Professors',
                'singular_name' => 'Professor'
            ]
        ]);
    }

    public function after_plugin_setup()
    {
        add_image_size('professor', 600, 800, true);
    }

    //REST API PROFESSOR HTML
    function prof_HTML()
    {
        register_rest_route('professor/v1', 'getHTML', array(
            'methods' => WP_REST_Server::READABLE,
            'callback' => array($this, 'prof_HTML_callback')
        ));
    }

    function prof_HTML_callback($data)
    {
        $profId = $data['profId'];
        return generateProfessorHTML($profId);
    }



    public function onInit()
    {

        load_textdomain(
            'professor-greatkhanjoy',
            false,
            dirname(plugin_basename(__FILE__)) . "/languages"
        );

        wp_register_script('professor-greatkhanjoy-editor-js', plugins_url('build/index.js', __FILE__), array('wp-blocks', 'wp-i18n', 'wp-editor'));
        wp_register_script('professor-greatkhanjoy-frontend-js', plugins_url('build/frontend.js', __FILE__), array('wp-blocks', 'wp-i18n', 'wp-editor'));
        wp_register_style('professor-greatkhanjoy-editor-css', plugins_url('build/index.css', __FILE__));

        wp_set_script_translations('professor-script', 'professor-greatkhanjoy', plugin_dir_path(__FILE__) . '/languages');

        register_block_type('greatkhanjoy/professor', array(
            'render_callback' => array($this, 'theHtml'),
            'editor_script' => 'professor-greatkhanjoy-editor-js',
            'editor_style' => 'professor-greatkhanjoy-editor-css'
        ));
    }

    function theHtml($attr)
    {
        if ($attr['profId']) {
            wp_enqueue_style('professor-greatkhanjoy-editor-css');
            wp_enqueue_script('professor-greatkhanjoy-frontend-js');

            return generateProfessorHTML($attr['profId']);
        } else {
            return NULL;
        }
    }
}

if (class_exists('Professor_Greatkhanjoy')) {
    $professor_greatkhanjoy = new Professor_Greatkhanjoy();
}

// Activation
register_activation_hook(__FILE__, array($professor_greatkhanjoy, 'activate'));

// Deactivation
register_deactivation_hook(__FILE__, array($professor_greatkhanjoy, 'deactivate'));

// Uninstall
register_uninstall_hook(__FILE__, array($professor_greatkhanjoy, 'uninstall'));


// Load Functions

function custom_trim_content($content, $num_words, $more_link = '')
{
    $trimmed_content = wp_trim_words($content, $num_words);
    if (!empty($more_link)) {
        $trimmed_content .= ' <a href="' . esc_url(get_permalink()) . '">' . $more_link . '</a>';
    }
    return $trimmed_content;
}
