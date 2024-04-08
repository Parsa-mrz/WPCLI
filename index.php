<?php

/**
 * Plugin Name: Books Of June
 * Version: 1.0.0
 * Description:
 * Author: Parsa Mirzaie
 * Author URI: #
 * Requires PHP: 7.1
 */

defined('ABSPATH') || exit;

if (!class_exists('Book_CLI')) {

    class Book_CLI
    {
        public function __construct()
        {
            $this->define_constant();
            $this->init();
        }


        public function activation()
        {
        }
        public function deactivation()
        {
        }
        public function define_constant()
        {
            define("book_DIR", plugin_dir_path(__FILE__));
            define("book_URL", plugin_dir_url(__FILE__));
        }
        public function init()
        {
            register_activation_hook(__FILE__, [$this, 'activation']);
            register_deactivation_hook(__FILE__, [$this, 'deactivation']);
            add_filter('plugin_row_meta', [$this, 'add_plugin_row_meta'], 10, 2);

            $this->add_classes();
        }

        public function add_classes()
        {
            require_once(book_DIR  . 'post-book.php');
            require_once(book_DIR  . 'book-cli.php');
        }
        public function add_plugin_row_meta($links, $file)
        {
            if (plugin_basename(__FILE__) === $file) {
                $submenu_slug = 'book-cli-commands';
                $cli_commands_url = admin_url('edit.php?post_type=book&page=' . $submenu_slug);
                $custom_link = '<a href="' . esc_url($cli_commands_url) . '" target="_blank">See WP CLI Commands</a>';
                $links[] = $custom_link;
            }
            return $links;
        }
    }
    new Book_CLI();
}
