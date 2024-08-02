<?php
/* @var $this yii\web\View */
$this->title = 'WebSocket Test';
$websocketUrl = env('WEB_SOCKET_URL');
?>
<div class="site-index">
    <h1>WebSocket Chat</h1>
    <div id="messages"></div>
    <div id="input">
        <input type="text" id="messageInput" placeholder="Type a message..." autofocus>
        <button onclick="sendMessage()">Send</button>
    </div>

    <script>
        const websocketUrl = '<?= $websocketUrl ?>';
        const socket = new WebSocket(websocketUrl);

        // Get references to the DOM elements
        const messagesDiv = document.getElementById('messages');
        const messageInput = document.getElementById('messageInput');

        // Event handler for when a message is received from the server
        socket.addEventListener('message', function (event) {
            const message = event.data;
            const messageElement = document.createElement('div');
            messageElement.textContent = message;
            messageElement.classList.add('message');
            messagesDiv.appendChild(messageElement);
            messagesDiv.scrollTop = messagesDiv.scrollHeight;
        });

        // Event handler for when the connection is opened
        socket.addEventListener('open', function (event) {
            const messageElement = document.createElement('div');
            messageElement.textContent = 'Connected to WebSocket server';
            messageElement.classList.add('message');
            messagesDiv.appendChild(messageElement);
        });

        // Event handler for when an error occurs
        socket.addEventListener('error', function (event) {
            const messageElement = document.createElement('div');
            const errorMessage = event.message || 'Unknown error';
            messageElement.textContent = 'Error: ' + errorMessage;
            messageElement.classList.add('message');
            messagesDiv.appendChild(messageElement);
        });

        // Event handler for when the connection is closed
        socket.addEventListener('close', function (event) {
            const messageElement = document.createElement('div');
            messageElement.textContent = 'Disconnected from WebSocket server';
            messageElement.classList.add('message');
            messagesDiv.appendChild(messageElement);
        });

        // Function to send a message to the WebSocket server
        function sendMessage() {
            const message = messageInput.value;
            if (message) {
                socket.send(message);
                const messageElement = document.createElement('div');
                messageElement.textContent = 'You: ' + message;
                messageElement.classList.add('message');
                messagesDiv.appendChild(messageElement);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
                messageInput.value = '';
            }
        }

        // Optional: send a message when pressing Enter key
        messageInput.addEventListener('keypress', function (event) {
            if (event.key === 'Enter') {
                sendMessage();
                event.preventDefault(); // Prevents newline in input
            }
        });
    </script>
</div>