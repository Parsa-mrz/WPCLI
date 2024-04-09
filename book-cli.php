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
                                        if (isset($args[1])) {
                                                $book_id = $args[1];
                                                $this->get_book($book_id, $assoc_args);
                                        } else {
                                                WP_CLI::error("Please provide the book ID.");
                                        }
                                        break;

                                case 'update':
                                        // Implement 'get<id>' subcommand to update a book
                                        // Example: wp book update 145 --title="New Title" --author="New Author" .....
                                        if (isset($args[1])) {
                                                $book_id = $args[1];
                                                $this->update_book($book_id, $assoc_args);
                                        } else {
                                                WP_CLI::error("Please provide the book ID.");
                                        }
                                        break;

                                case 'delete':
                                        // Implement 'delete' subcommand to delete a book
                                        // Example: wp book delete <book_id>
                                        if (isset($args[1])) {
                                                $book_id = $args[1];
                                                $this->delete_book($book_id);
                                        } else {
                                                WP_CLI::error("Please provide the book ID.");
                                        }
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

                private function create_book($assoc_args)
                {
                        // Check for all required parameters
                        $required_params = ['title', 'description', 'author', 'isbn', 'genre', 'publisher'];
                        foreach ($required_params as $param) {
                                if (!isset($assoc_args[$param])) {
                                        WP_CLI::error("Please provide all required fields: title, description, author, isbn, genre, and publisher.");
                                        return;
                                }
                        }

                        // Prepare the post data
                        $post_data = array(
                                'post_title'   => wp_strip_all_tags($assoc_args['title']),
                                'post_content' => $assoc_args['description'],
                                'post_status'  => 'publish',
                                'post_type'    => 'book', // Assuming 'book' is a custom post type
                                // Custom meta fields
                                'meta_input'   => array(
                                        '_author'    => $assoc_args['author'],
                                        '_isbn'      => $assoc_args['isbn'],
                                        '_genre'     => $assoc_args['genre'],
                                        '_publisher' => $assoc_args['publisher']
                                )
                        );

                        // Insert the book into the database
                        $post_id = wp_insert_post($post_data);

                        if ($post_id) {
                                WP_CLI::success("Book created successfully with ID: $post_id");
                        } else {
                                WP_CLI::error("There was an error creating the book.");
                        }
                }


                private function get_book($book_id, $assoc_args)
                {

                        // Get the post by ID
                        $book = get_post($book_id);

                        // Check if the post exists and is of the 'book' post type
                        if ($book && $book->post_type === 'book') {
                                // Retrieve book metadata
                                $author = get_post_meta($book_id, '_author', true);
                                $genre = get_post_meta($book_id, '_genre', true);
                                $isbn = get_post_meta($book_id, '_isbn', true);
                                $publisher = get_post_meta($book_id, '_publisher', true);

                                // Prepare book data
                                $book_data = array(
                                        'ID' => $book_id,
                                        'Title' => $book->post_title,
                                        'Description' => $book->post_content,
                                        'Author' => $author,
                                        'Genre' => $genre,
                                        'ISBN' => $isbn,
                                        'Publisher' => $publisher,
                                );

                                // Check the format argument to determine the output format
                                $format = isset($assoc_args['format']) ? $assoc_args['format'] : 'table';

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
                                        case 'csv':
                                                // Open the output stream
                                                $output = fopen('php://output', 'w');
                                                if ($output === false) {
                                                        WP_CLI::error("Failed to open output stream for CSV.");
                                                        break;
                                                }

                                                // Set the header for CSV download
                                                header('Content-Type: text/csv');
                                                header('Content-Disposition: attachment; filename="book.csv"');

                                                // Add CSV column headers
                                                fputcsv($output, array('ID', 'Title', 'Description', 'Author', 'Genre', 'ISBN', 'Publisher'));

                                                // Add book data
                                                fputcsv($output, $book_data);

                                                // Close the output stream
                                                fclose($output);
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

                private function update_book($book_id, $assoc_args)
                {

                        // Get the post by ID
                        $book = get_post($book_id);

                        // Check if the post exists and is of the 'book' post type
                        if ($book && $book->post_type === 'book') {
                                // Extract updated values from assoc_args
                                $updated_data = array(
                                        'post_title'   => isset($assoc_args['title']) ? $assoc_args['title'] : $book->post_title,
                                        'post_content' => isset($assoc_args['description']) ? $assoc_args['description'] : $book->post_content,
                                );

                                // Update the post
                                $updated = wp_update_post(array_merge(['ID' => $book_id], $updated_data), true);

                                if (!is_wp_error($updated)) {
                                        // Update custom fields for the book
                                        update_post_meta($book_id, '_author', isset($assoc_args['author']) ? $assoc_args['author'] : get_post_meta($book_id, '_author', true));
                                        update_post_meta($book_id, '_genre', isset($assoc_args['genre']) ? $assoc_args['genre'] : get_post_meta($book_id, '_genre', true));
                                        update_post_meta($book_id, '_isbn', isset($assoc_args['isbn']) ? $assoc_args['isbn'] : get_post_meta($book_id, '_isbn', true));
                                        update_post_meta($book_id, '_publisher', isset($assoc_args['publisher']) ? $assoc_args['publisher'] : get_post_meta($book_id, '_publisher', true));

                                        WP_CLI::success("Book updated successfully with ID: $book_id");
                                } else {
                                        WP_CLI::error("Failed to update book: " . $updated->get_error_message());
                                }
                        } else {
                                WP_CLI::error("Book not found with ID: $book_id");
                        }
                }

                private function delete_book($book_id)
                {

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
                                case 'csv':
                                        // Open the output stream
                                        $output = fopen('php://output', 'w');
                                        if ($output === false) {
                                                WP_CLI::error("Failed to open output stream for CSV.");
                                                break;
                                        }

                                        // Set the header for CSV download
                                        header('Content-Type: text/csv');
                                        header('Content-Disposition: attachment; filename="book.csv"');

                                        // Add CSV column headers
                                        fputcsv($output, array('ID', 'Title', 'Description', 'Author', 'Genre', 'ISBN', 'Publisher'));

                                        // Add book data
                                        foreach ($formatted_books as $book_data) {
                                                fputcsv($output, $book_data);
                                        }

                                        // Close the output stream
                                        fclose($output);
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
