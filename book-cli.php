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
                                        $quantity = isset($args[1]) && is_numeric($args[1]) ? intval($args[1]) : 1; // Default to 1 if not provided
                                        $this->generate_fake_books($quantity);
                                        break;
                                case 'create':
                                        // Implement 'create' subcommand to update a book
                                        // Example: wp book create --title="New Title" --author="New Author" .....
                                        $this->create_book($assoc_args);
                                        break;

                                case 'get':
                                        // Implement 'get<id>' subcommand to update a book
                                        // Example: wp book get 145
                                        $this->get_book($assoc_args);
                                        break;

                                case 'update':
                                        // Implement 'get<id>' subcommand to update a book
                                        // Example: wp book update 145 --title="New Title" --author="New Author" .....
                                        $this->update_book($assoc_args);
                                        break;

                                case 'delete':
                                        // Implement 'delete' subcommand to delete a book
                                        // Example: wp book delete <book_id>
                                        $this->delete_book($assoc_args);
                                        break;

                                case 'list':
                                        // Implement 'list' subcommand to list all books
                                        // Example: wp book list <JSON,ASCII table, YAML>
                                        $this->format_list($assoc_args);
                                        break;

                                default:
                                        WP_CLI::line("Unknown subcommand. Supported subcommands: generate, create, get, update, delete, list");
                                        break;
                        }
                }

                private function generate_fake_books($quantity)
                {
                        // Check if 'quantity' argument is provided and is a numeric value
                        // if (!isset($assoc_args['quantity']) || !is_numeric($assoc_args['quantity'])) {
                        //         WP_CLI::line("Please provide the quantity for generating books.");
                        //         return;
                        // }

                        // $quantity = intval($assoc_args['quantity']);

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

                private function get_book($args)
                {
                        // Check if book ID is provided
                        if (empty($args[0])) {
                                WP_CLI::error("Please provide the book ID.");
                        }

                        // Extract book ID from args
                        $book_id = $args[0];

                        // Get the post by ID
                        $book = get_post($book_id);

                        // Check if the post exists and is of the 'book' post type
                        if ($book && $book->post_type === 'book') {
                                // Retrieve book metadata
                                $author = get_post_meta($book->ID, '_author', true);
                                $genre = get_post_meta($book->ID, '_genre', true);
                                $isbn = get_post_meta($book->ID, '_isbn', true);
                                $publisher = get_post_meta($book->ID, '_publisher', true);

                                // Prepare book data
                                $book_data = array(
                                        'ID' => $book->ID,
                                        'Title' => $book->post_title,
                                        'Description' => $book->post_content,
                                        'Author' => $author,
                                        'Genre' => $genre,
                                        'ISBN' => $isbn,
                                        'Publisher' => $publisher,
                                );

                                // Check the format argument to determine the output format
                                $format = isset($args['format']) ? $args['format'] : 'table';

                                // Output the formatted data based on the specified format
                                switch ($format) {
                                        case 'table':
                                                WP_CLI\Utils\format_items('table', array($book_data), array('ID', 'Title', 'Description', 'Author', 'Genre', 'ISBN', 'Publisher'));
                                                break;
                                        case 'yaml':
                                                WP_CLI::line(yaml_emit(array($book_data)));
                                                break;
                                        case 'json':
                                                WP_CLI::line(json_encode(array($book_data)));
                                                break;
                                        case 'ids':
                                                WP_CLI::line($book->ID);
                                                break;
                                        case 'count':
                                                WP_CLI::line(1);
                                                break;
                                        default:
                                                WP_CLI::error("Invalid format. Supported formats: table, yaml, json, ids, count");
                                                break;
                                }
                        } else {
                                WP_CLI::error("Book not found with ID: $book_id");
                        }
                }

                private function update_book($args)
                {
                        // Check if book ID is provided
                        if (empty($args[0])) {
                                WP_CLI::error("Please provide the book ID.");
                        }

                        // Extract book ID from args
                        $book_id = $args[0];

                        // Get the post by ID
                        $book = get_post($book_id);

                        // Check if the post exists and is of the 'book' post type
                        if ($book && $book->post_type === 'book') {
                                // Extract updated values from assoc_args
                                $updated_data = array(
                                        'post_title'   => isset($args['title']) ? $args['title'] : $book->post_title,
                                        'post_content' => isset($args['description']) ? $args['description'] : $book->post_content,
                                );

                                // Update the post
                                $updated = wp_update_post(array_merge(['ID' => $book_id], $updated_data), true);

                                if (!is_wp_error($updated)) {
                                        // Update custom fields for the book
                                        update_post_meta($book_id, '_author', isset($args['author']) ? $args['author'] : get_post_meta($book_id, '_author', true));
                                        update_post_meta($book_id, '_genre', isset($args['genre']) ? $args['genre'] : get_post_meta($book_id, '_genre', true));
                                        update_post_meta($book_id, '_isbn', isset($args['isbn']) ? $args['isbn'] : get_post_meta($book_id, '_isbn', true));
                                        update_post_meta($book_id, '_publisher', isset($args['publisher']) ? $args['publisher'] : get_post_meta($book_id, '_publisher', true));

                                        WP_CLI::success("Book updated successfully with ID: $book_id");
                                } else {
                                        WP_CLI::error("Failed to update book: " . $updated->get_error_message());
                                }
                        } else {
                                WP_CLI::error("Book not found with ID: $book_id");
                        }
                }

                private function delete_book($args)
                {
                        // Check if book ID is provided
                        if (empty($args[0])) {
                                WP_CLI::error("Please provide the book ID.");
                        }

                        // Extract book ID from args
                        $book_id = $args[0];

                        // Get the post by ID
                        $book = get_post($book_id);

                        // Check if the post exists and is of the 'book' post type
                        if ($book && $book->post_type === 'book') {
                                // Delete the post
                                $deleted = wp_delete_post($book_id, true); // Set to true to force delete permanently

                                if ($deleted !== false) {
                                        WP_CLI::success("Book deleted successfully with ID: $book_id");
                                } else {
                                        WP_CLI::error("Failed to delete book with ID: $book_id");
                                }
                        } else {
                                WP_CLI::error("Book not found with ID: $book_id");
                        }
                }

                private function format_list($args)
                {
                        // Get all books
                        $books = get_posts(array(
                                'post_type' => 'book',
                                'posts_per_page' => -1, // Retrieve all posts
                        ));

                        // Prepare data for formatting
                        $formatted_books = array();
                        foreach ($books as $book) {
                                $formatted_books[] = array(
                                        'ID' => $book->ID,
                                        'Title' => $book->post_title,
                                        'Description' => $book->post_content,
                                        'Author' => get_post_meta($book->ID, '_author', true),
                                        'Genre' => get_post_meta($book->ID, '_genre', true),
                                        'ISBN' => get_post_meta($book->ID, '_isbn', true),
                                        'Publisher' => get_post_meta($book->ID, '_publisher', true),
                                );
                        }

                        // Check the format argument to determine the output format
                        $format = isset($args['format']) ? $args['format'] : 'table';

                        // Output the formatted data based on the specified format
                        switch ($format) {
                                case 'table':
                                        WP_CLI\Utils\format_items('table', $formatted_books, array('ID', 'Title', 'Description', 'Author', 'Genre', 'ISBN', 'Publisher'));
                                        break;
                                case 'yaml':
                                        WP_CLI::line(yaml_emit($formatted_books));
                                        break;
                                case 'json':
                                        WP_CLI::line(json_encode($formatted_books));
                                        break;
                                case 'ids':
                                        foreach ($formatted_books as $book) {
                                                WP_CLI::line($book['ID']);
                                        }
                                        break;
                                case 'count':
                                        WP_CLI::line(count($formatted_books));
                                        break;
                                default:
                                        WP_CLI::error("Invalid format. Supported formats: table, yaml, json, ids, count");
                                        break;
                        }
                }
        }

        // Instantiate the class
        new Book_CLI_Command();
}
