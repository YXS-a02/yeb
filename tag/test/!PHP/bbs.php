<php?
$password="150abcd051"
$db = new mysqli('localhost', 'root', $password, 'bbs');
?>
<div>
    <from method="get" enctype="multipart/form-data">
        <input type="text" name="id">
        <input type="submit" value="ok">
    </from>
</div>