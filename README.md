# CLI Command

---

## 1- generate 'quantity'

| Option   | Type               | Required | Description                              |
|----------|--------------------|----------|------------------------------------------|
| quantity | Positional Argument| Yes      | Specifies the number of books to generate|

### Usage example:
wp book generate 10

---

## 2- create [--title=title] [--description=description] [--author=author] [--isbn=isbn] [--genre=genre][--publisher=publisher]

| Option      | Type               | Required | Description                                         |
|-------------|--------------------|----------|-----------------------------------------------------|
| title       | Associative Argument | No       | The title of the book.                              |
| description | Associative Argument | No       | The description of the book.                        |
| author      | Associative Argument | No       | The author of the book.                             |
| isbn        | Associative Argument | No       | The International Standard Book Number of the book. |
| genre       | Associative Argument | No       | The genre of the book.                              |
| publisher   | Associative Argument | No       | The publisher of the book.                          |

### Usage example:
wp book create --title="A Brief History of Time" --description="A Brief History of Time: From the Big Bang to Black Holes is a book on theoretical cosmology by English physicist Stephen Hawking. It was first published in 1988. Hawking wrote the book for readers who had no prior knowledge of physics." --author="Stephen Hawking" --isbn="9780553109535" --genre="Cosmology" --publisher="Bantam Dell Publishing Group"

---

## 3- get 'id'

| Option | Type               | Required | Description                        |
|--------|--------------------|----------|------------------------------------|
| id     | Positional Argument| Yes      | Specifies the ID of the book.      |
| format | Associative Argument | No      | Render output in a particular format. Default value: table. Available options: table, csv, json, yaml|

### Usage example:
wp book get 123

---

## 4- update id [--title=title] [--description=description] [--author=author] [--isbn=isbn] [--genre=genre] [--publisher=publisher]

| Option     | Type               | Required | Description                                         |
|------------|--------------------|----------|-----------------------------------------------------|
| id         | Positional Argument| Yes      | The ID of the book.                                 |
| title      | Associative Argument | No      | The title of the book.                              |
| description| Associative Argument | No      | The description of the book.                        |
| author     | Associative Argument | No      | The author of the book.                             |
| isbn       | Associative Argument | No      | The International Standard Book Number of the book. |
| genre      | Associative Argument | No      | The genre of the book.                              |
| publisher  | Associative Argument | No      | The publisher of the book.                          |

### Usage example:
wp book update 123 --author="Stephen William Hawking"

---

## 5- delete 'id'

| Option | Type               | Required | Description                       |
|--------|--------------------|----------|-----------------------------------|
| id     | Positional Argument| Yes      | The ID of the book.               |

### Usage example:
wp book delete 123

---

## 6- list [--format=format]

| Option | Type               | Required | Description                                                                                   |
|--------|--------------------|----------|-----------------------------------------------------------------------------------------------|
| format | Positional Argument| No       | Render output in a particular format. Default value: table. Available options: table, csv, ids,json, count, yaml|

### Usage example:
wp book list --format=json

