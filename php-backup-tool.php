<?php

/* 

MIT License

Copyright (c) 2021 Denis Mitlenko

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all
copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
SOFTWARE.

*/
// ------------------ BACKUP SETTINGS ------------------

// Backup folder
define('pbt_backupfolder', './backups/');

// ----------------- LOGIN INFORMATION -----------------

// User name
define('pbt_username', 'admin');

// Password
define('pbt_password', 'admin');

// ---------------------- TIME ZONE --------------------

// Time zone
date_default_timezone_set('Europe/Kyiv');

// ---------------------- MAIN CODE --------------------

set_time_limit(0);

class Backuper
{
    function __construct($path)
    {

        $this->path = $path;
        $this->file_name = date("Y-m-d-H-i-s").'_'.$this->generate(8).'_backup';
        if(!is_dir(pbt_backupfolder)){
            mkdir(pbt_backupfolder);
        }
    }

    public function tar($type){
        $rootPath = realpath($this->path);
        $file = pbt_backupfolder.$this->file_name.'.tar';
        $exclude = '/^(?!(.*backups))(.*)$/i'; 
        $p = new PharData($file);
        $p->buildFromDirectory($rootPath,$exclude);
        $p->compress($type);
        unset($p);
        Phar::unlinkArchive($file);
    }

    public function zip(){
        $rootPath = realpath($this->path);
        $file = pbt_backupfolder.$this->file_name.'.tar';
        $exclude = '/^(?!(.*backups))(.*)$/i'; 
        $p = new PharData($file);
        $p->buildFromDirectory($rootPath,$exclude);
        $p->convertToData(Phar::ZIP);
        unset($p);
        Phar::unlinkArchive($file);
    }

