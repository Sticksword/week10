<?php
require_once('../private/initialize.php');
require_login();

$errors = array();
$territory_name = "";

if(is_post_request() && request_is_same_domain()) {
  ensure_csrf_token_valid();

  // Confirm that values are present before accessing them.
  if(isset($_POST['territory_name'])) { $territory_name = $_POST['territory_name']; }

  error_log($territory_name);
  $results = find_territories_by_name_vulnerable($territory_name);
}

?>
<?php $page_title = 'Staff: New Territory'; ?>
<?php include(SHARED_PATH . '/staff_header.php'); ?>

<h1> Search by territory name [DEPRECATED] </h1>
<form action="deprecated.php" method="post">
  <?php echo csrf_token_tag(); ?>
  Territory name:<br />
  <input type="text" name="territory_name" value="<?php echo h($territory_name); ?>" /><br />
  <input type="submit" name="submit" value="Submit"  />
</form>
<?php
if(is_post_request() && request_is_same_domain()) {
  while($result = db_fetch_assoc($results)) {
    echo join(", ", $result);
    echo "\n";
  }
}
?>


</div>

<?php include(SHARED_PATH . '/footer.php'); ?>
