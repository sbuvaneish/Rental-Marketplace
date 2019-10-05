<?php
$imageerr = $descriptionerr = $availability = "";
$image = $description = $availability = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  if (empty($_POST["image"])) {
    $imagerrr = "Image is required";
  } else {
    $image = test_input($_POST["image"]);
  }
  
  if (empty($_POST["description"])) {
    $descriptionerr = "Description is required";
  } else {
    $description = test_input($_POST["description"]);
  }
  
  if (empty($_POST["availability"])) {
    $availabilityerr = "Availability is required";
  } else {
    $availability = test_input($_POST["availability"]);
  }
}

function test_input($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}
?>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="//maxcdn.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!------ Include the above in your HEAD tag ---------->
<link rel="stylesheet" type="text/css" href="assets/css/upload.css">
<div class="container contact-form">
            <form method="post" action = "<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
                <h3>Drop Us a Message</h3>
               <div class="row">
                    <div class="col-md-6">
                        
                        <div class="form-group">
                            <label for="image">Upload image</label>
                            <div class="input-group mb-3">
                              <div class="custom-file">
                                <input type="file" name="image" class="custom-file-input" id="inputGroupFile02">
                                <label class="custom-file-label" for="inputGroupFile02">Choose file</label>
                                <span class="error">* <?php echo $imageerr;?></span>
                              </div>
                            </div>
                        </div>
                        

                        <div class="form-group">
                            <label for="description">Description</label>
                            <span class="error">* <?php echo $descriptionerr;?></span>
                            <textarea name="description" class="form-control" placeholder="Your Description *" style="width: 100%; height: 150px;"></textarea>
                        </div>
                        
                        <div class="form-group">
                          <label for="availability">Availability</label>
                          <span class="error">* <?php echo $availabilityerr;?></span>
                          <label class="radio-inline"><input type="radio" name="availability">Yes</label>
                          <label class="radio-inline"><input type="radio" name="availability">No</label>
                        </div>
                        
                        
                        
                    </div>
                    
                    
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="product_name">Product name</label>
                            <textarea name="product_name" class="form-control" placeholder="Name" style="width: 100%; height: 150px;"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="product_brand">Brand</label>
                            <input type="text" name="product_brand" class="form-control" placeholder="Brand" value="" />
                        </div>
                        <div class="form-group">
                            <label for="product_color">Color</label>
                            <input type="text" name="product_color" class="form-control" placeholder="Color" value="" />
                        </div>
                        <div class="form-group">
                            <label for="product_price">Price</label>
                            <input type="text" name="product_price" class="form-control" placeholder="Price" value="" />
                        </div>
                    </div>
                    
                    
                </div>
            </form>
</div>



<!--



<div class="form-group">
                            <input type="text" name="txtEmail" class="form-control" placeholder="Your Email *" value="" />
                        </div>
                        <div class="form-group">
                            <input type="text" name="txtPhone" class="form-control" placeholder="Your Phone Number *" value="" />
                        </div>
                        <div class="form-group">
                            <input type="submit" name="btnSubmit" class="btnContact" value="Send Message" />
                        </div>
-->