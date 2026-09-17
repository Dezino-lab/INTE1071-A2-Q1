<?php
session_start();

foreach ($_SESSION['cart'] as $item) {
    echo $item['name'] . " - $" . $item['price'] . "<br>";
}

$_SESSION['total'] = 0;

if (!empty($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $_SESSION['total'] += $item['price'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <title>Shopping Cart</title>
    <!-- Bootstrap core CSS -->
    <link href="assets/css/bootstrap.css" rel="stylesheet">
    <!-- Fontawesome core CSS -->
    <link href="assets/css/font-awesome.min.css" rel="stylesheet" />
    <!--GOOGLE FONT -->
    <link href='http://fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <!--Slide Show Css -->
    <link href="assets/ItemSlider/css/main-style.css" rel="stylesheet" />
    <!-- custom CSS here -->
    <link href="assets/css/style.css" rel="stylesheet" />
    
    <style>
        .cart-container {
            width: 80%;
            margin: 0 auto;
            border: 1px solid #ddd;
            padding: 20px;
        }
        .cart-header, .cart-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .cart-header {
            border-bottom: 1px solid #ddd;
            padding-bottom: 10px;
        }
        .cart-item {
            padding: 10px 0;
            border-bottom: 1px solid #ddd;
        }
        .cart-item img {
            max-width: 100px;
        }
        .cart-item p {
            margin: 0;
        }
        .cart-item .description {
            flex: 2;
            padding: 0 10px;
        }
        .cart-item .price, .cart-item .qty, .cart-item .total {
            flex: 1;
            text-align: center;
        }
        .cart-item .qty input {
            width: 50px;
            text-align: center;
        }
        .update-btn, .remove-btn {
            background-color: black;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-default" role="navigation">
        <div class="container-fluid">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="index.php"><strong>ALICE'S</strong> ELECTRONIC BIKE Shop</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">


                <ul class="nav navbar-nav navbar-right">
                    <li><a href="#">Track Order</a></li>
                    <li><a href="#">Login</a></li>
                    <li><a href="#">Signup</a></li>

                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">24x7 Support <b class="caret"></b></a>
                        <ul class="dropdown-menu">
                            <li><a href="#"><strong>Call: </strong>+61-000-000-000</a></li>
                            <li><a href="#"><strong>Mail: </strong>info@alicebikeshop.com</a></li>
                            <li class="divider"></li>
                            <li><a href="#"><strong>Address: </strong>
                                <div>
                                    Melbourne,<br />
                                    VIC 3000, AUSTRALIA
                                </div>
                            </a></li>
                        </ul>
                    </li>
                </ul>
                <form class="navbar-form navbar-right" role="search">
                    <div class="form-group">
                        <input type="text" placeholder="Enter Keyword Here ..." class="form-control">
                    </div>
                    &nbsp; 
                    <button type="submit" class="btn btn-primary">Search</button>
                </form>
            </div>
            <!-- /.navbar-collapse -->
        </div>
                <!-- /.container-fluid -->
    </nav>
<div class="cart-container">
    <h1>Shopping Cart</h1>
    <div class="cart-header">
        <div>Remove</div>
        <div>Image</div>
        <div class="description">Product Description</div>
        <div class="price">Price</div>
        <div class="qty">Qty</div>
        <div class="total">Total</div>
    </div>

    <div class="cart-item">
        <div><input type="checkbox"></div>
        <div><img src="assets/img/bronton.jpg" alt="Electric Bike Model 1"></div>
        <div class="description">
            <p><strong>[EB1-500W-48V-28MPH] Electric Bike Model 1</strong></p>
            <p>500W Motor, 48V Battery, Range: 50 miles, Top Speed: 28 mph</p>
            <p>Availability: <span style="color:green;">Online</span> <span style="color:blue;">Immediate Pick-up</span></p>
        </div>
        <div class="price">$1,299.00</div>
        <div class="qty"><input type="number" value="1"></div>
        <div class="total">$1,299.00</div>
    </div>

    <div class="cart-item">
        <div><input type="checkbox"></div>
        <div><img src="assets/img/bronton.jpg" alt="Electric Bike Model 2"></div>
        <div class="description">
            <p><strong>[EB2-750W-52V-32MPH] Electric Bike Model 2</strong></p>
            <p>750W Motor, 52V Battery, Range: 60 miles, Top Speed: 32 mph</p>
            <p>Availability: <span style="color:green;">Online</span> <span style="color:blue;">Immediate Pick-up</span></p>
        </div>
        <div class="price">$1,499.00</div>
        <div class="qty"><input type="number" value="1"></div>
        <div class="total">$1,499.00</div>
    </div>

    <button class="update-btn">UPDATE QTY</button>
    <button class="remove-btn">REMOVE</button>

    <div class="cart-total">
        <h2>Total: $ <?php echo number_format($total, 2); ?></h2>
    </div>

    <div class="payment-section">
        <h2>Select Payment Option</h2>
        <div class="payment-icons">
            <button type="button">VISA</button>
            <button type="button">MasterCard</button>
            <button type="button">PayPal</button>
            <button type="button">Google Pay</button>
            <div id="gpay"></div>
        </div>
    </div>
    <div class="checkout-footer">
        <a href="billing.php" class="checkout-btn">CHECKOUT NOW &gt;&gt;&gt;</a>
    </div>
</div>
<?php
session_start();
?>

<table class="table">
    <thead>
        <tr>
            <th>Image</th>
            <th>Product</th>
            <th>Description</th>
            <th>Price</th>
            <th>Qty</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        <?php
        if (!empty($_SESSION['cart'])) {
            foreach ($_SESSION['cart'] as $item) {
                echo "<tr>";
                echo "<td><img src='images/default.png' width='80'></td>";
                echo "<td>{$item['name']}</td>";
                echo "<td>ID: {$item['id']}</td>";
                echo "<td>\${$item['price']}</td>";
                echo "<td>1</td>";
                echo "<td>\${$item['price']}</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Your cart is empty.</td></tr>";
        }
        ?>
    </tbody>
</table>

<!-- Local Google Pay Script -->
<script src="gpay.js"></script>
<!-- Google Pay JavaScript Library -->
<script async src="https://pay.google.com/gp/p/js/pay.js" onload="onGooglePayLoaded()"></script>


</body>
</html>
