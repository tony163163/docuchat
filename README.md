# DocuChat: A Document Chat Application with OpenAI Embeddings

DocuChat is a chat application that allows users to communicate with each other by exchanging documents and messages. The application uses OpenAI embeddings for faster and more secure communication, and the embeddings are stored locally, eliminating the need for external API requests.

## Features

Chat platform: Users can communicate with each other by exchanging documents and messages.
Local embeddings: The chat application uses OpenAI embeddings for faster and more secure communication. The embeddings are stored locally, eliminating the need for external API requests.
Document exchange: Users can exchange documents through the chat platform, allowing for easy collaboration.
Search: Users can search for specific documents or messages within the chat application.

## Technology

DocuChat is built using HTML, PHP, and JavaScript. The front-end of the application is built using JavaScript, providing a dynamic and responsive user interface. The back-end of the application is built using PHP, allowing for server-side processing and storage of the local embeddings. The local embeddings are stored in a MySQL database, providing fast and efficient storage and retrieval of the embeddings.

## Requirements

To use DocuChat, you need to have an OpenAI API Key. You should edit the config.php file to include your API Key before using the application.

## Usage

Upload a PDF file (<25 pages) to start a conversation.
Enter your message in the input field and click "Send".
The application will use OpenAI embeddings to find similar documents and display a summary of the nearest neighbors.
Performance

The application is optimized for documents up to 50 pages in length (25 page limit can be removed). Longer documents may result in decreased performance.

## Installation

Clone this repository.
Edit the config.php file to include your OpenAI API Key.
Install and configure a web server with PHP and MySQL.
Upload the files to your web server.
Open the application in a web browser.

## Credits

DocuChat was developed by Yamil Ricardo Velez. It uses the OpenAI API for text embeddings.
