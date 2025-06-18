<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Detail Chat | TATA</title>
    <link rel="shortcut icon" type="image/png" href="{{ asset($tPath.'img/icon/icon.png') }}" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
        integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}" />
    <style>
        .chat-main {
            height: 100vh;
            display: flex;
            flex-direction: column;
            background: #f5f7f9;
        }

        .chat-header {
            background: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .chat-header-left {
            display: flex;
            align-items: center;
        }

        .back-button {
            text-decoration: none;
            color: #333;
            font-size: 1.1rem;
            margin-right: 15px;
            display: flex;
            align-items: center;
        }

        .back-button i {
            margin-right: 5px;
        }

        .user-info {
            display: flex;
            align-items: center;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
        }

        .user-name {
            font-weight: 500;
            color: #333;
            margin: 0;
        }

        .user-status {
            font-size: 0.8rem;
            color: #4CAF50;
        }

        .order-preview {
            background: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid #e0e0e0;
        }

        .order-preview-content {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .order-image {
            width: 50px;
            height: 50px;
            border-radius: 8px;
            object-fit: cover;
        }

        .order-details h6 {
            margin: 0 0 5px 0;
            color: #333;
        }

        .order-price {
            color: #666;
            font-size: 0.9rem;
        }

        .chat-messages {
            flex: 1;
            padding: 20px;
            overflow-y: auto;
        }

        .message {
            display: flex;
            flex-direction: column;
            margin-bottom: 20px;
            max-width: 70%;
        }

        .message-incoming {
            align-self: flex-start;
        }

        .message-outgoing {
            align-self: flex-end;
        }

        .message-content {
            padding: 12px 16px;
            border-radius: 12px;
            position: relative;
            margin-bottom: 5px;
        }

        .message-incoming .message-content {
            background: #fff;
            color: #333;
            border: 1px solid #e0e0e0;
        }

        .message-outgoing .message-content {
            background: #4CAF50;
            color: white;
        }

        .message-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 0.75rem;
        }

        .message-incoming .message-meta {
            padding-left: 4px;
        }

        .message-outgoing .message-meta {
            justify-content: flex-end;
            padding-right: 4px;
        }

        .message-time {
            color: #666;
        }

        .message-status {
            color: #4CAF50;
        }

        .chat-input {
            background: #fff;
            padding: 15px 20px;
            border-top: 1px solid #e0e0e0;
        }

        .chat-input form {
            display: flex;
            gap: 10px;
        }

        .chat-input input {
            flex: 1;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 24px;
            outline: none;
            font-size: 0.95rem;
            transition: border-color 0.3s ease;
        }

        .chat-input input:focus {
            border-color: #4CAF50;
        }

        .chat-input button {
            width: 45px;
            height: 45px;
            border: none;
            border-radius: 50%;
            background: #4CAF50;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .chat-input button:hover {
            background: #43A047;
        }

        .chat-input button i {
            font-size: 1.2rem;
        }

        /* Loading animation */
        .typing-indicator {
            display: flex;
            gap: 4px;
            padding: 8px 12px;
            background: #f0f0f0;
            border-radius: 12px;
            width: fit-content;
        }

        .typing-indicator span {
            width: 6px;
            height: 6px;
            background: #666;
            border-radius: 50%;
            animation: typing 1s infinite;
        }

        .typing-indicator span:nth-child(2) {
            animation-delay: 0.2s;
        }

        .typing-indicator span:nth-child(3) {
            animation-delay: 0.4s;
        }

        @keyframes typing {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-4px); }
        }

        .date-divider {
            text-align: center;
            margin: 20px 0;
            position: relative;
        }

        .date-divider::before,
        .date-divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 45%;
            height: 1px;
            background: #e0e0e0;
        }

        .date-divider::before {
            left: 0;
        }

        .date-divider::after {
            right: 0;
        }

        .date-text {
            background: #f5f7f9;
            padding: 0 15px;
            color: #666;
            font-size: 0.85rem;
        }
    </style>
</head>

<body>
    <div class="chat-main">
        <!-- Chat Header -->
        <div class="chat-header">
            <div class="chat-header-left">
                <a href="/chat" class="back-button">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div class="user-info">
                    <img src="{{ asset($tPath.'assets/images/profile/user-1.jpg') }}" alt="User" class="user-avatar">
                    <div>
                        <h5 class="user-name">{{ $chat->user_name ?? 'User Name' }}</h5>
                        <span class="user-status">Online</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Order Preview -->
        <div class="order-preview">
            <div class="order-preview-content">
                <img src="{{ asset($tPath.'assets/images/products/product-1.jpg') }}" alt="Product" class="order-image">
                <div class="order-details">
                    <h6>{{ $chat->order_title ?? 'Logo Design' }}</h6>
                    <span class="order-price">Rp {{ number_format($chat->order_price ?? 300000, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>

        <!-- Chat Messages -->
        <div class="chat-messages" id="chatMessages">
            <div class="date-divider">
                <span class="date-text">Today</span>
            </div>

            <!-- Messages will be loaded here dynamically -->
        </div>

        <!-- Chat Input -->
        <div class="chat-input">
            <form id="chatForm">
                <input type="text" id="messageInput" placeholder="Ketik pesan..." autocomplete="off">
                <button type="submit">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </form>
        </div>
    </div>

    <script src="{{ asset($tPath.'assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            const chatMessages = $('#chatMessages');
            const messageInput = $('#messageInput');
            const chatForm = $('#chatForm');
            const userId = '{{ $chat->user_uuid ?? "" }}';

            // Function to add a new message to the chat
            function addMessage(message, isOutgoing = false) {
                const messageHtml = `
                    <div class="message ${isOutgoing ? 'message-outgoing' : 'message-incoming'}">
                        <div class="message-content">${message}</div>
                        <div class="message-meta">
                            <span class="message-time">${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                            ${isOutgoing ? '<span class="message-status"><i class="fas fa-check"></i></span>' : ''}
                        </div>
                    </div>
                `;
                chatMessages.append(messageHtml);
                chatMessages.scrollTop(chatMessages[0].scrollHeight);
            }

            // Load initial messages
            function loadMessages() {
                // Example messages - replace with actual API call
                const messages = [
                    { content: "Halo kak, saya ingin menanyakan terkait pesanan ini, kapan diproses", isOutgoing: false },
                    { content: "Baik kak mohon ditunggu ya proses verifikasi membutuhkan waktu 1x24 jam", isOutgoing: true },
                    { content: "Baik admin, saya harap pengerjaan secepatnya ya", isOutgoing: false },
                    { content: "Baik kami memaksimalkan ya kak, terima kasih atas kesabarannya", isOutgoing: true }
                ];

                messages.forEach(msg => addMessage(msg.content, msg.isOutgoing));
            }

            // Handle form submission
            chatForm.on('submit', function(e) {
                e.preventDefault();
                const message = messageInput.val().trim();
                if (message) {
                    // Add message to chat
                    addMessage(message, true);
                    // Clear input
                    messageInput.val('');
                    
                    // Here you would typically send the message to your backend
                    // $.post('/api/chat/send', {
                    //     user_id: userId,
                    //     message: message
                    // });
                }
            });

            // Load initial messages
            loadMessages();

            // Optional: Add real-time updates using WebSocket or polling
            // function pollMessages() {
            //     $.get(`/api/chat/${userId}/messages`, function(data) {
            //         // Update messages
            //     });
            // }
            // setInterval(pollMessages, 3000);
        });
    </script>
</body>

</html> 