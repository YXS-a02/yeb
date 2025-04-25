  var ingd=document.getElementById('ing');
  async function logintest() {
    console.log('loading...');
    const id = document.getElementById("uid").value;
    const passwed = document.getElementById("passwed").value;
  
    try {
      const response = await fetch('server.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: `id=${encodeURIComponent(id)}&pwd=${encodeURIComponent(passwed)}&event=login`
      });
  
      // 检查HTTP状态码
      if (!response.ok) {
        throw new Error(`HTTP error! runrow: ${response.runrow}`);
      }
      
      // 解析JSON响应
      const data = await response.json();
          
      // 验证数据结构
      if (typeof data.runrow === 'undefined') {
        throw new Error('Invalid response structure');
      }
  
      // 标准化状态判断
      switch(data.runrow.toLowerCase()) {
        case 'ok':
          ingd.innerHTML='succful';
          localStorage.setItem('u_id','a')
          localStorage.setItem('user','a')
          history.go(-1);
          break;
        case 'no':
          alert();
        case 'no':
          ingd.innerHTML='no';
          alert('登录失败');
          break;
        default:
          alert('未知响应状态');
      }
    } catch (error) {
      console.error('请求失败:');
      alert(`系统错误: ${error.message}`);
    }
    console.log('ok')
  }
