<%
    ' 创建数据库连接对象
    Set conn = Server.CreateObject("ADODB.Connection")
    ' 打开数据库连接
    conn.Open "Provider=SQLOLEDB;Data Source=10.15.85.15;Initial Catalog=YourDatabaseName;User ID=YourUsername;Password=150abcd051;"
    
    ' 执行SQL查询
    Set rs = conn.Execute("SELECT * FROM YourTableName")
    
    ' 输出查询结果
    Do While Not rs.EOF
        Response.Write(rs("ColumnName") & "<br>")
        rs.MoveNext
    Loop
    
    ' 关闭记录集和连接
    rs.Close
    conn.Close
    Set rs = Nothing
    Set conn = Nothing
%>
