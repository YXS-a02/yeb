function set_chat_msg() {
    const username = localStorage.getItem("user");
    if (!username) {
        alert("Please set your username before sending a message.");
        return;
    }

    const message = document.getElementById('message').value.trim();
    if (!message) {
        alert("Message cannot be empty.");
        return;
    }

    fetch('server.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `e=${encodeURIComponent('send')}&username=${encodeURIComponent(username)}&message=${encodeURIComponent(message)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            load_messages();
        } else {
            alert(data.msg);
        }
    })
    .catch(error => {
        console.error('Error sending message:', error);
        alert('Failed to send message. Please try again later.');
    });
}

function load_messages() {
    fetch('server.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_messages'
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        if (data.status === 'success') {
            const messagesContainer = document.getElementById('messages');
            messagesContainer.innerHTML = ''; // 清空旧消息
            data.messages.forEach(msg => {
                const messageElement = document.createElement('div');
                messageElement.innerHTML = `<strong>${msg.username}</strong>: ${msg.message}`;
                messagesContainer.appendChild(messageElement);
            });
        } else {
            alert(data.msg);
        }
    })
    .catch(error => {
        console.error('Error fetching messages:', error);
    });
}

setInterval(load_messages, 2500); // 每隔两秒加载一次消息