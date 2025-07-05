Support Chat Application Setup Laravel + GoLang(Go)
==============================

Prerequisites
-------------
- PHP >= 8.x
- Composer
- Go (1.18+ recommended)
- MySQL or compatible database
- Laravel CLI (artisan)

1. Install Composer Dependencies
--------------------------------
Run this in your Laravel project directory:

    composer install

2. Migrate Tables
--------------------------
Run this command in your project directory
    
    php artisan migrate

3. Create Users (Example)
--------------------------
use seeders:
    php artisan db:seed --class=UserSeeder

4. Run Laravel Server
---------------------
Start the Laravel development server:

    php artisan serve

Default Laravel URL:

    http://127.0.0.1:8000

5. API Endpoints
----------------
    1) Start Conversation:
        POST /api/conversations
        header: X-API-TOKEN=token(customer token)

    2) Conversation List
        GET /api/conversations
        header: X-API-TOKEN=token(customer/Agent)
    
    3) Send Message
        POST api/conversations/{conversation_id}/messages
        header: {
            X-API-TOKEN=token(customer/Agent)
            Accept: application/json
        }
        body/params : {
            X-API-TOKEN=token(receiver token)
            Content-Type: application/json
            content: Your Query

        } 
    
    4) Receive Messages
        GET /api/conversations/{conversation_id}/messages
        X-API-TOKEN=token(customer/Agent)


6. Install Go and Run WebSocket Server
--------------------------------------

Install Go: https://go.dev/dl/

To run the Go server, navigate to the Go app folder (e.g., `/go`) and run:

    go run push_message.go

This will start the server at:

    http://localhost:8080

6. WebSocket Test Page (Optional)
---------------------------------
Create a test HTML page to connect to WebSocket:

    ws://localhost:8080/ws

Use JavaScript WebSocket API to send/receive messages.

7. Laravel Environment Config
-----------------------------
Update `.env` in Laravel project to notify Go server:

    GO_SERVER_URL=http://localhost:8080/push_message

8. Confirm Go Receives Messages
-------------------------------
When Laravel sends a message, it triggers a POST to `/push_message`, Go logs the payload, saves it to `messages_outboxes`, and broadcasts to WebSocket clients.

Make sure:
- Go server is running before sending messages.
- WebSocket client is connected to receive broadcast.

9. Database Config for Go
--------------------------
In Go, use correct MySQL DSN:

    root:@tcp(127.0.0.1:3306)/support_chat_app?parseTime=true

10. Logs and Debugging
-----------------------
- Laravel logs: `storage/logs/laravel.log`
- Go server logs to console (stdout)

Done!
-----
You now have a working chat system where Laravel handles messages and Go broadcasts them in real time.
"""

with open("SupportChatSetup.txt", "w") as f:
    f.write(readme_text)
