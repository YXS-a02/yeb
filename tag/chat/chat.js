function set_chat_msg() {
    const username = localStorage.getItem("user")
    const message = document.getElementById('message').value;

    fetch('server.php', {
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
    fetch('chat.txt')
    .then(response => response.text())
    .then(data => {
        document.getElementById('messages').innerHTML = '<pre>' + data.replace(/\n/g, '<br>') + '</pre>';
    });
}

setInterval(load_messages, 2000); // 每隔两秒加载一次消息