function dndn() {
    var name=document.getElementById("name").value
    var pwb=document.getElementById("passwed").valueconst 
    mysql = require('mysql');
    const connection = mysql.createConnection({
      host: '10.15.85.15',
      user: 'net',
      password: '150051',
      database: 'yeb'
    });
    const mysql = require('mysql');
    
    connection.connect((err) => {
      if (err) throw err;
      console.log('Connected to MySQL server.');
    
      // 准备SQL查询
      const sqlQuery = `SELECT id, pwb FROM your_table WHERE name = ?`;
    
      // 查询数据
      const [rows] = await connection.query(sqlQuery, ['name']);
    
      if (rows.length > 0) {
        const result = rows[0]; // 如果找到结果，提取第一条数据
        console.log('Found row with ID:', result.id);
        console.log('pwb value:', result.pwb);
    
        // 关闭连接
        connection.end();
      } else {
        console.log('No row found for the given name.');
      }
    });


}