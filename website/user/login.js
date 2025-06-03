  var ingd=document.getElementById('ing');
  async function login() {
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
        case 'yes':
          ingd.innerHTML='succful';
          localStorage.setItem('u_id',data.username);
          localStorage.setItem('user',data.username);
          yookie.set('u_name',data.username,31536e3,'/');
          yookie.set('u_id',id,31536e3,'/');
          history.go(-1);
          break;
        case 'uno':
          alert('uno');
          break;
        case 'pno':
          ingd.innerHTML='错误！';
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
