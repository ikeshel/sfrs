<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SFRS Directory</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background-color: #333;
            color: #fff;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }
        
        nav {
            position: absolute;
            top: 20px;
            left: 20px;
        }
        
        nav a {
            color: #fff;
            text-decoration: none;
            padding: 10px 20px;
            border: 1px solid #fff;
            border-radius: 4px;
            transition: 0.3s;
        }
        
        nav a:hover {
            background-color: #fff;
            color: #333;
        }
        
        .container {
            width: 80%;
            max-width: 900px;
        }
        
        h1 {
            text-align: center;
            margin-bottom: 30px;
            font-size: 2.5em;
        }
        
        p {
            line-height: 1.8;
            font-size: 1.1em;
            text-align: justify;
        }
    </style>
</head>
<body>
    <nav>
        <?php
        $baseDir = __DIR__;
        $dirs = glob($baseDir . '/menu_*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $dirName = basename($dir);
            // Remove 'menu_' prefix and format for display
            $label = strtoupper(str_replace('_', ' ', substr($dirName, 5))) . ' Directory';
            $href = htmlspecialchars($dirName) . '/';
            echo '<a href="' . $href . '">' . $label . '</a> ';
        }
        ?>
    </nav>
    
    <div class="container">
        <h1>Welcome</h1>
        <p>
            Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
        </p>
    </div>
</body>
</html>