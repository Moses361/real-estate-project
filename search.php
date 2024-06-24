<?php
include_once "connection.php";

if (isset($_POST['submit'])) {
    $search_value = $_POST['search'] ?? '';
    $delivery_type = $_POST['delivery_type'] ?? '';
    $search_price = $_POST['search_price'] ?? '';
    $property_type = $_POST['property_type'] ?? '';

    // Prepare the base query
    $query = "SELECT * FROM properties WHERE 1=1";
    $params = [];
    $types = '';

    // Add search value condition if provided
    if (!empty($search_value)) {
        $query .= " AND (property_title LIKE ? OR property_details LIKE ? OR property_address LIKE ? OR property_type LIKE ?)";
        $like_search_value = '%' . $search_value . '%';
        $params = array_merge($params, array_fill(0, 4, $like_search_value));
        $types .= str_repeat('s', 4);
    }

    // Add delivery type condition if provided
    if (!empty($delivery_type)) {
        $query .= " AND delivery_type = ?";
        $params[] = $delivery_type;
        $types .= 's';
    }

    // Add property type condition if provided
    if (!empty($property_type)) {
        $query .= " AND property_type = ?";
        $params[] = $property_type;
        $types .= 's';
    }

    // Add price range conditions based on the search_price value
    if ($search_price == 1) {
        $query .= " AND price >= 5000 AND price <= 50000";
    } elseif ($search_price == 2) {
        $query .= " AND price >= 50000 AND price <= 100000";
    } elseif ($search_price == 3) {
        $query .= " AND price >= 100000 AND price <= 200000";
    } elseif ($search_price == 4) {
        $query .= " AND price >= 200000";
    }

    // Prepare the statement
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        // Bind parameters to the prepared statement
        if (!empty($params)) {
            mysqli_stmt_bind_param($stmt, $types, ...$params);
        }

        // Execute the statement
        mysqli_stmt_execute($stmt);

        // Get the result
        $result = mysqli_stmt_get_result($stmt);

        if (!$result) {
            echo "Error executing query: " . mysqli_error($con);
        }
    } else {
        echo "Error preparing statement: " . mysqli_error($con);
    }
} else {
    header("Location: index.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
<title>Search Result - Real Estate Management System</title>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>

<link rel="stylesheet" href="assets/bootstrap/css/bootstrap.css" />
<link rel="stylesheet" href="assets/style.css"/>
<script src="assets/jquery-1.9.1.min.js"></script>
<script src="assets/bootstrap/js/bootstrap.js"></script>
<script src="assets/script.js"></script>

<!-- Owl stylesheet -->
<link rel="stylesheet" href="assets/owl-carousel/owl.carousel.css">
<link rel="stylesheet" href="assets/owl-carousel/owl.theme.css">
<script src="assets/owl-carousel/owl.carousel.js"></script>
<!-- Owl stylesheet -->

<!-- slitslider -->
<link rel="stylesheet" type="text/css" href="assets/slitslider/css/style.css" />
<link rel="stylesheet" type="text/css" href="assets/slitslider/css/custom.css" />
<script type="text/javascript" src="assets/slitslider/js/modernizr.custom.79639.js"></script>
<script type="text/javascript" src="assets/slitslider/js/jquery.ba-cond.min.js"></script>
<script type="text/javascript" src="assets/slitslider/js/jquery.slitslider.js"></script>
<!-- slitslider -->

<script src='assets/google_analytics_auto.js'></script></head>

<body>

<!-- Header Starts -->
<div class="navbar-wrapper">

    <div class="navbar-inverse" style="background-color: #0BE0FD">
        <div class="container">
            <div class="navbar-header">

                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                    <span class="sr-only">Toggle navigation</span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

            </div>

            <!-- Nav Starts -->
            <div class="navbar-collapse collapse">
                <ul class="nav navbar-nav navbar-right">
                    <li class="active"><a href="index.php">Home</a></li>
                    <li><a href="about.html">About</a></li>
                    <li><a href="contact.html">Contact</a></li>
                </ul>
            </div>
            <!-- #Nav Ends -->

        </div>
    </div>

</div>
<!-- #Header Starts -->

<div class="container">

    <!-- Header Starts -->
    <div class="header">
        <!-- <a href="index.php"><img src="images/logo.png" alt="Realestate"></a> -->

        <div class="menu">
            <ul class="pull-right">
                <li><a href="index.php">Home</a></li>
                <li><a href="list-properties.php">List Properties</a>
                    <ul class="dropdown">
                        <li><a href="sale.php">Properties on Sale</a></li>
                        <li><a href="rent.php">Properties on Rent</a></li>
                    </ul>
                </li>

            </ul>
        </div>
    </div>
    <!-- #Header Starts -->
</div><!-- banner -->
<div class="inside-banner">
    <div class="container">
        <h2>Search Result:</h2>
    </div>
</div>

<!-- banner -->

<div class="container">
    <div class="properties-listing spacer">

        <div class="row">
            <div class="col-lg-3 col-sm-4 ">

                <div class="search-form"><h4><span class="glyphicon glyphicon-search"></span> Search for</h4>
                <form id="propertySearchForm" action="search.php" method="post" name="search" onsubmit="return validateForm()">
        <input type="text" class="form-control" name="search" placeholder="Search of Properties">
        <div class="row mt-2">
            <div class="col-lg-5">
                <select name="delivery_type" class="form-control">
                    <option value="">Delivery Type</option>
                    <option value="Rent">Rent</option>
                    <option value="Sale">Sale</option>
                </select>
            </div>
            <div class="col-lg-7">
                <select name="search_price" class="form-control">
                    <option value="">Price</option>
                    <option value="1">$5000 - $50,000</option>
                    <option value="2">$50,000 - $100,000</option>
                    <option value="3">$100,000 - $200,000</option>
                    <option value="4">$200,000 - above</option>
                </select>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-lg-12">
                <select name="property_type" class="form-control">
                    <option value="">Property Type</option>
                    <option value="Apartment">Apartment</option>
                    <option value="Building">Building</option>
                    <option value="Office-Space">Office-Space</option>
                </select>
            </div>
        </div>
        <button type="submit" name="submit" class="btn btn-primary mt-3">Find Now</button>
    </form>
                </div>

            </div>

            <div class="col-lg-9 col-sm-8">
                <div class="sortby clearfix">
                    <div class="pull-left result">
                        <ul style="list-style-type: none;">
                            <?php if (!empty($search_value)) { ?><li>Location: <?php echo htmlspecialchars($search_value); ?></li><?php } ?>
                            <?php if (!empty($delivery_type)) { ?><li>Buy/Rent: <?php echo htmlspecialchars($delivery_type); ?></li><?php } ?>
                            <?php if (!empty($search_price)) { ?><li>Price: 
                                <?php
                                    if ($search_price == 1) echo "$5000 - $50,000";
                                    elseif ($search_price == 2) echo "$50,000 - $100,000";
                                    elseif ($search_price == 3) echo "$100,000 - $200,000";
                                    elseif ($search_price == 4) echo "$200,000 - above";
                                ?>
                            </li><?php } ?>
                            <?php if (!empty($property_type)) { ?><li>Type: <?php echo htmlspecialchars($property_type); ?></li><?php } ?>
                        </ul>
                    </div>
                        <div class="pull-right">
                    </div>

                </div>
                <div class="row">

                    <!-- properties -->
                    <?php
                    while($property_result = mysqli_fetch_assoc($result)){
                        $id = $property_result['property_id'];
                        $property_title = $property_result['property_title'];
                        $delivery_type = $property_result['delivery_type'];
                        $availablility = $property_result['availablility'];
                        $price = $property_result['price'];
                        $property_img = $property_result['property_img'];
                        $bed_room = $property_result['bed_room'];
                        $liv_room = $property_result['liv_room'];
                        $parking = $property_result['parking'];
                        $kitchen = $property_result['kitchen'];
                        $utility = $property_result['utility'];

                        ?>
                        <div class="col-lg-4 col-sm-6">
                            <div class="properties">
                                <div class="image-holder"><img src="<?php echo $property_img; ?>" class="img-responsive" alt="properties">
                                    <?php if($availablility == 0){ ?><div class="status sold">Available</div> <?php } else { ?>
                                    <div class="status new">Not Available</div>
                                    <?php } ?>
                                </div>
                                <h4><a href="property-detail.php?id=<?php echo $id; ?>"><?php echo $property_title; ?></a></h4>
                                <p class="price">Price: $<?php echo $price; ?></p>
                                <div class="listing-detail"><span data-toggle="tooltip" data-placement="bottom" data-original-title="Bed Room"><?php echo $bed_room; ?></span> <span data-toggle="tooltip" data-placement="bottom" data-original-title="Living Room"><?php echo $liv_room; ?></span> <span data-toggle="tooltip" data-placement="bottom" data-original-title="Parking"><?php echo $parking; ?></span> <span data-toggle="tooltip" data-placement="bottom" data-original-title="Kitchen"><?php echo $kitchen; ?></span> </div>
                                <a class="btn btn-primary" href="property-detail.php?id=<?php echo $id; ?>">View Details</a>
                            </div>
                        </div>
                        <?php
                    }
                    ?>
                    <!-- properties -->

                </div>
            </div>
        </div>
    </div>
</div>
</div>

<div style="background-color: #0BE0FD">
    <div class="container">

        <div class="row">
            <div class="col-lg-4 col-sm-4">
                <h4>Information</h4>
                <ul class="row">
                    <li class="col-lg-12 col-sm-12 col-xs-3"><a href="about.html">About</a></li>
                    <li class="col-lg-12 col-sm-12 col-xs-3"><a href="agents.php">Agents</a></li>
                    <li class="col-lg-12 col-sm-12 col-xs-3"><a href="blog.html">Blog</a></li>
                    <li class="col-lg-12 col-sm-12 col-xs-3"><a href="contact.html">Contact</a></li>
                </ul>
            </div>

            <div class="col-lg-4 col-sm-4">
                <h4>Newsletter</h4>
                <p>Get notified about the latest properties in our marketplace.</p>
                <form class="form-inline" role="form">
                    <input type="text" placeholder="Enter Your email address" class="form-control">
                    <button class="btn btn-success" type="button">Notify Me!</button>
                </form>
            </div>

            <div class="col-lg-4 col-sm-4">
                <h4>Follow us</h4>
                <a href="#"><img src="images/facebook.png" alt="facebook"></a>
                <a href="#"><img src="images/twitter.png" alt="twitter"></a>
                <a href="#"><img src="images/linkedin.png" alt="linkedin"></a>
                <a href="#"><img src="images/instagram.png" alt="instagram"></a>
            </div>

            <p class="copyright">Copyright 2024. All rights reserved.	</p>

        </div>
    </div>
</div>

<script>
        function validateForm() {
            const form = document.forms['search'];
            const searchValue = form['search'].value.trim();
            const deliveryType = form['delivery_type'].value.trim();
            const searchPrice = form['search_price'].value.trim();
            const propertyType = form['property_type'].value.trim();

            if (searchValue === '' && deliveryType === '' && searchPrice === '' && propertyType === '') {
                alert('Please fill in at least one field.');
                return false; // Prevent form submission
            }

            return true; // Allow form submission
        }
    </script>

</body>
</html>
