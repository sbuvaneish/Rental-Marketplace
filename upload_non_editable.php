<?php

session_start();

if(!isset($_SESSION['user_id'])) {
  header("Location: /login.php");
}

require 'database.php';


if(isset($_GET['product_id'])) {
  
  $autofill_query = "SELECT * from products where product_id = :product_id";
  $records = $conn->prepare($autofill_query);
  $records->bindParam(':product_id', $_GET['product_id']);
  $records->execute();
  $view_results = $records->fetchAll(PDO::FETCH_ASSOC)[0];
  
  $autofill_query = "SELECT username FROM users JOIN owns ON owns.product_id = :product_id AND owns.user_id = users.id";
  $records = $conn->prepare($autofill_query);
  $records->bindParam(':product_id', $_GET['product_id']);
  $records->execute();
  $email = $records->fetchAll(PDO::FETCH_ASSOC)[0]["username"];
  
}

?>

<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<link rel="stylesheet" type="text/css" href="assets/css/upload.css">
<div class="container contact-form">
            <form enctype="multipart/form-data" method="post" action="message.php">
               <h3>Product Information</h3>
               <div class="row">
                    <div class="col-md-6">
                        
                        <div class="form-group">
                            <br><br>
                            
                            <img height="100" width="100" src="data:image/jpg;base64,<?= $view_results["image"]?>" />
                            
                        </div>
                        

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" placeholder="Your Description" style="width: 100%; height: 150px;" readonly><?= $view_results["description"]?></textarea>
                        </div>
                        
                        <div class="form-group">
                          <label for="availability">Availability</label>
                          <label class="radio-inline"><input type="radio" value="yes" name="availability" <?php if ($view_results["is_available"] === "1"){echo "checked";} else{echo "disabled";}?> >Yes</label>
                          <label class="radio-inline"><input type="radio" value="no" name="availability" <?php if ($view_results["is_available"] === "0"){echo "checked";} else{echo "disabled";}?> >No</label>
                        </div>
                        
                        
                        
                    </div>
                    
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_name">Product name</label>
                            <input type="text" name="product_name" class="form-control" placeholder="Name" value="<?= $view_results["name"] ?>" readonly />
                        </div>
                        <div class="form-group">
                            <label for="product_brand">Brand</label>
                            <input type="text" name="product_brand" class="form-control" placeholder="Brand" value="<?= $view_results["brand"] ?>" readonly />
                        </div>
                        <div class="form-group">
                            <label for="product_color">Color</label>
                            <input type="text" name="product_color" class="form-control" placeholder="Color" value="<?= $view_results["color"]?>" readonly />
                        </div>
                        <div class="form-group">
                            <label for="product_price">Price</label>
                            <input type="text" name="product_price" class="form-control" placeholder="Price" value="<?= $view_results["price"]?>" readonly />
                        </div>
                        <div>
                            <input type="hidden" name="myEmail" value="<?= $email ?>" />
                            <input type="submit" value="Message User" />
                        </div>
                  </div>
                </div>
               
            </form>
</div>
