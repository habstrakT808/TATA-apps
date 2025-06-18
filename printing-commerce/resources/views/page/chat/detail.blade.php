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
        body {
            background-color: #F6F9FF;
            margin: 0;
            padding: 0;
            height: 100vh;
            overflow: hidden;
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
            display: flex;
            align-items: center;
            margin-right: 15px;
            font-size: 16px;
        }

        .back-button i {
            margin-right: 8px;
        }

        .chat-title {
            font-size: 24px;
            margin: 0;
            color: #333;
        }

        .chat-container {
            display: flex;
            height: calc(100vh - 60px);
        }

        .chat-sidebar {
            width: 300px;
            background: #fff;
            border-right: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
        }

        .chat-search {
            padding: 15px;
            border-bottom: 1px solid #e0e0e0;
        }

        .chat-search input {
            width: 100%;
            padding: 8px 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
        }

        .chat-list {
            flex: 1;
            overflow-y: auto;
        }

        .chat-item {
            padding: 15px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e0e0e0;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .chat-item:hover {
            background-color: #f5f5f5;
        }

        .chat-item.active {
            background-color: #e9ecef;
        }

        .chat-item-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 15px;
        }

        .chat-item-info {
            flex: 1;
        }

        .chat-item-name {
            font-weight: 500;
            margin-bottom: 3px;
        }

        .chat-item-preview {
            font-size: 13px;
            color: #666;
        }

        .chat-item-time {
            font-size: 12px;
            color: #999;
        }

        .chat-main {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #ffffff;
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

        .message-image {
            max-width: 300px;
            border-radius: 12px;
            overflow: hidden;
            margin-bottom: 5px;
        }

        .message-image img {
            width: 100%;
            height: auto;
            display: block;
        }

        .message-time {
            font-size: 12px;
            margin-top: 5px;
            color: #5A6A85;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .message-outgoing .message-time {
            color: #5D87FF;
        }

        .chat-input {
            padding: 20px;
            background: #ffffff;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        .chat-input-wrapper {
            display: flex;
            gap: 10px;
            align-items: flex-end;
        }

        .chat-input form {
            display: flex;
            gap: 10px;
            width: 100%;
        }

        .input-group {
            flex: 1;
            position: relative;
        }

        .chat-input input[type="text"] {
            width: 100%;
            padding: 12px 45px 12px 15px;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            outline: none;
            font-size: 14px;
            color: #2A3547;
            background: #ffffff;
            transition: all 0.2s;
        }

        .chat-input input[type="text"]:focus {
            border-color: #5D87FF;
            box-shadow: 0 0 0 2px rgba(93, 135, 255, 0.1);
        }

        .chat-input .attach-btn {
            position: absolute;
            right: 10px;
            bottom: 8px;
            background: none;
            border: none;
            color: #5A6A85;
            cursor: pointer;
            padding: 5px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .chat-input .attach-btn:hover {
            color: #5D87FF;
        }

        .chat-input button[type="submit"] {
            padding: 12px;
            width: 45px;
            height: 45px;
            border: none;
            border-radius: 10px;
            background: #5D87FF;
            color: white;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .chat-input button[type="submit"]:hover {
            background: #4570EA;
        }

        .chat-input input[type="file"] {
            display: none;
        }

        /* Image preview */
        .image-preview {
            max-width: 200px;
            margin: 10px 0;
            position: relative;
            display: none;
        }

        .image-preview img {
            width: 100%;
            height: auto;
            border-radius: 8px;
        }

        .image-preview .remove-image {
            position: absolute;
            top: -8px;
            right: -8px;
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #5A6A85;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .image-preview .remove-image:hover {
            background: #f5f5f5;
            color: #dc3545;
        }
    </style>
</head>

<body>
    <div class="chat-wrapper">
        <!-- Main Header -->
        <div class="chat-header">
            <a href="/chat" class="back-button">
                <i class="fas fa-arrow-left"></i>
                Kembali
            </a>
            <h1 class="chat-title">Kotak Pesan</h1>
        </div>

        <!-- Main Container -->
        <div class="chat-container">
            <!-- Left Sidebar -->
            <div class="chat-sidebar">
                <div class="chat-search">
                    <input type="text" placeholder="Cari percakapan...">
                </div>
                <div class="chat-list">
                    <div class="chat-item active">
                        <img src="{{ asset($tPath.'assets/images/profile/user-1.jpg') }}" alt="Emma Myers" class="chat-item-avatar">
                        <div class="chat-item-info">
                            <div class="chat-item-name">Emma Myers</div>
                            <div class="chat-item-preview">Logo Design - Rp 300.000</div>
                            <div class="chat-item-time">20.08.2025</div>
                        </div>
                    </div>
                    <div class="chat-item">
                        <img src="{{ asset($tPath.'assets/images/profile/user-2.jpg') }}" alt="Fufubaba Fufubini" class="chat-item-avatar">
                        <div class="chat-item-info">
                            <div class="chat-item-name">Fufubaba Fufubini</div>
                            <div class="chat-item-preview">Website Design - Rp 500.000</div>
                            <div class="chat-item-time">20.08.2025</div>
                        </div>
                    </div>
                    <div class="chat-item">
                        <img src="{{ asset($tPath.'assets/images/profile/user-3.jpg') }}" alt="Watson Kalimasada" class="chat-item-avatar">
                        <div class="chat-item-info">
                            <div class="chat-item-name">Watson Kalimasada</div>
                            <div class="chat-item-preview">Mobile App Design - Rp 800.000</div>
                            <div class="chat-item-time">20.08.2025</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Chat Area -->
            <div class="chat-main">
                <!-- Chat Header -->
                <div class="chat-main-header">
                    <img src="{{ asset($tPath.'assets/images/profile/user-1.jpg') }}" alt="Emma Myers">
                    <div class="chat-main-header-info">
                        <h4>Emma Myers</h4>
                        <p>20.08.2025</p>
                    </div>
                </div>

                <!-- Chat Messages -->
                <div class="chat-messages">
                    <!-- Order Preview -->
                    <div class="order-preview">
                        <img src="{{ asset($tPath.'assets/images/products/product-1.jpg') }}" alt="Logo Design">
                        <div class="order-info">
                            <h6>Logo Design</h6>
                            <p>Rp 300.000</p>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div class="message message-incoming">
                        <div class="message-content">
                            Halo kak, saya ingin menanyakan terkait pesanan ini, kapan diproses
                        </div>
                        <div class="message-time">
                            <span>12:30</span>
                        </div>
                    </div>

                    <div class="message message-outgoing">
                        <div class="message-content">
                            Baik kak mohon ditunggu ya proses verifikasi membutuhkan waktu 1x24 jam
                        </div>
                        <div class="message-time">
                            <span>12:35</span>
                            <i class="fas fa-check-double text-success"></i>
                        </div>
                    </div>

                    <!-- Example Image Message (Incoming) -->
                    <div class="message message-incoming">
                        <div class="message-content">
                            <div class="message-image">
                                <img src="{{ asset($tPath.'assets/images/products/product-1.jpg') }}" alt="Sent image">
                            </div>
                            <div>Ini referensi logo yang saya inginkan</div>
                        </div>
                        <div class="message-time">
                            <span>12:37</span>
                        </div>
                    </div>

                    <!-- Example Image Message (Outgoing) -->
                    <div class="message message-outgoing">
                        <div class="message-content">
                            <div class="message-image">
                                <img src="{{ asset($tPath.'assets/images/products/product-2.jpg') }}" alt="Received image">
                            </div>
                            <div>Baik, ini draft logo yang sudah kami buat</div>
                        </div>
                        <div class="message-time">
                            <span>12:40</span>
                            <i class="fas fa-check-double text-success"></i>
                        </div>
                    </div>

                    <div class="message message-incoming">
                        <div class="message-content">
                            Baik admin, saya harap pengerjaan secepatnya ya
                        </div>
                        <div class="message-time">
                            <span>12:42</span>
                        </div>
                    </div>

                    <div class="message message-outgoing">
                        <div class="message-content">
                            Baik kami memaksimalkan ya kak, terima kasih atas kesabarannya
                        </div>
                        <div class="message-time">
                            <span>12:45</span>
                            <i class="fas fa-check-double text-success"></i>
                        </div>
                    </div>
                </div>

                <!-- Chat Input -->
                <div class="chat-input">
                    <form id="chatForm">
                        <div class="image-preview" id="imagePreview">
                            <img src="" alt="Preview">
                            <div class="remove-image">
                                <i class="fas fa-times"></i>
                            </div>
                        </div>
                        <div class="chat-input-wrapper">
                            <div class="input-group">
                                <input type="text" placeholder="Ketik pesan..." autocomplete="off">
                                <button type="button" class="attach-btn" onclick="document.getElementById('imageInput').click()">
                                    <i class="fas fa-image"></i>
                                </button>
                                <input type="file" id="imageInput" accept="image/*">
                            </div>
                            <button type="submit">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset($tPath.'assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Image upload preview
            $('#imageInput').on('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        $('#imagePreview img').attr('src', e.target.result);
                        $('#imagePreview').show();
                    }
                    reader.readAsDataURL(file);
                }
            });

            // Remove image preview
            $('.remove-image').click(function() {
                $('#imagePreview').hide();
                $('#imagePreview img').attr('src', '');
                $('#imageInput').val('');
            });

            // Handle message submission
            $('#chatForm').on('submit', function(e) {
                e.preventDefault();
                const input = $(this).find('input[type="text"]');
                const message = input.val().trim();
                const imageFile = $('#imageInput')[0].files[0];
                
                if (message || imageFile) {
                    let messageHtml = `
                        <div class="message message-outgoing">
                            <div class="message-content">
                    `;

                    if (imageFile) {
                        const imageUrl = URL.createObjectURL(imageFile);
                        messageHtml += `
                            <div class="message-image">
                                <img src="${imageUrl}" alt="Sent image">
                            </div>
                        `;
                    }

                    if (message) {
                        messageHtml += `<div>${message}</div>`;
                    }

                    messageHtml += `
                            </div>
                            <div class="message-time">
                                <span>${new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' })}</span>
                                <i class="fas fa-check-double text-success"></i>
                            </div>
                        </div>
                    `;

                    $('.chat-messages').append(messageHtml);
                    
                    // Clear input
                    input.val('');
                    $('#imagePreview').hide();
                    $('#imagePreview img').attr('src', '');
                    $('#imageInput').val('');
                    
                    // Scroll to bottom
                    $('.chat-messages').scrollTop($('.chat-messages')[0].scrollHeight);
                }
            });

            // Handle chat item selection
            $('.chat-item').click(function() {
                $('.chat-item').removeClass('active');
                $(this).addClass('active');
            });

            // Initial scroll to bottom
            $('.chat-messages').scrollTop($('.chat-messages')[0].scrollHeight);
        });
    </script>
</body>

</html> 