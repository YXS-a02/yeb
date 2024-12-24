function dn() {
    var passwed=document.getElementById("passwed").value
    var name=document.getElementById("user").value
    const mysql = require('mysql');
    const connection = mysql.createConnection({
      host: '',
      user: 'root',
      password: '150abcd051',
      database: 'yeb'
    });
    connection.connect((err) => {
      if (err) throw err;
      console.log('Connected to MySQL server!');
    });
}