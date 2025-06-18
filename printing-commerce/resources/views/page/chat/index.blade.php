<?php
$tPath = app()->environment('local') ? '' : '';
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat | TATA</title>
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

        .chat-container {
            flex: 1;
            display: flex;
            background: #fff;
            margin: 0;
            height: calc(100vh - 60px); /* Adjust for header */
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

        .chat-sidebar {
            width: 350px;
            border-right: 1px solid #e0e0e0;
            height: 100%;
            overflow-y: auto;
            background: #fff;
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

        /* Search bar styling */
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

        .chat-search input:focus {
            border-color: #4CAF50;
        }

        /* Loading state */
        .chat-loading {
            padding: 15px;
            text-align: center;
            color: #666;
        }

        .chat-loading span {
            display: inline-block;
            width: 100%;
            height: 20px;
            background: #f0f0f0;
            border-radius: 4px;
            animation: pulse 1.5s infinite;
        }

        @keyframes pulse {
            0% { opacity: 0.6; }
            50% { opacity: 1; }
            100% { opacity: 0.6; }
        }
    </style>
</head>

<body>
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
                    <input type="text" placeholder="Cari percakapan...">
                </div>

                <!-- Chat List -->
                <ul class="chat-list">
                    <li class="chat-item" data-user-id="user-1">
                        <div class="chat-item-content">
                            <img src="{{ asset($tPath.'assets/images/profile/user-1.jpg') }}" alt="User" class="user-avatar">
                            <div class="chat-item-info">
                                <div class="chat-item-header">
                                    <span class="chat-item-name">Emma Myers</span>
                                    <span class="chat-item-date">20.08.2025</span>
                                </div>
                                <div class="chat-item-preview">Logo Design - Rp 300.000</div>
                            </div>
                        </div>
                    </li>
                    <li class="chat-item" data-user-id="user-2">
                        <div class="chat-item-content">
                            <img src="{{ asset($tPath.'assets/images/profile/user-2.jpg') }}" alt="User" class="user-avatar">
                            <div class="chat-item-info">
                                <div class="chat-item-header">
                                    <span class="chat-item-name">Fufubaba Fufubini</span>
                                    <span class="chat-item-date">20.08.2025</span>
                                </div>
                                <div class="chat-item-preview">Website Design - Rp 500.000</div>
                            </div>
                        </div>
                    </li>
                    <li class="chat-item" data-user-id="user-3">
                        <div class="chat-item-content">
                            <img src="{{ asset($tPath.'assets/images/profile/user-3.jpg') }}" alt="User" class="user-avatar">
                            <div class="chat-item-info">
                                <div class="chat-item-header">
                                    <span class="chat-item-name">Watson Kalimasada</span>
                                    <span class="chat-item-date">20.08.2025</span>
                                </div>
                                <div class="chat-item-preview">Mobile App Design - Rp 800.000</div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div>

            <!-- Chat Main Area - Empty State -->
            <div class="chat-main">
                <div class="chat-empty-state">
                    <i class="fas fa-comments"></i>
                    <h3>Pilih percakapan untuk memulai chat</h3>
                    <p>Pilih salah satu percakapan dari daftar di sebelah kiri</p>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset($tPath.'assets/libs/jquery/dist/jquery.min.js') }}"></script>
    <script src="{{ asset($tPath.'assets/libs/bootstrap/dist/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Simple chat selection logic
        $(document).ready(function() {
            $('.chat-item').click(function() {
                const userId = $(this).data('user-id'); // Add data-user-id attribute to chat items
                window.location.href = `/chat/detail/${userId}`;
            });
        });
    </script>
</body>

</html> 