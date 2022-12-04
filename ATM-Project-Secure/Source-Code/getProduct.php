<?php
$connect=pg_connect("host=localhost dbname=catingerdb user=tomoechan password=khoichan") or die();

$productId=$_GET['productId'];

if(!is_numeric($productId) || strpos($productId,'.')== true || $productId > 3 || $productId <= 0){
header("Location: http://localhost/ATM_Secure/404.html");
}
else {
//$queryProduct="SELECT * FROM products WHERE id=".$productId;
//$result = pg_query($connect,$queryProduct) or die();

$queryProduct='SELECT * FROM products WHERE id=$1';
$result= pg_query_params($connect, $queryProduct, array($productId)) or die(); // parameterized queries


$html_brand="";
$html_description="";
$html_price=0;
$product_image="";

while($row=pg_fetch_assoc($result)){
      $html_brand=$row["brand"];
      $html_description=$row["description"];
      $html_price=$row["price"];
}
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge"> <!╌ for support old legacy browser ╌>
	<meta name="viewport" content="width=device-width, intial-scale=1.0"><!╌ This gives the browser instructions on how to control the page's dimensions and scaling. -->
	<title>THE CATINGER SHOP</title>
	<link rel="icon" href="f2.ico">
	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fortawesome/fontawesome-free@6.2.0/css/fontawesome.min.css" />
	 <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
	<link rel="stylesheet" href="assets/styles.css"/>
</head>
<body>
	<section id="header">
	<a href="index.html"><img src="img/logoes.png" class="logo" width="135" height="135"></a>		
	<div>
		<ul id="navbar">
			<li><a class="active" href="index.html">Home</a></li>
			<li><a href="shop.html">Shop</a></li>
			<li><a href="#">Blog</a></li>
			<li><a href="About.html">About</a></li>
			<li><a href="#">Contact</a></li>
			<!--<li><a href="#"><i class="fa-thin fa-cart-shopping"></i></a></li>-->
	</div>
	</section>
	<section id="product1" class="section-p1">
		<p>Summers Catnip Collection</p>
    </section>
	<section id="prodetails" class="section-p1">

		<div class="single-pro-image">
                <?php echo '<img src="img/products/f'.$productId.'.jpg" class="box1" width=100% id="MainImg" alt="">
			
		</div>
		<div class="single-pro-details">
			<h6>home/ Catnip</h6>
			<h4>'.$html_brand.'</h4>
			<h2>'.$html_price.',00$</h2>
			<input type="number" value="1">
			<button class="button-74"> Add to Cart</button>
			<h4>Product Details</h4>
			<span>'.$html_description.'</span>

		</div>
	</section>';
 ?>
	<footer class="section-p1">
		<div class="col">
		<h4>Contact</h4>
		<p><strong>Address:</strong> Ho Chi Minh National University Dormitory A, Binh Duong</p>
		<p><strong>Phone:</strong> (+84)19 215 538</p>
		<p><strong>Hours:</strong> 10:00 - 12:00, Mon - Tue</p>
		</div>
		<div class="col">
		<h4>About author</h4>
		<p><strong>Name:</strong>suppi147</p>
		<p><strong>Favourite pet:</strong> cat</p>
		<p><strong>Favourite catnip:</strong> Meowijuana</p>
		</div>
	</footer>
</body>
</html>
