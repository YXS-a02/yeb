function set_chat_msg() {
    const username = localStorage.getItem("u_id")
    const message = document.getElementById('message').value;

    fetch('server.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `e=${encodeURIComponent('send')}&username=${encodeURIComponent(username)}&message=${encodeURIComponent(message)}`
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
        document.getElementById('messages').innerHTML = data.replace(/\n/g, '<br>');
    });
}

setInterval(load_messages, 2500); // 每隔两秒加载一次消息