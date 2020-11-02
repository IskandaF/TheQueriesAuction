<?php
$pdo = new PDO('mysql:host=localhost;port=8888;dbname=AuctionDB', 
   'root', 'root');
// See the "errors" folder for details...
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

