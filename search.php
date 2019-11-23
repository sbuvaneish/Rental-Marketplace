<?php

session_start();

if(!isset($_SESSION['user_id'])) {
  header("Location: /login.php");
}

unset($_SESSION['product_id']);

require 'database.php';

$error = "";

$email = $_SESSION["email"];

$name_query = "SELECT username FROM users";
$name_records = $conn->prepare($name_query);
$name_records->execute();
$name_results = $name_records->fetchAll(PDO::FETCH_ASSOC);


//Obtaining the tagged product, brand and color from Commerce Tagger if search query is not empty
if(isset($_POST['search_submit']) and $_POST['search']) {
  
  require 'tagger.php';
  $tagged_query = getTagDescription($_POST['search']);
  
  $color = $tagged_query['color'];
  $brand = $tagged_query['brand'];
  $product = $tagged_query['product'];
  
  
  //Getting optional filters
  $price = $availability = $renter = $user_rating = ""; 
  
  if(!empty($_POST['price'])) {
    $price = max(floatval($_POST['price']), 0);
  }
  if($_POST['availability'] == 'yes') {
    $availability = "1";
  }
  else if($_POST['availability'] == 'no') {
    $availability = "0";
  }
  
  $renter = $_POST['search_drop'] ? $_POST['search_drop'] : $_POST['renter'];
  
  
  $search_query = "SELECT users.username, products.product_id, products.image, products.name, products.brand, products.color, products.price, products.description, products.is_available, owns.created_at AS datetime FROM products JOIN owns ON owns.product_id = products.product_id JOIN users ON users.id = owns.user_id WHERE products.name = :product AND (users.username LIKE CONCAT('%', :renter, '%')) AND (owns.user_id <> :this_id)";
  
  if(isset($_POST['availability'])) {
    $search_query = $search_query . " AND (products.is_available = :is_available)";
  }
  if(!empty($_POST['price'])) {
    $search_query = $search_query . " AND (products.price IS NULL or (products.price <= :price))";
  }
  
  
  $search_records = $conn->prepare($search_query);
  $search_records->bindParam(':product', $product);
  $search_records->bindParam(':renter', $renter);
  $search_records->bindParam(':this_id', $_SESSION['user_id']);
  
  if(isset($_POST['availability'])) {
    $search_records->bindParam(':is_available', $availability);  
  }
  if(!empty($_POST['price'])) {
    $search_records->bindParam(':price', $price);
  }
  
  $search_records->execute();
  $search_results = $search_records->fetchAll(PDO::FETCH_ASSOC);
  
  
  
  
  $send_dict = [];
  
  foreach($search_results as $row) {
    
    array_push($send_dict, array('product_id' => $row['product_id'], 'brand' => $row['brand'], 'color' => $row['color']));
    
  }
  
  
  
  
  $json_search_results = addslashes(json_encode($send_dict));
  $json_search = addslashes(json_encode($tagged_query));
  $op = "";
  exec("python3 /home/ec2-user/environment/project/Rental-Marketplace/sort.py \"{$json_search_results}\" \"{$json_search}\"", $op);
  $ranked_results = json_decode($op[0],$assoc=TRUE);
  
  
  
  $new_ranked_results = [];
  
  
  foreach($ranked_results as $row) {
    
    for($i=0; $i<count($search_results); $i++) {
      if($search_results[$i]['product_id'] == $row['product_id']) {
        array_push($new_ranked_results, $search_results[$i]);
        break;
      }
    }
    
  }
  

  

}
else if(isset($_POST['search_submit'])) {
  $error = "Enter valid search query";
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <title>Bootstrap Example</title>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/css/bootstrap.min.css">

  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.0/js/bootstrap.min.js"></script>

  <style>
    /* Set height of the grid so .sidenav can be 100% (adjust if needed) */
    .row.content {height: 1500px}
    
    /* Set gray background color and 100% height */
    .sidenav {
      background-color: #f1f1f1;
      height: 100%;
    }
    
    /* Set black background color, white text and some padding */
    .footer {
      background-color: #555;
      color: white;
      padding: 15px;
    }
    
    /* On small screens, set height to 'auto' for sidenav and grid */
    @media screen and (max-width: 767px) {
      .sidenav {
        height: auto;
        padding: 15px;
      }
      .row.content {height: auto;} 
    }
    
    .product-grid {
    border: 1px solid #CEEFFF;
    margin-bottom: 20px;
    margin-top: 10px;
    background: #fff;
    padding: 6px;
    box-shadow: 0 1px 1px 0 rgba(21,180,255,0.5);
      
    }
     
 
.product-grid .product-image {
    background-color: #EBF8FE;
    display: block;
    overflow: hidden;
    position: relative;
    border: 1px solid #CEEFFF;
    text-align: center;
    margin-left: auto;
  margin-right: auto;
 
}
 
 
 
.product-grid .product-content {
    border-bottom: 1px solid #dfe5e9;
    padding-bottom: 17px;
    padding-left: 16px;
    padding-top: 16px;
    position: relative;
    background: #fff
}
 
 
 

    
    
  </style>
</head>
<body>
    
    

<div class="container-fluid">
  <div class="row content">
    <div class="col-sm-3 sidenav">
        <a href="main.php"><h4 style="color:green">Search Product Page</h4></a>
      <span style="float:right;color:green">Welcome, <?=$_SESSION['email']?></span>
      
      <br>
      <a href="main.php">Back to main page</a>
      <br><br><br>
      
      <form action="/search.php" method="post">
          <input type="text" placeholder="Search.." name="search" value="<?=$_POST['search']?>">
          <span class="error" style="color:red">* <?php echo $error;?></span>
          
      
      <br>
     
      <ul class="nav nav-pills nav-stacked">
          <li>Price <input type="text" name="price" value="<?=$_POST['price']?>"></li>
          <li>Availability 
          <label class="radio-inline"><input type="radio" name="availability" value="yes" <?php if ($availability === "1"){echo "checked";}?> >Yes</label>
          <label class="radio-inline"><input type="radio" name="availability" value="no" <?php if ($availability === "0"){echo "checked";}?> >No</label></li>
          <li onclick="myFunction()" class="dropbtn" >Renter Name
           <div id="myDropdown" class="dropdown-content">
          <input type="text" placeholder="Search.." name="search_drop" value="<?= $_POST['renter'] ? $_POST['renter'] : $_POST['search_drop'];?>" id="myInput" onkeyup="filterFunction()">
          <select name="renter">
            <option value="">Select...</option>
          <?php for($index = 0; $index < count($name_results); $index++) { ?>
          
            <option value="<?=$name_results[$index]["username"]?>"><?=$name_results[$index]["username"]?></option>
          
          <?php } ?>
          </select>
          </div>
          </li>
          <script>
            function filterFunction() {
                var input, filter, ul, li, a, i;
                input = document.getElementById("myInput");
                filter = input.value.toUpperCase();
                div = document.getElementById("myDropdown");
                a = div.getElementsByTagName("option");
                for (i = 0; i < a.length; i++) {
                  txtValue = a[i].textContent || a[i].innerText;
                  if (txtValue.toUpperCase().indexOf(filter) > -1) {
                    a[i].style.display = "";
                  } else {
                    a[i].style.display = "none";
                  }
                }
              } 
          </script>
      </ul><br>
        <input type="submit" name="search_submit" value="Submit"/>
      </form>
      
    </div>

    <div class="col-sm-9">
      <?php if(isset($_POST['search_submit'])) { ?>
      <h1>Search Results: <?=count($new_ranked_results);?></h1>
      <?php } ?>
      
      <?php foreach($new_ranked_results as $record) : ?>
      
      
      <div class="row">
        <div class="col-md-3 col-sm-6">
            <div class="product-grid">
              
                
                <div class="product-image">
                  <img height="100" width="100" src="data:image/jpg;base64,<?php echo $record["image"]?>" />
                </div>
              
                
                <div class="product-content">
                    Username: <?= $record["username"] ?><br>
                    Description: <?= $record["description"] ?><br>
                    Availability: <?= ($record["is_available"]) ? "Yes" : "No" ?><br>
                    Date of Upload: <?= explode(" ", $record["datetime"])[0] ?>
                    <a href="upload_non_editable.php?product_id=<?=$record['product_id']?>" target="_blank" class="list-group-item list-group-item-action active" style="background-color:orange">More info!</a>
                    <br>
                </div>
                <br><br>
            </div>
        </div>
      </div>
      
      
      <?php endforeach ?>
    </div>
  </div>
</div>


</body>
</html>
