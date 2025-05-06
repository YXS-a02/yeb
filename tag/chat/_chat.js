function set_chat_msg() {
    const username = localStorage.getItem("user")
    const message = document.getElementById('message').value;

    fetch('_server.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `username=${encodeURIComponent(username)}&message=${encodeURIComponent(message)}`
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            load_messages();
        } else {
            alert(data.msg);
        }
    });
}

function load_messages() {
    fetch('_server.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=get_messages'
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            document.getElementById('messages').innerHTML = data.messages.replace(/\n/g, '<br>');
        } else {
            alert(data.msg);
        }
    })
    .catch(error => {
        console.error('Error fetching messages:', error);
    });
}

setInterval(load_messages, 2000); // 每隔两秒加载一次消息
setInterval(load_messages, 2000); // 每隔两秒加载一次消息