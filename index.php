<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>登入介面</title>
</head>
<center>
<body>
    <?php
    ?>
    <form method = "post" action = "result.php">
        <h1>Login</h1>
        <p>
            <label for="Acc">帳號:
                <input name="Acc" type = "text" placeholder = "Key account" 
                  require /> 
            </label>
        </p>
        <p>
            <label for="Pass">密碼:
                <input name="Pass" type = "password" placeholder = "Key password" 
                  pattern="(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[A-Za-z\d]{8,20}" />
            </label>

        </p>
        <p>
            <input type = "submit" value = "Send" class="button"/>
            <input type = "reset" value = "Clear" class="button"/>
            <button type="button" onclick="location.href='guest.php'">訪客</button>
         </p> 
    </form>
</body>
</center>
</html>