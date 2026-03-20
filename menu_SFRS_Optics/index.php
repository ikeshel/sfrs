<!DOCTYPE html>
<html lang="en">
<head>
<?php 
    include 'head.html';
?>
</head>
<body>
    <div class="menu-buttons" style="height: 50px; padding: 5px; background-color: #f0f0f0;">
        <button onclick="window.location.href='../'">HOME</button>
        <button onclick="changeSVG('S-FRS_Detectors_V.1.43_08.02.2023_1')">V.1.43</button>
        <button onclick="changeSVG('legend.webp')">Legend</button>
        <button onclick="changeSVG('minimal')">Minimal</button>
        <button onclick="changeSVG('only_first')">Only First</button>
    </div>

    <script>
    function changeSVG(filename) {
        document.querySelector('object[type="image/svg+xml"]').setAttribute('data', filename);
    }
    </script>
    <div class="container">
        <div class="svg-wrapper">
            <object type="image/svg+xml" data="S-FRS_Detectors_V.1.43_08.02.2023_1">
                <img src="" alt="" />
            </object>
        </div>
    </div>
</body>
</html>