    private function generate($length = 10) {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}

// ------------------- MAIN PROGRAM --------------------

$islogged = (($_POST['username'] == pbt_username) and ($_POST['password'] == pbt_password));

$iserror = false;

if (isset($_POST['type']) and isset($_POST['folder']) and isset($_POST['submit'])){
    $type = $_POST['type'];
    $folder = $_POST['folder'];
    $b = new Backuper($folder);
    switch ($type) {
        case 'zip':
            $b->zip();
            break;
        case 'targz':
            $b->tar(Phar::GZ);
            break;
        case 'tarbz2':
            $b->tar(Phar::BZ2);
            break;
        default:
            $iserror = true;
            break;
    }
    header("Location:".$_SERVER['HTTP_REFERER']);
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>PHP Backup Tool</title>
    <style type="text/css">
        * {
            margin: 0;
            padding: 0;
        }

        .pbt-login-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        .pbt-login-box {
            width: 20em;
            height: auto;
            border: 1px Solid Black;
            border-radius: 2px;
            background: #FCF9F7;
            padding: 18px;
        }
        .pbt-login-form {
            /* */
        }
        .pbt-login-form > input[type=submit] {
            float: right;
        }
        .pbt-login-field {
            font-family: monospace;
        }
        .pbt-login-title,.pbt-main-title {
            font-family: Arial;
        }
        .pbt-main-status {
            font-family: monospace;
            font-size: 8pt;
        }
        .pbt-main-container {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        .pbt-main-box {
            width: 80em;
            height: auto;
            border: 1px Solid Black;
            border-radius: 2px;
            background: #FCF9F7;
            padding: 18px;
        }
        .pbt-main-content {
            font-family: monospace;
        }
        .pbt-main-backups, .pbt-main-tool {
            border: 1px Solid Black;
            border-radius: 2px;
            background: #FCFCFC;
            padding: 18px;
            box-shadow: 0px 0px 1px Black;
            margin: 8px;
        }
        input {
            margin: 4px 0px;
            font-family: monospace;
        }
        hr {
            margin: 8px;
            border-top: 1px Solid Black;
        }
        td, th {
            padding: 0px 8px;
        }
        th {
            text-align: left;
        }
    </style>
    <link href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAABAAAAAQCAYAAAAf8/9hAAAAAXNSR0IArs4c6QAAAARnQU1BAACxjwv8YQUAAAAJcEhZcwAADsMAAA7DAcdvqGQAAAIZSURBVDhPfVNNaxNRFD0TkhqSTNJ2iFJqVFBLWhAFyUKIkE1BF8WtK9GNUcRFf4E/oEgXJbTdFJfdCYIUEYpiWiliKFrKIIoLq6G2k3aSNP1KTe95ndFJU3uGy5v7ce579777tEajAS/eW7giS1YkI5KkTWCKvBEZv2ZgXlkc/E0gxKAsw/X67oPKWgkblTJ2d7aVL9B2AmE9Cr2jE35/YExMg5Joiz6VwCG/3ijb6ZXiT8y/fYWTiXOgb+mriUisHecvXUUwrCPe1Y1wNJaX+H4m8TGLYJjk379+COkPvn0u4OP0FF5OjEDTNJgfZvF8dAi1qg3GMJYcEn2smcfmzl7s1eu49+QpUv0DGLg/iKq9ju8LB+UzlhxyeYIsa+bOXiQu9sIfaFP/7EG8+wys4pLSGUuOIMsEGTbsMBryeREMhbG9WXM0qCYLMkyQdLt9HGxrRW7BcDS4N5R0m9iCgjTxS2FO/S/OvYO9uowLl1NK98IvYkqNLadI9PTh08w08i8mRdNw/dZtGHKFLtgXgckE+VBET9ql5gTx02dx485D1Mo2QtGYY/0H4XDJs4RctNOQ+26uxtWPItNHjiDn42zLeD4zTnUpJ7Eqw3IcGEsOue4oR8Q+VVlfS1vLxZaZcMGdSdbbOzjKNyVB1fuYmGREJuxuuWTJ2FaaHhNr5rG5s5gek0zf/57zIxHOu/c5c9ccj60sCsA+SiDmbpG2LJQAAAAASUVORK5CYII=" rel="icon" type="image/x-icon" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>
<body>
    <?php if(!$islogged): ?>
    <div class="pbt-login-container">
        <div class="pbt-login-box">
            <form class="pbt-login-form" method="POST">
                <h1 class="pbt-login-title">
                    PHP Backup Tool
                </h1>
                <div class="pbt-login-field">
                    <label for="apikey">
                        Username:
                    </label>
                    <input type="text" name="username" required="true">
                </div>
                <div class="pbt-login-field">
                    <label for="password">
                        Password:
                    </label>
                    <input type="password" name="password" required="true">
                </div>
                <input type="submit">
            </form>
        </div>
    </div>
    <?php else: ?>
    <div class="pbt-main-container">
        <div class="pbt-main-box">
            <div class="pbt-main-header">
                <h1 class="pbt-main-title">PHP Backup Tool</h1>
                <span class="pbt-main-status">
                    Logged in as <?php echo pbt_username ?>
                    <a href>Log out</a>        
                </span>
            </div>
            <hr>
            <div class="pbt-main-content">
                Available backups:
                <table class="pbt-main-backups">
                    <?php 
                        $backups = @scandir(pbt_backupfolder);
                        if ($backups){
                            ?>
                            <tr>
                                <th>Date:</th>
                                <th>ID:</th>
                                <th>Size:</th>
                                <th>Filename:</th>
                            </tr>
                            <?php
                            foreach ($backups as $file) {
                                if (!is_dir($file)){
                                    echo "<tr>";
                                    echo "<td>".explode('_',$file)[0]."</td>";
                                    echo "<td>".explode('_',$file)[1]."</td>";
                                    echo "<td>".((@filesize($file)/1024)/1024)."MB</td>";
                                    echo "<td><a href=\"".pbt_backupfolder."$file\">$file</a></td>";
                                    echo "</tr>";
                                }
                            }
                        } else {
                            echo "<tr><td>No available backups</td></tr>";
                        }
                        
                    ?>
                </table>
                Backup tool:
                <form method="POST" class="pbt-main-tool">
                    <input type="hidden" name="username" value="<?php echo $_POST['username'] ?>">
                    <input type="hidden" name="password" value="<?php echo $_POST['password'] ?>">
                    <div class="pbt-form-field">
                        <label for="type">
                            Type:
                        </label>
                        <select name="type">
                            <option value="zip">.zip</option>
                            <option value="targz">.tar.gz</option>
                            <option value="tarbz2">.tar.bz2</option>
                        </select>
                    </div>
                    <div class="pbt-form-field">
                        <label for="folder">
                            Folder:
                        </label>
                        <input type="text" name="folder" value=".">
                    </div>
                    <div class="pbt-form-field">
                        <input type="submit" name="submit">
                    </div>
                    
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>