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

if ( ! class_exists( 'Book_CLI' ) ) {

    class Book_CLI{
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
            $this->add_classes();

        }

        public function add_classes()
        {
            require_once(book_DIR  . 'post-book.php');
            require_once(book_DIR  . 'book-cli.php');

        }
    }
    new Book_CLI();
}