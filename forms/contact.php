<?php
// Database connection
$host = 'localhost';
$dbname = 'contact_form';
$username = 'root'; // Default XAMPP username
$password = ''; // Default XAMPP password

try {
  $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
  die("Database connection failed: " . $e->getMessage());
}

// Receiving email address
$receiving_email_address = 'contact@example.com';

if (file_exists($php_email_form = '../assets/vendor/php-email-form/php-email-form.php')) {
  include($php_email_form);
} else {
  die('Unable to load the "PHP Email Form" Library!');
}

// Save form data to database
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $subject = $_POST['subject'];
  $message = $_POST['message'];

  $stmt = $conn->prepare("INSERT INTO messages (name, email, subject, message) VALUES (:name, :email, :subject, :message)");
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':email', $email);
  $stmt->bindParam(':subject', $subject);
  $stmt->bindParam(':message', $message);

  if ($stmt->execute()) {
    echo "Message saved successfully!";
  } else {
    echo "Failed to save the message.";
  }
}

// Email functionality
$contact = new PHP_Email_Form;
$contact->ajax = true;

$contact->to = $receiving_email_address;
$contact->from_name = $_POST['name'];
$contact->from_email = $_POST['email'];
$contact->subject = $_POST['subject'];

/*
$contact->smtp = array(
  'host' => 'example.com',
  'username' => 'example',
  'password' => 'pass',
  'port' => '587'
);
*/

$contact->add_message($_POST['name'], 'From');
$contact->add_message($_POST['email'], 'Email');
$contact->add_message($_POST['message'], 'Message', 10);

echo $contact->send();
?>
