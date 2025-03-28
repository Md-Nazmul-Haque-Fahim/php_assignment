<?php 
include('config/db_connect.php');

$title = $email = $ingredients = $price = '';
$errors = array('email' => '', 'title' => '', 'ingredients' => '', 'price' => '');

if(isset($_POST['submit'])){

	// Check email
	if(empty($_POST['email'])){
		$errors['email'] = 'An email is required <br/>';
	} else {
		$email = $_POST['email'];
		if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
			$errors['email'] = 'Email must be a valid email address';
		}
	}

	// Check title
	if(empty($_POST['title'])){
		$errors['title'] = 'A title is required <br/>';
	} else {
		$title = $_POST['title'];
		if(!preg_match('/^[a-zA-Z\s]+$/', $title)){
			$errors['title'] = 'Title must be letters and spaces only';
		}
	}

	// Check ingredients
	if(empty($_POST['ingredients'])){
		$errors['ingredients'] = 'At least one ingredient is required <br/>';
	} else {
		$ingredients = $_POST['ingredients'];
		if (!preg_match('/^[a-zA-Z\s]+(,\s*[a-zA-Z\s]*)*$/', $ingredients)){
			$errors['ingredients'] = 'Ingredients must be a comma-separated list';
		}
	}

	// Check price
	if(empty($_POST['price'])){
		$errors['price'] = 'A price is required <br/>';
	} else {
		$price = $_POST['price'];
		if(!preg_match('/^\d+(\.\d{1,2})?$/', $price)){
			$errors['price'] = 'Price must be a number with up to 2 decimal places';
		}
	}

	if(array_filter($errors)){
		// There are errors in the form
	} else {
		$email = mysqli_real_escape_string($conn, $_POST['email']);
		$title = mysqli_real_escape_string($conn, $_POST['title']);
		$ingredients = mysqli_real_escape_string($conn, $_POST['ingredients']);
		$price = mysqli_real_escape_string($conn, $_POST['price']);

		// Create SQL
		$sql = "INSERT INTO pizzas(title, email, ingredients, price) VALUES('$title', '$email', '$ingredients', '$price')";

		// Save to db and check
		if(mysqli_query($conn, $sql)){
			// Success
			header('Location: index.php');
		} else {
			echo 'Query error: '.mysqli_error($conn);
		}
	}
}
?>

<!DOCTYPE html>
<html>
<?php include('templates/header.php'); ?>

<section class="container grey-text">
	<h4 class="center">Add a Pizza</h4>
	<form class="white" action="add.php" method="POST">
		<label>Your Email:</label>
		<input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>">
		<div class="red-text"><?php echo $errors['email']; ?></div>

		<label>Pizza Title:</label>
		<input type="text" name="title" value="<?php echo htmlspecialchars($title); ?>">
		<div class="red-text"><?php echo $errors['title']; ?></div>

		<label>Ingredients (comma separated):</label>
		<input type="text" name="ingredients" value="<?php echo htmlspecialchars($ingredients); ?>">
		<div class="red-text"><?php echo $errors['ingredients']; ?></div>

		<label>Price:</label>
		<input type="text" name="price" value="<?php echo htmlspecialchars($price); ?>">
		<div class="red-text"><?php echo $errors['price']; ?></div>

		<div class="center">
			<input type="submit" name="submit" value="Submit" class="btn brand z-depth-0">
		</div>
	</form>
</section>

<?php include('templates/footer.php'); ?>
</html>
