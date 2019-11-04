<?php

session_start();

if(!isset($_SESSION['user_id'])) {
  header("Location: /login.php");
}

require 'database.php';
require 'tagger.php';

//method to check URL validity
function isValidURL($url) {
  $file_ext = strtolower(end(explode('.', $url)));
      
  $extensions = array("jpeg","jpg","png");
  
  return (in_array($file_ext,$extensions) === true);
}



//initializing errors and variable values
$imagerr = $descriptionerr = $availabilityerr = $insertionerr = "";
$image_val = $description_val = $availability_val = $name_val = $brand_val = $color_val = $price_val = "";


//If redirection is from View Product page
if(isset($_GET['product_id'])) {
  
  $_SESSION['product_id'] = $_GET['product_id'];
  
  //Getting product information for autofill using product_id
  $autofill_query = "SELECT * from products where product_id = :product_id";
  $records = $conn->prepare($autofill_query);
  $records->bindParam(':product_id', $_GET['product_id']);
  $records->execute();
  $view_results = $records->fetchAll(PDO::FETCH_ASSOC);
  
  $image_val = $view_results[0]["image"];
  $description_val = $view_results[0]["description"];
  $availability_val = intval($view_results[0]["is_available"]);
  $name_val = $view_results[0]["name"];
  $brand_val = $view_results[0]["brand"];
  $color_val = $view_results[0]["color"];
  $price_val = $view_results[0]["price"];
  $product_id = $view_results[0]["product_id"];
  
  $query = "SELECT u.username from users u, owns o where o.product_id = :product_id and u.id = o.user_id";
  $records = $conn->prepare($query);
  $records->bindParam(':product_id', $product_id);
  $records->execute();
  $view_results = $records->fetchAll(PDO::FETCH_ASSOC);
  $email = $view_results[0]["username"];
  //store the image into the session so as to keep displaying in the form
  $_SESSION['image_val'] = $image_val;
  
}


