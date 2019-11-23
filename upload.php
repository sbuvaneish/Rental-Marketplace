<?php

session_start();

if(!isset($_SESSION['user_id'])) {
  header("Location: /login.php");
}

if($_SERVER["REQUEST_METHOD"] == "GET" and !isset($_GET['product_id'])) {
  unset($_SESSION['image_val']);
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
  
  
  $query = "SELECT u.id from users u, owns o where o.product_id = :product_id and u.id = o.user_id";
  $records = $conn->prepare($query);
  $records->bindParam(':product_id', $_GET['product_id']);
  $records->execute();
  $view_results = $records->fetchAll(PDO::FETCH_ASSOC);
  $id = $view_results[0]["id"];
  
  if($id != $_SESSION['user_id']){
    $url =  "https://".$_SERVER['HTTP_HOST']."/upload_non_editable.php?product_id=".$_GET['product_id'];
    header("Refresh: 0.1;url=".$url);
    return;
  }
  
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
  if(isset($_POST['btnSubmit']) or isset($_POST['autoFillSubmit']) or isset($_POST['colorSubmit'])) {
    
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
    
    if(isset($_POST['colorSubmit'])) {
      $availabilityerr = $descriptionerr = "";
      
      if(!isset($_SESSION['image_val']) and empty($_POST['image_url'])) {
        $imagerr = "Invalid URL, please choose a JPEG or PNG file.";
      }
      else {
        if(!empty($_POST['image_url'])) {
          $image_val = base64_encode(file_get_contents($_POST['image_url']));
          
          $send = addslashes(json_encode($image_val));
          exec("python3.4 /home/ec2-user/environment/project/Rental-Marketplace/color.py \"{$send}\"", $op);

          $result = json_decode($op[0], $assoc=TRUE);
          $color_val = $result[1];
          
        }
        else {
          
          $send = addslashes(json_encode($_SESSION['image_val']));
          $op="";
          exec("python3.4 /home/ec2-user/environment/project/Rental-Marketplace/color.py \"{$send}\"", $op);
          $result = json_decode($op[0], $assoc=TRUE);
          $color_val = $result[1];
          
          // if(strpos($result[0], 'white') !== false) {
          //   $color_val = $result[1];
          // }
          // else {
          //   $color_val = $result[0];
          // }
          
        }
      }
      
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
  
  if(empty($imagerr)) {
    if(isValidURL($_POST['image_url'])) {
      $_SESSION['image_val'] = base64_encode(file_get_contents($_POST['image_url']));
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
            <form enctype="multipart/form-data" method="post" action ="upload.php">
                <h3>Upload or update product information</h3>
                <br>
                <a href="main.php">Back to main page</a>
                <br><br><br>
                <span class="error" style="color:red"> <?php echo $insertionerr;?></span>
               <div class="row">
                    <div class="col-md-6">
                        
                        <div class="form-group">
                            <label for="image">Upload image</label>
                            <span class="error" style="color:red">* <?php echo $imagerr;?></span>
                            
                            <form action="upload.php" method="POST" enctype="multipart/form-data">
                               <input type="text" name="image_url" class="form-control" placeholder="Image URL" value="<?=$_POST['image_url']?>" />
                               <input type="submit" name="image_submit" value="check" />
                            </form>
                            <br><br>
                            
                            <?php if(isset($_SESSION['image_val'])) { ?>
                              <img height="100" width="100" src="data:image/jpg;base64,<?= $_SESSION['image_val']?>" />
                            <?php } ?>
                            
                        </div>
                        

                        <div class="form-group">
                            <label for="description">Description</label>
                            <span class="error" style="color:red">* <?php echo $descriptionerr;?></span>
                            <textarea name="description" class="form-control" placeholder="Your Description" style="width: 100%; height: 150px;"><?= $description_val?></textarea>
                        </div>
                        
                        <div class="form-group">
                          <label for="availability">Availability</label>
                          <span class="error" style="color:red">* <?php echo $availabilityerr;?></span>
                          <label class="radio-inline"><input type="radio" value="yes" name="availability" <?php if ($availability_val === 1){echo "checked";}?> >Yes</label>
                          <label class="radio-inline"><input type="radio" value="no" name="availability" <?php if ($availability_val === 0){echo "checked";}?> >No</label>
                        </div>
                        
                        
                        
                    </div>
                    
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_name">Product name</label>
                            <input type="text" name="product_name" class="form-control" placeholder="Name" value="<?= $name_val ?>" />
                        </div>
                        <div class="form-group">
                            <label for="product_brand">Brand</label>
                            <input type="text" name="product_brand" class="form-control" placeholder="Brand" value="<?= $brand_val ?>" />
                        </div>
                        <div class="form-group">
                            <label for="product_color">Color</label>
                            <input type="text" name="product_color" class="form-control" placeholder="Color" value="<?= $color_val?>" />
                        </div>
                        <div class="form-group">
                            <label for="product_price">Price</label>
                            <input type="text" name="product_price" class="form-control" placeholder="Price" value="<?= $price_val?>" />
                        </div>
                  
                <input type="submit" name="autoFillSubmit" class="btnContact" value="Autofill" style="background-color:orange"/>
                <br><br>
                <input type="submit" name="colorSubmit" class="btnContact" value="Get Color" style="background-color:blue"/>
                <br><br>
                <input type="submit" name="btnSubmit" class="btnContact" value="Submit" style="background-color:green"/>
                  </div>
                    
                </div>
            </form>
            <br>
            <a href="main.php">Back to main page</a>
            <br>
</div>
