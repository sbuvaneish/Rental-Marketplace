<?php

session_start();

if(!isset($_SESSION['user_id'])) {
  header("Location: /login.php");
}

unset($_SESSION['product_id']);

require 'database.php';

$description_search = "";

if(isset($_POST['search_submit'])) {
    $description_search = $_POST['search'];
}

$query = "SELECT products.product_id as product_id, image, description, is_available, owns.created_at as datetime FROM products JOIN owns on owns.user_id = :user_id AND owns.product_id = products.product_id AND description LIKE CONCAT('%', :description_search, '%')";

$records = $conn->prepare($query);
$records->bindParam(':user_id', $_SESSION['user_id']);
$records->bindParam(':description_search', $description_search);
$records->execute();
$results = $records->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->

<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" />

<div class="container">
    <h3 class="h3" style="color:green">Here are your Products!!</h3>
    <span style="float:right;color:green">Welcome, <?=$_SESSION['email']?></span>
    <form action="/view.php" method="post">
      <input type="text" placeholder="Search.." name="search" value="<?=$_POST['search']?>">
      <input type="submit" name="search_submit" value="Submit"/>
    </form>
    <br>
    <div class="row">
        <?php for($index = 0; $index < count($results); $index++) { ?>

        <div class="col-md-3 col-sm-6">
            <div class="product-grid">
                <div class="product-image">
                    <img height="100" width="100" src="data:image/jpg;base64,<?=$results[$index]["image"]?>" />
                </div>
                
                <div class="product-content">
                    Description: <?= $results[$index]["description"] ?><br>
                    Availability: <?= ($results[$index]["is_available"]) ? "Yes" : "No" ?><br>
                    Date of Upload: <?= explode(" ", $results[$index]["datetime"])[0] ?>
                    <a href="upload.php?product_id=<?=$results[$index]['product_id']?>" target="_blank" class="list-group-item list-group-item-action active" style="background-color:orange">More info!</a>
                    <br>
                </div>
                
            </div>
        </div>
        <?php } ?>
    </div>
</div>