// All post requests for the page except the autofill.
else if ($_SERVER["REQUEST_METHOD"] == "POST") {
  
  //checking url validity for all post methods
  if(!isValidURL($_POST['image_url'])) {
    $imagerr = "Invalid URL, please choose a JPEG or PNG file.";
  }
  
  //Submission of product information
  if(isset($_POST['btnSubmit']) or isset($_POST['autoFillSubmit'])) {
    
    $description_val = $_POST['description'];
    if($_POST['availability'] == 'yes') {
      $availability_val = 1;
    }
    else if($_POST['availability'] == 'no') {
      $availability_val = 0;
    }
    $name_val = $_POST['product_name'] ? $_POST['product_name'] : 'NULL';
    $brand_val = $_POST['product_brand'] ? $_POST['product_brand'] : 'NULL';
    $color_val = $_POST['product_color'] ? $_POST['product_color'] : 'NULL';
    $price_val = $_POST['product_price'] ? $_POST['product_price'] : 0;
    
    if (empty($_POST["availability"])) {
      $availabilityerr = "Required";
    }
  
    if (empty($_POST["description"])) {
      $descriptionerr = "Required";
    }
    else if(isset($_POST['autoFillSubmit'])) {
      $availabilityerr = $descriptionerr = "";
      
      $tagged_query = getTagDescription($_POST['description']);
      $name_val = $tagged_query['product'] ? $tagged_query['product'] : $name_val;
      $brand_val = $tagged_query['brand'] ? $tagged_query['brand'] : $brand_val;
      $color_val = $tagged_query['color'] ? $tagged_query['color'] : $color_val;
    }
    
    //In terms of URL, either it should be empty or valid.
    if(isset($_POST['btnSubmit']) and ((!$_POST['image_url'] and isset($_SESSION['product_id'])) or !$imagerr) and !$descriptionerr and !$availabilityerr) {
      
      
      $tagged_query = getTagDescription($_POST['description']);
      
      //name_val should have either previous value or if null then tagged query val.
      
      if($name_val === 'NULL') {
        $name_val = $tagged_query['product'] ? $tagged_query['product'] : $name_val;
      }
      if($brand_val === 'NULL') {
        $brand_val = $tagged_query['brand'] ? $tagged_query['brand'] : $brand_val;
      }
      if($color_val === 'NULL') {
        $color_val = $tagged_query['color'] ? $tagged_query['color'] : $color_val; 
      }
      
      //variables assignment
      if($_POST['image_url']) {
        $image_val = base64_encode(file_get_contents($_POST['image_url']));
      }
      
      
      //setting the queries based on the type of post request
      if(isset($_SESSION['product_id'])) {
        if($_POST['image_url']) {
          $products_query = "UPDATE products SET name = :name, brand = :brand, color = :color, price = :price, description = :description, is_available = :is_available, image = :image WHERE product_id = :product_id";
        }
        else {
          $products_query = "UPDATE products SET name = :name, brand = :brand, color = :color, price = :price, description = :description, is_available = :is_available WHERE product_id = :product_id";
        }
      }
      else {
        $products_query = "INSERT INTO products(name, brand, color, price, description, is_available, image) VALUES (:name, :brand, :color, :price, :description, :is_available, :image)";
      }
      
      
      
      $records = $conn->prepare($products_query);
      $records->bindParam(':name', $name_val);
      $records->bindParam(':brand', $brand_val);
      $records->bindParam(':color', $color_val);
      $records->bindParam(':price', $price_val);
      $records->bindParam(':description', $description_val);
      $records->bindParam(':is_available', $availability_val);
      
      
      
      if(!empty($image_val)) {
        $records->bindParam(':image', $image_val);
      }
      
      if(isset($_SESSION['product_id'])) {
        $records->bindParam(':product_id', $_SESSION['product_id']);
      }
      
      
      
	    //Execution of products query
	    if($records->execute()) {
	      
	      if(isset($_SESSION['product_id'])) {
	        unset($_SESSION['product_id']);
	        unset($_SESSION['image_val']);
	        $_SESSION['updation_flag'] = TRUE;
	        header("Location: /main.php");
	      }
	      
	      //Have to perform insertion in the owns table
	      $owns_query = "INSERT INTO owns(user_id, product_id) VALUES (:user_id, :product_id)";
	      $user_id = $_SESSION['user_id'];
	      $product_id = $conn->lastInsertId();
	      $records = $conn->prepare($owns_query);
	      $records->bindParam(':user_id', $user_id);
	      $records->bindParam(':product_id', $product_id);
	      
	      if($records->execute()) {
	        $_SESSION['insertion_flag'] = TRUE;
	        header("Location: /main.php");
	      }
	      else {
	        $insertionerr = "Error in uploading the product";
	      }
	      
	    }
	    else {
	      
	      if(isset($_SESSION['product_id'])) {
	        $insertionerr = "Error in updating product information";
	      }
	      else {
	        $insertionerr = "Error in uploading the product";
	      }
	      
	      
	    }
      
    }
    
    //Clear image error if updating en existing product and URL is empty
    if(!$_POST['image_url'] and isset($_SESSION['product_id'])) {
      $imagerr = "";
    }
  
  }
  
  
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
                <span class="error" style="color:red"> <?php echo $insertionerr;?></span>
               <div class="row">
                    <div class="col-md-6">
                        
                        <div class="form-group">
                            <br><br>
                            
                            <!-- display image in URL if valid when the check button is clicked -->
                            <?php if(isset($_POST['image_submit']) and !$imagerr) {
                                $image_val = base64_encode(file_get_contents($_POST['image_url']));?>
                                <img height="100" width="100" src="data:image/jpg;base64,<?= $image_val?>" />
                            <?php } ?>
                            
                            <!--For get request from view product page-->
                            <?php if(isset($_GET['product_id'])) { ?>
                              <img height="100" width="100" src="data:image/jpg;base64,<?= $image_val?>" />
                            <?php } ?>
                            
                            <!--If it's the post request from final submit, then old image is shown only if URL is empty-->
                            <?php if(isset($_POST['btnSubmit']) and !$_POST['image_url'] and isset($_SESSION['product_id'])) { ?>
                              <img height="100" width="100" src="data:image/jpg;base64,<?= $_SESSION['image_val']?>" />
                            <?php } ?>
                            
                        </div>
                        

                        <div class="form-group">
                            <label for="description">Description</label>
                            <textarea name="description" class="form-control" placeholder="Your Description" style="width: 100%; height: 150px;" readonly><?= $description_val?></textarea>
                        </div>
                        
                        <div class="form-group">
                          <label for="availability">Availability</label>
                          <label class="radio-inline"><input type="radio" value="yes" name="availability" <?php if ($availability_val === 1){echo "checked";} else{echo "disabled";}?> >Yes</label>
                          <label class="radio-inline"><input type="radio" value="no" name="availability" <?php if ($availability_val === 0){echo "checked";} else{echo "disabled";}?> >No</label>
                        </div>
                        
                        
                        
                    </div>
                    
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_name">Product name</label>
                            <input type="text" name="product_name" class="form-control" placeholder="Name" value="<?= $name_val ?>" readonly />
                        </div>
                        <div class="form-group">
                            <label for="product_brand">Brand</label>
                            <input type="text" name="product_brand" class="form-control" placeholder="Brand" value="<?= $brand_val ?>" readonly />
                        </div>
                        <div class="form-group">
                            <label for="product_color">Color</label>
                            <input type="text" name="product_color" class="form-control" placeholder="Color" value="<?= $color_val?>" readonly />
                        </div>
                        <div class="form-group">
                            <label for="product_price">Price</label>
                            <input type="text" name="product_price" class="form-control" placeholder="Price" value="<?= $price_val?>" readonly />
                        </div>
                        <div>
                            <input type="hidden" name="myEmail" value="<?php echo $email?>" />
                            <input type="submit" value="Message User" />
                        </div>
                  </div>
                </div>
               
            </form>
</div>