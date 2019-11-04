

<script type="text/javascript">
  function submitForm(action) {
    var form = document.getElementById('form1');
    form.action = action;
    form.submit();
  }
</script>

<!DOCTYPE html>
<html>
<title>W3.CSS</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">

<style>
    * {
  box-sizing: border-box;
}

/* Style the search field */
form.example input[type=text] {
  padding: 10px;
  font-size: 17px;
  border: 1px solid grey;
  float: left;
  width: 80%;
  background: #f1f1f1;
}

/* Style the submit button */
form.example button {
  float: left;
  width: 20%;
  padding: 10px;
  background: #2196F3;
  color: white;
  font-size: 17px;
  border: 1px solid grey;
  border-left: none; /* Prevent double borders */
  cursor: pointer;
}

form.example button:hover {
  background: #0b7dda;
}

/* Clear floats */
form.example::after {
  content: "";
  clear: both;
  display: table;
}
</style>
<body>
  <br>
  <br>
  <form id="form1" >
<!--    <input type="button" onclick="submitForm('list_messages.php')" value="List" style="padding: 50x 100px"/>
-->    <input type="button" onclick="submitForm('message.php')" value="Send" style="padding: 50x 100px"/>
    <!-- The form -->
    <form class="example" action="list_mess_user.php" method = "POST">
      <input type="text" placeholder="Search.." name="search" value="<?=$_POST['search']?>"> 
      <button type="submit"><i class="fa fa-search"></i></button>
    </form>
  </form>
</body>





