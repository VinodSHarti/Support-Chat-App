package main

import (
    "database/sql"
    "encoding/json"
    "log"
    "net/http"
    "time"

    _ "github.com/go-sql-driver/mysql"
    "github.com/gorilla/websocket"
)

var (
    clients  = make(map[*websocket.Conn]bool)
    upgrader = websocket.Upgrader{
        CheckOrigin: func(r *http.Request) bool {
            return true
        },
    }
    db *sql.DB
)

type Message struct {
    MessageID      int       `json:"message_id"`
    ConversationID int       `json:"conversation_id"`
    SenderID       int       `json:"sender_id"`
    Content        string    `json:"content"`
    CreatedAtStr   string    `json:"created_at"`
}

func handleWS(w http.ResponseWriter, r *http.Request) {
    ws, err := upgrader.Upgrade(w, r, nil)
    if err != nil {
        log.Println("WebSocket upgrade error:", err)
        return
    }
    defer ws.Close()
    clients[ws] = true
    log.Println("Client connected")

    for {
        if _, _, err := ws.ReadMessage(); err != nil {
            log.Println("Client disconnected")
            delete(clients, ws)
            break
        }
    }
}

func handlePushMessage(w http.ResponseWriter, r *http.Request) {
    var msg Message

    err := json.NewDecoder(r.Body).Decode(&msg)
    if err != nil {
        log.Println("❌ JSON decode error:", err)
        http.Error(w, "Invalid JSON", http.StatusBadRequest)
        return
    }

    log.Printf("✅ Received push message: %+v\n", msg)

    createdAt, err := time.Parse("2006-01-02 15:04:05", msg.CreatedAtStr)
    if err != nil {
        log.Println("⏱️ Time parse error:", err)
        createdAt = time.Now()
    }

    // Insert into messages_outboxes table
    _, err = db.Exec("INSERT INTO messages_outboxes (message_id, delivered, created_at) VALUES (?, false, ?)", msg.MessageID, createdAt)
    if err != nil {
        log.Println("❌ DB insert error:", err)
        http.Error(w, "DB insert error", http.StatusInternalServerError)
        return
    }

    // Broadcast to all connected clients
    msgJSON, _ := json.Marshal(msg)
    for client := range clients {
        err := client.WriteMessage(websocket.TextMessage, msgJSON)
        if err != nil {
            log.Println("WebSocket write error:", err)
            client.Close()
            delete(clients, client)
        }
    }

    w.WriteHeader(http.StatusOK)
}


func main() {
    var err error
    dsn := "root:@tcp(127.0.0.1:3306)/support_chat_app?parseTime=true"
    db, err = sql.Open("mysql", dsn)
    if err != nil {
        log.Fatal("Database connection error:", err)
    }
    defer db.Close()

    http.HandleFunc("/ws", handleWS)
    http.HandleFunc("/push_message", handlePushMessage)

    log.Println("Go WebSocket server started on :8080")
    log.Fatal(http.ListenAndServe(":8080", nil))
}
