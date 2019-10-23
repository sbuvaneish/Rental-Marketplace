<script type="text/javascript">
  function submitForm(action) {
    var form = document.getElementById('form1');
    form.action = action;
    form.submit();
  }
</script>

<form id="form1">
  <input type="button" onclick="submitForm('list_messages.php')" value="List" />
  <input type="button" onclick="submitForm('message.php')" value="Send" />
</form>