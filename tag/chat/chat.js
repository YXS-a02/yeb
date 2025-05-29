// 工具函数：转义 HTML
function escapeHTML(str) {
    return str.replace(/&/g, '&amp;')
             .replace(/</g, '&lt;')
             .replace(/>/g, '&gt;')
             .replace(/"/g, '&quot;')
             .replace(/'/g, '&#039;');
  }
  
  // 工具函数：通用请求
  async function apiRequest(action, data = {}) {
    const params = new URLSearchParams({ action, ...data });
    const response = await fetch('server.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
      body: params.toString(),
    });
    if (!response.ok) throw new Error(`HTTP error: ${response.status}`);
    return await response.json();
  }
  
  // 发送消息
  async function set_chat_msg() {
    const username = localStorage.getItem("user");
    if (!username) {
      alert("Please set your username before sending a message.");
      return;
    }
  
    const messageInput = document.getElementById('message');
    const message = messageInput.value.trim();
    if (!message) {
      alert("Message cannot be empty.");
      return;
    }
  
    try {
      const data = await apiRequest('send_message', { username, message });
      if (data.status === 'success') {
        messageInput.value = ''; // 清空输入框
        load_messages();
      } else {
        alert(data.msg || 'Failed to send message.');
      }
    } catch (error) {
      alert('Failed to send message. Please try again later.');
    }
  }
  
  // 加载消息
  async function load_messages() {
    try {
      const data = await apiRequest('get_messages');
      if (data.status === 'success') {
        const messagesContainer = document.getElementById('messages');
        messagesContainer.innerHTML = '';
        data.messages.forEach(msg => {
          const messageElement = document.createElement('div');
          const formattedTime = new Date(msg.timestamp).toLocaleString();
          messageElement.innerHTML = `<strong>[${escapeHTML(formattedTime)}] ${escapeHTML(msg.username)}</strong>:<br>${escapeHTML(msg.message)}`;
          messagesContainer.appendChild(messageElement);
        });
        // 滚动到底部
        messagesContainer.scrollTop = messagesContainer.scrollHeight;
      }
    } catch (error) {
      console.error('Failed to load messages:', error);
    }
  }
  
  // 每 4 秒刷新消息
  setInterval(load_messages, 8000);
  
  // 初始加载
  document.addEventListener('DOMContentLoaded', load_messages);