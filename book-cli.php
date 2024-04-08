<?php

if (!class_exists('Book_CLI_Command')) {
        class Book_CLI_Command
        {

                public function __construct()
                {
                        if (defined('WP_CLI') && WP_CLI) {
                                WP_CLI::add_command('book', array($this, 'book_cli_callback'));
                        }
                }

                public function book_cli_callback($args, $assoc_args)
                {
                        // Check if subcommand is provided
                        $subcommand = isset($args[0]) ? $args[0] : '';

                        switch ($subcommand) {
                                case 'generate':
                                        // Implement 'generate<quantity>' subcommand to add a book
                                        // Example: wp book generate 10
                                        if (isset($args[1]) && is_numeric($args[1])) {
                                                $quantity = intval($args[1]);
                                                $this->generate_fake_books($quantity);
                                        } else {
                                                WP_CLI::line("Please provide the quantity for generating books.");
                                        }
                                        break;
                                case 'create':
                                        // Implement 'create' subcommand to update a book
                                        // Example: wp book create --title="New Title" --author="New Author"
                                        $this->create_book($assoc_args);
                                        break;

                                case 'get':
                                        // Implement 'get<id>' subcommand to update a book
                                        // Example: wp book get 145
                                        $this->get_book($assoc_args);
                                        break;

                                case 'update':
                                        // Implement 'get<id>' subcommand to update a book
                                        // Example: wp book get 145

                                        break;

                                case 'delete':
                                        // Implement 'delete' subcommand to delete a book
                                        // Example: wp book delete <book_id>
                                        // Your code here
                                        break;

                                case 'list':
                                        // Implement 'list' subcommand to list all books
                                        // Example: wp book list
                                        // Your code here
                                        break;
                                default:
                                        WP_CLI::line("Unknown subcommand. Supported subcommands: add, update, delete, list");
                                        break;
                        }
                }

                private function generate_fake_books($quantity)
                {
                        // API endpoint URL
                        $url = "https://fakerapi.it/api/v1/books?_quantity=" . $quantity;

                        // Fetch data from the API
                        $response = wp_remote_get($url);

                        // Check if the request was successful
                        if (!is_wp_error($response) && wp_remote_retrieve_response_code($response) === 200) {
                                $body = wp_remote_retrieve_body($response);

                                // Decode JSON response
                                $data = json_decode($body, true);

                                // Check if data exists and has books
                                if (isset($data['status']) && $data['status'] === "OK" && isset($data['data'])) {
                                        $books = $data['data'];

                                        // Create posts for each book
                                        foreach ($books as $book) {
                                                $post_data = array(
                                                        'post_title'    => $book['title'],
                                                        'post_content'  => $book['description'],
                                                        'post_status'   => 'publish',
                                                        'post_author'   => 1, // Set the author ID
                                                        'post_type'     => 'book', // Your custom post type
                                                );

                                                // Insert the post into the database
                                                $post_id = wp_insert_post($post_data);

                                                // Set custom fields for the book
                                                if (!is_wp_error($post_id)) {
                                                        update_post_meta($post_id, '_author', $book['author']);
                                                        update_post_meta($post_id, '_genre', $book['genre']);
                                                        update_post_meta($post_id, '_isbn', $book['isbn']);
                                                        update_post_meta($post_id, '_image', $book['image']);
                                                        update_post_meta($post_id, '_published_date', $book['published']);
                                                        update_post_meta($post_id, '_publisher', $book['publisher']);
                                                        WP_CLI::line("Generated book: " . $book['title']);
                                                } else {
                                                        WP_CLI::line("Error creating book: " . $book['title']);
                                                }
                                        }
                                } else {
                                        WP_CLI::line("No books found in the API response.");
                                }
                        } else {
                                WP_CLI::line("Failed to fetch books from the API.");
                        }
                }

                private function create_book($args)
                {
                        // Extracting values from assoc_args
                        $title = isset($args['title']) ? $args['title'] : '';
                        $description = isset($args['description']) ? $args['description'] : '';
                        $author = isset($args['author']) ? $args['author'] : '';
                        $isbn = isset($args['isbn']) ? $args['isbn'] : '';
                        $genre = isset($args['genre']) ? $args['genre'] : '';
                        $publisher = isset($args['publisher']) ? $args['publisher'] : '';

                        // Check if required fields are provided
                        if (empty($title) || empty($description) || empty($author) || empty($isbn) || empty($genre) || empty($publisher)) {
                                WP_CLI::error("Please provide all required fields: title, description, author, isbn, genre, and publisher.");
                        }

                        // Create post data
                        $post_data = array(
                                'post_title'   => $title,
                                'post_content' => $description,
                                'post_status'  => 'publish',
                                'post_author'  => 1, // Set the author ID
                                'post_type'    => 'book', // Your custom post type
                        );

                        // Insert the post into the database
                        $post_id = wp_insert_post($post_data);

                        // Check if post insertion was successful
                        if (!is_wp_error($post_id)) {
                                // Set custom fields for the book
                                update_post_meta($post_id, '_author', $author);
                                update_post_meta($post_id, '_genre', $genre);
                                update_post_meta($post_id, '_isbn', $isbn);
                                update_post_meta($post_id, '_publisher', $publisher);

                                WP_CLI::success("Book created successfully with ID: $post_id");
                        } else {
                                WP_CLI::error("Failed to create book: " . $post_id->get_error_message());
                        }
                }

                private function get_book($id)
                {
                        // Check if book ID is provided
                        if (empty($id)) {
                                WP_CLI::error("Please provide the book ID.");
                        }

                        // Get the post by ID
                        $book = get_post($id);

                        // Check if the post exists and is of the 'book' post type
                        if ($book && $book->post_type === 'book') {
                                // Retrieve book metadata
                                $author = get_post_meta($id, '_author', true);
                                $genre = get_post_meta($id, '_genre', true);
                                $isbn = get_post_meta($id, '_isbn', true);
                                $publisher = get_post_meta($id, '_publisher', true);

                                // Display book information
                                WP_CLI::line("Book ID: $id");
                                WP_CLI::line("Title: $book->post_title");
                                WP_CLI::line("Description: $book->post_content");
                                WP_CLI::line("Author: $author");
                                WP_CLI::line("Genre: $genre");
                                WP_CLI::line("ISBN: $isbn");
                                WP_CLI::line("Publisher: $publisher");
                        } else {
                                WP_CLI::error("Book not found with ID: $id");
                        }
                }
        }

        // Instantiate the class
        new Book_CLI_Command();
}
