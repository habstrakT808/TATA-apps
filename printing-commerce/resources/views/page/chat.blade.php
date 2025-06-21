<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Chat | TATA</title>
    <link href="{{ asset($tPath.'assets2/img/logo.png') }}" rel="icon">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="{{ asset($tPath.'assets/css/styles.min.css') }}" />
    <link rel="stylesheet" href="{{ asset($tPath.'css/preloader.css') }}" />
    <!-- CSS for full calender -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css"/>
    <style>
    body {
        background-color: #F6F9FF;
        margin: 0;
        padding: 0;
        height: 100vh;
        overflow: hidden;
    }

    img {
        pointer-events: none;
    }
    
    a:hover {
        text-decoration: none;
    }
    
    .chat-wrapper {
        height: 100vh;
        display: flex;
        flex-direction: column;
    }
    
    .chat-header {
        background: #fff;
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
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
    
    .chat-title {
        font-size: 1.5rem;
        margin: 0;
        color: #333;
    }
    
    .chat-container {
        flex: 1;
        display: flex;
        background: #fff;
        margin: 0;
        height: calc(100vh - 60px);
    }
    
    .chat-sidebar {
        width: 350px;
        border-right: 1px solid #e0e0e0;
        height: 100%;
        overflow-y: auto;
        background: #fff;
    }
    
    .chat-search {
        padding: 15px;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .chat-search input {
        width: 100%;
        padding: 10px 15px;
        border: 1px solid #ddd;
        border-radius: 20px;
        outline: none;
        font-size: 0.9rem;
    }
    
    .chat-list {
        padding: 0;
        margin: 0;
        list-style: none;
    }
    
    .chat-item {
        padding: 15px 20px;
        border-bottom: 1px solid #e0e0e0;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .chat-item:hover {
        background: #f8f9fa;
    }
    
    .chat-item.active {
        background: #e3f2fd;
    }
    
    .chat-item-content {
        display: flex;
        align-items: center;
    }
    
    .user-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        margin-right: 15px;
        object-fit: cover;
    }
    
    .chat-item-info {
        flex: 1;
    }
    
    .chat-item-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
    }
    
    .chat-item-name {
        font-weight: 500;
        color: #333;
        font-size: 1rem;
    }
    
    .chat-item-date {
        font-size: 0.8rem;
        color: #666;
    }
    
    .chat-item-preview {
        color: #666;
        font-size: 0.9rem;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    
    .chat-main {
        flex: 1;
        display: flex;
        flex-direction: column;
        background: #f5f7f9;
    }
    
    .chat-main-header {
        padding: 15px 20px;
        background: #ffffff;
        border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        color: #2A3547;
        display: flex;
        align-items: center;
    }
    
    .chat-main-header img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-right: 15px;
        object-fit: cover;
    }
    
    .chat-main-header-info h4 {
        margin: 0;
        font-size: 16px;
        color: #2A3547;
    }
    
    .chat-main-header-info p {
        margin: 0;
        font-size: 13px;
        color: #5A6A85;
    }
    
    .chat-messages {
        flex: 1;
        padding: 20px;
        overflow-y: auto;
        background: #f6f9ff;
    }
    
    .message {
        margin-bottom: 20px;
        max-width: 75%;
        display: flex;
        flex-direction: column;
    }
    
    .message-incoming {
        margin-right: auto;
    }
    
    .message-outgoing {
        margin-left: auto;
        align-items: flex-end;
    }
    
    .message-content {
        padding: 12px 16px;
        border-radius: 12px;
        position: relative;
        box-shadow: rgba(145, 158, 171, 0.2) 0px 0px 2px 0px, rgba(145, 158, 171, 0.12) 0px 12px 24px -4px;
    }
    
    .message-incoming .message-content {
        background: #ffffff;
        color: #2A3547;
        border-bottom-left-radius: 4px;
    }
    
    .message-outgoing .message-content {
        background: #5D87FF;
        color: #ffffff;
        border-bottom-right-radius: 4px;
    }
    
    .message-time {
        font-size: 11px;
        margin-top: 5px;
        opacity: 0.7;
    }
    
    .message-incoming .message-time {
        color: #5A6A85;
    }
    
    .message-outgoing .message-time {
        color: #ffffff;
    }
    
    .chat-input {
        padding: 15px 20px;
        background: #ffffff;
        border-top: 1px solid #e0e0e0;
        display: flex;
        align-items: center;
    }
    
    .chat-input input {
        flex: 1;
        border: none;
        outline: none;
        padding: 10px 15px;
        border-radius: 20px;
        background: #f5f7f9;
        font-size: 14px;
    }
    
    .chat-input button {
        background: #5D87FF;
        color: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        margin-left: 10px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .chat-input button:hover {
        background: #4D77FF;
    }
    
    .chat-empty-state {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
        color: #666;
        padding: 20px;
        text-align: center;
    }
    
    .chat-empty-state i {
        font-size: 4rem;
        color: #ccc;
        margin-bottom: 20px;
    }
    
    .chat-empty-state h3 {
        margin: 0 0 10px 0;
        color: #333;
    }
    
    .chat-empty-state p {
        margin: 0;
        color: #666;
    }
    
    .read-status {
        font-size: 11px;
        color: #5A6A85;
        margin-top: 2px;
        text-align: right;
    }
    
    .order-preview {
        background: white;
        border-radius: 10px;
        padding: 15px;
        margin-bottom: 20px;
        display: inline-flex;
        align-items: center;
        gap: 15px;
        box-shadow: rgba(145, 158, 171, 0.2) 0px 0px 2px 0px, rgba(145, 158, 171, 0.12) 0px 12px 24px -4px;
        width: auto;
        max-width: 300px;
    }
    
    .order-preview img {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        object-fit: cover;
    }
    
    .order-info h6 {
        margin: 0;
        font-size: 14px;
        color: #2A3547;
        font-weight: 500;
    }
    
    .order-info p {
        margin: 5px 0 0;
        font-size: 13px;
        color: #5A6A85;
    }
    
    /* Status badge for messages */
    .status-badge {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 12px;
        display: inline-block;
        margin-left: 8px;
    }
    
    .status-badge.read {
        background-color: #e3f2fd;
        color: #1976d2;
    }
    
    .status-badge.delivered {
        background-color: #e8f5e9;
        color: #388e3c;
    }
    
    /* Styling untuk pesanan dalam chat */
    .order-card {
        background-color: #fff;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        width: 100%;
        max-width: 350px;
    }
    
    .order-card-logo {
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f0f4ff;
        border-radius: 8px;
        margin-right: 16px;
    }
    
    .order-card-info {
        flex: 1;
    }
    
    .order-card-info h6 {
        margin: 0 0 4px;
        font-size: 14px;
        font-weight: 600;
    }
    
    .order-card-info p {
        margin: 0;
        font-size: 16px;
        font-weight: 700;
        color: #333;
    }
    
    .dibaca-tag {
        font-size: 12px;
        color: #999;
        text-align: right;
        margin-top: 4px;
    }
    </style>
</head>

<body>
    @if(app()->environment('local'))
    <script>
    var tPath = '';
    </script>
    @else
    <script>
    var tPath = '';
    </script>
    @endif
    <script>
    const domain = window.location.protocol + '//' + window.location.hostname + ":" + window.location.port;
    var csrfToken = "{{ csrf_token() }}";
    var userAuth = @json($userAuth ?? []);
    </script>

    <div class="chat-wrapper">
        <!-- Chat Header -->
        <div class="chat-header">
            <a href="/dashboard" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <h1 class="chat-title">Kotak Pesan</h1>
        </div>

        <!-- Main Chat Container -->
        <div class="chat-container">
            <!-- Chat Sidebar -->
            <div class="chat-sidebar">
                <!-- Search Bar -->
                <div class="chat-search">
                    <input type="text" placeholder="Cari percakapan..." id="search-chat">
                </div>

                <!-- Chat List -->
                <ul class="chat-list" id="chat-list">
                    <!-- Chat items will be dynamically loaded here -->
                    <!-- Example item for reference -->
                    <li class="chat-item" style="display: none;">
                        <div class="chat-item-content">
                            <img src="{{ asset($tPath.'assets/images/profile/user-1.jpg') }}" alt="User" class="user-avatar">
                            <div class="chat-item-info">
                                <div class="chat-item-header">
                                    <span class="chat-item-name">Emma Myers</span>
                                    <span class="chat-item-date">20.09.2025</span>
                                </div>
                                <div class="chat-item-preview">Logo Design - Rp 300.000</div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Chat Main Area -->
            <div class="chat-main" id="chat-main">
                <!-- Default Empty State -->
                <div class="chat-empty-state" id="empty-state">
                    <i class="fas fa-comments"></i>
                    <h3>Pilih percakapan</h3>
                    <p>Silahkan pilih percakapan dari daftar untuk mulai chat</p>
                </div>

                <!-- Chat area akan muncul saat user dipilih -->
                <div class="chat-area" id="chat-area" style="display: none; flex-direction: column; height: 100%;">
                    <div class="chat-main-header" id="chat-main-header">
                        <!-- User info akan diisi di sini -->
                    </div>
                    
                    <div class="chat-messages" id="chat-messages">
                        <!-- Pesan akan ditampilkan di sini -->
                    </div>
                    
                    <div class="chat-input">
                        <input type="text" placeholder="Ketik pesan Anda..." id="message-input">
                        <button id="send-button">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @include('components.preloader')
    
    <!-- JS for jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.20.1/moment.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
    <script src="{{ asset($tPath.'assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/js/app.min.js') }}"></script>
    
    <!-- Firebase for real-time chat -->
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-firestore.js"></script>
    
    <script>
    // Firebase config (optional - untuk real-time jika dibutuhkan)
    const firebaseConfig = {
        apiKey: "{{ env('FIREBASE_API_KEY', '') }}",
        authDomain: "{{ env('FIREBASE_AUTH_DOMAIN', '') }}",
        projectId: "{{ env('FIREBASE_PROJECT_ID', '') }}",
        storageBucket: "{{ env('FIREBASE_STORAGE_BUCKET', '') }}",
        messagingSenderId: "{{ env('FIREBASE_MESSAGING_SENDER_ID', '') }}",
        appId: "{{ env('FIREBASE_APP_ID', '') }}"
    };
    
    // Initialize Firebase if configured
    let db;
    try {
        if (firebaseConfig.apiKey) {
            firebase.initializeApp(firebaseConfig);
            db = firebase.firestore();
            console.log('Firebase initialized successfully');
        } else {
            console.log('Firebase not configured, using regular polling instead');
        }
    } catch (e) {
        console.error('Firebase initialization error:', e);
    }
    
    // Referensi elemen DOM
    const chatList = document.getElementById('chat-list');
    const chatArea = document.getElementById('chat-area');
    const emptyState = document.getElementById('empty-state');
    const chatMessages = document.getElementById('chat-messages');
    const chatMainHeader = document.getElementById('chat-main-header');
    const messageInput = document.getElementById('message-input');
    const sendButton = document.getElementById('send-button');
    const searchInput = document.getElementById('search-chat');
    
    // Status user yang dipilih
    let selectedUserId = null;
    let selectedChatId = null;
    let messagePollingInterval = null;
    
    // Fungsi untuk memuat daftar chat
    function loadChatList() {
        console.log('Loading chat list...');
        console.log('CSRF Token:', csrfToken);
        console.log('Request URL:', '/chat/chats');
        
        // API call ke Laravel untuk mendapatkan daftar chat
        $.ajax({
            url: '/chat/chats',
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'Content-Type': 'application/json'
            },
            success: function(response) {
                console.log('Chat list response SUCCESS:', response);
                if (response.success) {
                    console.log('Number of chats received:', response.data.length);
                    renderChatList(response.data);
                } else {
                    console.error('Failed to load chat list:', response.message);
                    chatList.innerHTML = '<li class="chat-item"><div class="chat-item-content">Tidak ada percakapan</div></li>';
                }
            },
            error: function(xhr, status, error) {
                console.error('Chat list request FAILED');
                console.error('Status:', xhr.status);
                console.error('Status Text:', xhr.statusText);
                console.error('Response Text:', xhr.responseText);
                console.error('Error:', error);
                
                let errorMessage = 'Gagal memuat percakapan';
                try {
                    const errorResponse = JSON.parse(xhr.responseText);
                    errorMessage = errorResponse.message || errorMessage;
                } catch (e) {
                    console.error('Failed to parse error response');
                }
                
                chatList.innerHTML = `<li class="chat-item"><div class="chat-item-content">${errorMessage}</div></li>`;
            }
        });
    }
    
    // Render daftar chat
    function renderChatList(chats) {
        console.log('Rendering chat list with data:', chats);
        chatList.innerHTML = '';
        
        if (chats.length === 0) {
            console.log('No chats to display');
            chatList.innerHTML = '<li class="chat-item"><div class="chat-item-content">Tidak ada percakapan</div></li>';
            return;
        }
        
        chats.forEach((chat, index) => {
            console.log(`Rendering chat ${index + 1}:`, chat);
            
            // Pastikan data user tersedia
            const user = chat.user || { nama_user: 'Pengguna', profile_picture: null };
            
            const li = document.createElement('li');
            li.className = 'chat-item';
            li.setAttribute('data-chat-id', chat.uuid);
            li.setAttribute('data-user-id', user.id_user || chat.user_id);
            
            // PERBAIKAN: Path gambar yang benar
            const profilePicture = user.profile_picture 
                ? `${domain}/storage/profile_pictures/${user.profile_picture}` 
                : `${domain}/assets/images/profile/user-1.jpg`; // ← GUNAKAN PATH YANG ADA
            
            li.innerHTML = `
                <div class="chat-item-content">
                    <img src="${profilePicture}" alt="${user.nama_user}" class="user-avatar" 
                         onerror="this.src='${domain}/assets/images/profile/user-1.jpg'">
                    <div class="chat-item-info">
                        <div class="chat-item-header">
                            <span class="chat-item-name">${user.nama_user}</span>
                            <span class="chat-item-date">${formatDate(chat.updated_at)}</span>
                        </div>
                        <div class="chat-item-preview">${chat.last_message || 'Belum ada pesan'}</div>
                    </div>
                </div>
            `;
            
            li.addEventListener('click', function() {
                console.log('Chat item clicked:', chat.uuid);
                
                // Hapus kelas active dari semua item
                document.querySelectorAll('.chat-item').forEach(item => {
                    item.classList.remove('active');
                });
                
                // Tambahkan kelas active ke item yang dipilih
                li.classList.add('active');
                
                // Tampilkan area chat
                selectedChatId = chat.uuid;
                selectedUserId = user.id_user || chat.user_id;
                showChatArea(chat);
            });
            
            chatList.appendChild(li);
            console.log(`Chat ${index + 1} rendered successfully`);
        });
        
        console.log('All chats rendered successfully');
    }
    
    // Tampilkan area chat
    function showChatArea(chat) {
        emptyState.style.display = 'none';
        chatArea.style.display = 'flex';
        
        // Pastikan data user tersedia
        const user = chat.user || { nama_user: 'Pengguna', profile_picture: null };
        const pesanan = chat.pesanan || null;
        
        // PERBAIKAN: Path gambar yang benar
        const profilePicture = user.profile_picture 
            ? `${domain}/storage/profile_pictures/${user.profile_picture}` 
            : `${domain}/assets/images/profile/user-1.jpg`; // ← GUNAKAN PATH YANG ADA
        
        // Tampilkan header dengan info user
        chatMainHeader.innerHTML = `
            <img src="${profilePicture}" alt="${user.nama_user}" 
                 onerror="this.src='${domain}/assets/images/profile/user-1.jpg'">
            <div class="chat-main-header-info">
                <h4>${user.nama_user}</h4>
                <p>${pesanan ? `Pesanan #${pesanan.uuid}` : 'Chat Umum'}</p>
            </div>
        `;
        
        // Muat pesan untuk chat ini
        loadMessages(chat.uuid);
        
        // Tandai pesan sebagai sudah dibaca
        markMessagesAsRead(chat.uuid);
        
        // Set polling interval untuk memeriksa pesan baru
        if (messagePollingInterval) {
            clearInterval(messagePollingInterval);
        }
        
        messagePollingInterval = setInterval(() => {
            if (selectedChatId) {
                loadMessages(selectedChatId);
            }
        }, 5000); // Check every 5 seconds
    }
    
    // Muat pesan dari chat tertentu
    function loadMessages(chatUuid) {
        // Jika menggunakan Firebase, gunakan listener
        if (typeof db !== 'undefined' && db) {
            chatMessages.innerHTML = '';
            
            // Tambahkan listener untuk pesan real-time
            const messagesRef = db.collection('chat_messages')
                .where('chat_uuid', '==', chatUuid)
                .orderBy('created_at', 'asc');
            
            messagesRef.onSnapshot(snapshot => {
                let changes = snapshot.docChanges();
                
                changes.forEach(change => {
                    if (change.type === 'added') {
                        renderMessage(change.doc.data(), change.doc.id);
                    }
                });
                
                // Scroll ke pesan terbaru
                chatMessages.scrollTop = chatMessages.scrollHeight;
            });
        } else {
            // Jika tidak menggunakan Firebase, gunakan polling regular
            $.ajax({
                url: '/chat/messages',
                method: 'GET',
                data: {
                    chat_uuid: chatUuid
                },
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                },
                success: function(response) {
                    if (response.success) {
                        chatMessages.innerHTML = '';
                        response.data.forEach(message => {
                            renderMessage(message, message.id);
                        });
                        
                        // Scroll ke pesan terbaru
                        chatMessages.scrollTop = chatMessages.scrollHeight;
                    } else {
                        console.error('Failed to load messages:', response.message);
                    }
                },
                error: function(xhr) {
                    console.error('Error loading messages:', xhr.responseText);
                }
            });
        }
    }
    
    // Render pesan
    function renderMessage(message, messageId) {
        const isAdmin = message.sender_type === 'admin';
        const messageClass = isAdmin ? 'message-outgoing' : 'message-incoming';
        
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${messageClass}`;
        messageDiv.setAttribute('data-message-id', messageId);
        
        let messageContent = '';
        
        // Render pesanan jika ada
        if (message.message_type === 'order' && message.pesanan) {
            const pesanan = message.pesanan;
            messageContent = `
                <div class="order-card">
                    <div class="order-card-logo">
                        <img src="/assets3/img/jasa/${pesanan.jasa.kategori}/logo.png" alt="Logo">
                    </div>
                    <div class="order-card-info">
                        <h6>${pesanan.jasa.nama || 'Logo Design'}</h6>
                        <p>Rp ${formatNumber(pesanan.total_harga || 300000)}</p>
                    </div>
                </div>
                <div class="message-content">
                    ${message.message}
                    <div class="message-time">${formatTime(message.created_at)}</div>
                </div>
            `;
        } else if (message.message_type === 'image' || message.file_url) {
            messageContent = `
                <div class="message-content">
                    <img src="${message.file_url}" alt="Shared image" style="max-width: 200px; border-radius: 8px; margin-bottom: 8px;">
                    ${message.message ? `<div>${message.message}</div>` : ''}
                    <div class="message-time">${formatTime(message.created_at)}</div>
                </div>
            `;
        } else {
            messageContent = `
                <div class="message-content">
                    ${message.message}
                    <div class="message-time">${formatTime(message.created_at)}</div>
                </div>
            `;
        }
        
        messageDiv.innerHTML = messageContent;
        
        // Tambahkan status dibaca untuk pesan keluar
        if (isAdmin && message.is_read) {
            const readStatus = document.createElement('div');
            readStatus.className = 'dibaca-tag';
            readStatus.textContent = 'Dibaca';
            messageDiv.appendChild(readStatus);
        }
        
        chatMessages.appendChild(messageDiv);
    }
    
    // Tandai pesan sebagai sudah dibaca
    function markMessagesAsRead(chatUuid) {
        $.ajax({
            url: '/chat/mark-read',
            method: 'POST',
            data: {
                chat_uuid: chatUuid
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                console.log('Messages marked as read');
            },
            error: function(xhr) {
                console.error('Error marking messages as read:', xhr.responseText);
            }
        });
    }
    
    // Kirim pesan
    function sendMessage() {
        const message = messageInput.value.trim();
        
        if (!message || !selectedChatId) return;
        
        // Simpan pesan melalui Laravel API
        $.ajax({
            url: '/chat/send',
            method: 'POST',
            data: {
                chat_uuid: selectedChatId,
                message: message,
                message_type: 'text'
            },
            headers: {
                'X-CSRF-TOKEN': csrfToken
            },
            success: function(response) {
                if (response.success) {
                    console.log('Message sent successfully');
                    messageInput.value = '';
                    
                    // Jika tidak menggunakan Firebase, reload pesan segera
                    if (typeof db === 'undefined' || !db) {
                        loadMessages(selectedChatId);
                    }
                } else {
                    console.error('Failed to send message:', response.message);
                }
            },
            error: function(xhr) {
                console.error('Error sending message:', xhr.responseText);
            }
        });
    }
    
    // Event listener untuk tombol kirim
    sendButton.addEventListener('click', sendMessage);
    
    // Event listener untuk input (enter untuk kirim)
    messageInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            sendMessage();
        }
    });
    
    // Event listener untuk pencarian
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        
        document.querySelectorAll('.chat-item').forEach(item => {
            const name = item.querySelector('.chat-item-name').textContent.toLowerCase();
            const preview = item.querySelector('.chat-item-preview').textContent.toLowerCase();
            
            if (name.includes(searchTerm) || preview.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Helper: Format tanggal
    function formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric'
        });
    }
    
    // Helper: Format waktu
    function formatTime(timestamp) {
        let date;
        
        if (timestamp instanceof Date) {
            date = timestamp;
        } else if (timestamp && timestamp.seconds) {
            // Firebase Timestamp
            date = new Date(timestamp.seconds * 1000);
        } else {
            date = new Date(timestamp);
        }
        
        return date.toLocaleTimeString('id-ID', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }
    
    // Helper: Format number to IDR
    function formatNumber(number) {
        return new Intl.NumberFormat('id-ID').format(number);
    }
    
    // Muat daftar chat saat halaman dimuat
    document.addEventListener('DOMContentLoaded', function() {
        loadChatList();
    });
    </script>
</body>

</html>