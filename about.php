<?php
require 'functions.php';
require 'dbconnect.php';
head();
echo'<title>O nás</title>';
navbar();
echo'
<body>
<h1>SK Třebechovice pod Orebem, oddíl kanoistiky</h1>
<div class="contentContainer">
    <div class="personContainer">
        <div class="person">
            <h2>Matyáš Marek</h2>
            <div class="contentContainer">
                <img src="img/mm.jpg" alt="Matyáš Marek" class="profile">
            </div>
            <p>programátor, vývojář</p>
            <div class="table">
                <table>
                    <tr>
                        <td>věk</td>
                        <td>19 let</td>
                    </tr>
                    <tr>
                        <td>studia</td>
                        <td>DELTA SŠIE, UHK</td>
                    </tr>
                    <tr>
                        <td>zájmy</td>
                        <td>sporty, vše kolem IT</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>
</body>
</html>
';