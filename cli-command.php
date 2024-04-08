<div class="wrapper">
    <h1>CLI Command</h1>
    <hr><br><br>

    <h2>1- genereate 'quantity' </h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Option</th>
                <th>Type</th>
                <th>Required</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>quantity</td>
                <td>Positional Argument</td>
                <td>Yes</td>
                <td>Specifies the number of books to generate.</td>
            </tr>
        </tbody>
    </table>

    <h3>Usage example:</h3>
    <p>wp book generate 10</p>
    <hr>
    <br>


    <h2>2- create [--title=title] [--description=description] [--author=author] [--isbn=isbn] [--genre=genre][--publisher=publisher] </h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Option</th>
                <th>Type</th>
                <th>Required</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>title </td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The title of the book.</td>
            </tr>
            <tr>
                <td>description </td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The description of the book.</td>
            </tr>
            <tr>
                <td>author </td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The author of the book.</td>
            </tr>
            <tr>
                <td>isbn</td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The International Standard Book Number of the book.</td>
            </tr>
            <tr>
                <td>genre </td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The genre of the book.</td>
            </tr>
            <tr>
                <td>publisher</td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The publisher of the book.</td>
            </tr>
        </tbody>
    </table>

    <h3>Usage example:</h3>
    <p>wp book create --title="A Brief History of Time" --description="A Brief History of Time: From the Big Bang to Black Holes is a book on theoretical cosmology by English physicist
        Stephen Hawking. It was first published in 1988. Hawking wrote
        the book for readers who had no prior knowledge of physics." --
        author="Stephen Hawking" --isbn="9780553109535" --
        genre="Cosmology" --publisher="Bantam Dell Publishing Group"</p>
    <hr>
    <br>

    <h2>3- get 'id' </h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Option</th>
                <th>Type</th>
                <th>Required</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>id</td>
                <td>Positional Argument</td>
                <td>Yes</td>
                <td>Specifies the ID of the book.</td>
            </tr>
            <tr>
                <td>format</td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>Render output in a particular format.
                    Default value: table. Available options:
                    table, csv, json, yaml</td>
            </tr>
        </tbody>
    </table>

    <h3>Usage example:</h3>
    <p>wp book get 123</p>
    <hr>
    <br>

    <h2>4- update id [--title=title] [--description=description] [--author=author] [--isbn=isbn] [--genre=genre] [--publisher=publisher] </h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Option</th>
                <th>Type</th>
                <th>Required</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>id </td>
                <td>Positional Argument</td>
                <td>Yes</td>
                <td>The ID of the book.</td>
            </tr>
            <tr>
                <td>title </td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The title of the book.</td>
            </tr>
            <tr>
                <td>description </td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The description of the book.</td>
            </tr>
            <tr>
                <td>author </td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The author of the book.</td>
            </tr>
            <tr>
                <td>isbn</td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The International Standard Book Number of the book.</td>
            </tr>
            <tr>
                <td>genre </td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The genre of the book.</td>
            </tr>
            <tr>
                <td>publisher</td>
                <td>Associative Argument</td>
                <td>No</td>
                <td>The publisher of the book.</td>
            </tr>
        </tbody>
    </table>

    <h3>Usage example:</h3>
    <p>wp book update 123 --author="Stephen William Hawking"</p>
    <hr>
    <br>

    <h2>5- delete 'id' </h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Option</th>
                <th>Type</th>
                <th>Required</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>id</td>
                <td>Positional Argument</td>
                <td>Yes</td>
                <td>The ID of the book.</td>
            </tr>
        </tbody>
    </table>

    <h3>Usage example:</h3>
    <p>wp book delete 123</p>
    <hr>
    <br>

    <h2>6- list [--format=format] </h2>
    <table class="wp-list-table widefat fixed striped">
        <thead>
            <tr>
                <th>Option</th>
                <th>Type</th>
                <th>Required</th>
                <th>Description</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>format</td>
                <td>Positional Argument</td>
                <td>No</td>
                <td>Render output in a particular format. Default value: table. Available options: table, csv, ids,json, count, yaml</td>
            </tr>
        </tbody>
    </table>

    <h3>Usage example:</h3>
    <p>wp book list --format=json</p>
    <hr>
    <br>
</div>