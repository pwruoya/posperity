<?php
session_start();
if ($_SERVER["REQUEST_METHOD"] == "GET") {
    $_SESSION['selectedId'] = $_GET['product_id'];
}
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "posperity";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare and bind the SQL query with a placeholder for the ID
$sql = "SELECT `product_id`, `name`, `description`, `price`, `quantity`, `img_url`, `user`, `merchant` FROM `product` WHERE `product_id` = ?";
$stmt = $conn->prepare($sql);

// Bind the ID variable to the prepared statement
$id = intval($_SESSION['selectedId']);
$stmt->bind_param("i", $id);

// Execute the statement
$stmt->execute();

// Get the result
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // Fetch the first (and only) row since product_id should be unique
    $row = $result->fetch_assoc();

    // Assign fetched values to PHP variables
    $productId = $row["product_id"];
    $name = $row["name"];
    $description = $row["description"];
    $price = $row["price"];
    $quantity = $row["quantity"];
    $imgUrl = $row["img_url"];
    $user = $row["user"];
    $merchant = $row["merchant"];
} else {
    echo "No product found with the specified ID";
}

// Close the statement and connection
$stmt->close();
$conn->close();


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="add.css">
    <script src="https://kit.fontawesome.com/f7e75704ad.js" crossorigin="anonymous"></script>
    <!-- <script type="module" src="upload_image.js"></script> -->

</head>

<body>
    <header>
        <h1>
            <?php
            if (isset($_SESSION['merchantname'])) {
                echo $_SESSION['merchantname'];
            }
            ?>
        </h1>
    </header>
    <div class="container">
        <h2>Edit product details</h2>
        <form id="productForm" action="editInventory.php" method="POST">

            <?php
            // Assuming you have fetched the product details and stored them in $row
            if (!empty($productId)) {
            ?>
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" value="<?php echo $name; ?>" required>

                <label for="description">Description:</label>
                <textarea id="description" name="description" rows="3" required><?php echo $description; ?></textarea>

                <label for="price">Price:</label>
                <input type="number" id="price" name="price" step="0.01" value="<?php echo $price; ?>" required>

                <label for="quantity">Quantity:</label>
                <input type="number" id="quantity" name="quantity" value="<?php echo $quantity; ?>" required>

                <label for="img_url">Change product image:</label>
                <input type="text" id="img_url" name="img_url" value="<?php echo $imgUrl; ?>" style="row-gap: 3;">

                <!-- Buttons for image upload -->
                <input type="file" id="imageInput" accept="image/*" style="display: none;">
                <div class="spacebtn">
                    <button type="button" onclick="openFilePicker()">Choose Image</button>
                    <button type="button" onclick="openCamera()">Capture Image</button>
                </div>
                <br>
                <br>
                <div id="imgdiv">
                    <img id="img" src="<?php echo $imgUrl; ?>" alt="">
                </div>
                <br>
                <button type="button" onclick="upldClick()" id="upld">upload selected image</button>
                <br>
                <br>
                <div class="spacebtn">
                    <button type="button" onclick="toInventory()"><i class="fa-solid fa-angles-left" style="color: #ffffff;"></i></button>
                    <input type="submit" value="Update Product">
                </div>
            <?php
            } else {
                echo "No product found with the specified ID";
            }
            ?>

        </form>


        <?php
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "posperity";

        // Create connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        // Check connection
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Check if all required fields are present
            if (isset($_POST['name']) && isset($_POST['description']) && isset($_POST['price']) && isset($_POST['quantity']) && isset($_POST['img_url'])) {

                // Sanitize inputs to prevent SQL injection
                $name = htmlspecialchars($_POST['name']);
                $description = htmlspecialchars($_POST['description']);
                $price = floatval($_POST['price']); // Convert to float for price
                $quantity = intval($_POST['quantity']); // Convert to integer for quantity
                $img_url = htmlspecialchars($_POST['img_url']);
                // $product_id = intval($_POST['product_id']); // Convert to integer for product_id

                // Additional sanitization and validation can be added here

                // Prepare and bind parameters for the SQL statement to update product details
                $sql = "UPDATE `product` SET `name`=?, `description`=?, `price`=?, `quantity`=?, `img_url`=?, `user`=?, `merchant`=? WHERE `product_id`=?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ssdissii", $name, $description, $price, $quantity, $img_url, $user, $merchant, $productId);

                // Example user and merchant values (adjust as needed)
                $user = $_SESSION['userid'];
                $merchant = $_SESSION['merchantid']; // Assuming you store the merchant ID in a session variable

                // Execute the SQL statement to update product details
                if ($stmt->execute()) {
                    echo "Product updated successfully.";
                    // You can redirect the user to another page if needed
                    header("Location: inventory.php");
                    // exit();
                } else {
                    echo "Error updating product: " . $stmt->error;
                }

                // Close the statement
                $stmt->close();
            } else {
                echo "All fields are required.";
            }
        }

        // Close the connection
        $conn->close();
        ?>
    </div>
    <footer>
        <p style="font-size: 10px;">
            &copy; 2024 posperity,all rights reserved</p>
    </footer>
    <script>
        function toInventory() {
            // Redirect to another page (replace 'page-url' with the actual URL)
            window.location.href = 'inventory.php';
        }

        function upldClick() {
            console.log("Upload selected image clicked");
        }

        // Function to open file picker for storage selection
        function openFilePicker() {
            document.getElementById('imageInput').click();
        }



        // Function to open camera for picture capture
        function openCamera() {
            // Access the user's camera
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    const videoElement = document.createElement('video');
                    videoElement.srcObject = stream;
                    videoElement.play();

                    // Append the video element to the document for preview
                    document.getElementById("imgdiv").appendChild(videoElement);

                    // Create a button for capturing the image
                    const captureButton = document.createElement('button');
                    captureButton.id = 'upld';
                    captureButton.textContent = 'Capture Image';
                    captureButton.onclick = () => {
                        // Create a canvas element to capture the image
                        const canvas = document.createElement('canvas');
                        canvas.width = videoElement.videoWidth;
                        canvas.height = videoElement.videoHeight;
                        const context = canvas.getContext('2d');

                        // Draw the video frame onto the canvas
                        context.drawImage(videoElement, 0, 0, canvas.width, canvas.height);

                        // Convert the canvas content to a data URL (base64-encoded image)
                        const dataURL = canvas.toDataURL('image/jpeg');

                        // Save the data URL to localStorage or sessionStorage
                        localStorage.setItem('capturedImage', dataURL);

                        // Retrieve the captured image data from localStorage
                        const capturedImageDataURL = localStorage.getItem('capturedImage');
                        console.log('Image saved to localStorage');
                        // Display the captured image in an <img> element
                        if (capturedImageDataURL) {
                            const imageElement = document.getElementById("img");
                            imageElement.src = capturedImageDataURL;
                            console.log('upload shown.');
                        } else {
                            console.error('No captured image data found.');
                        }

                        // Stop the video stream and close the camera
                        stream.getTracks().forEach(track => track.stop());
                        videoElement.remove();
                        captureButton.remove();

                        // Navigate to a new page or perform other actions
                        // Replace with your desired page
                    };

                    // Append the capture button to the document
                    document.getElementById("imgdiv").appendChild(captureButton);
                })
                .catch(error => {
                    console.error('Error accessing camera:', error);
                });
        }

        document.addEventListener('DOMContentLoaded', () => {
            const imagePicker = document.getElementById('imageInput');

            // Event listener for when an image is selected using the file picker
            imagePicker.addEventListener('change', handleImageSelection);

        });

        function handleImageSelection(event) {
            const selectedFile = event.target.files[0]; // Get the selected file (image)

            if (selectedFile) {
                // Create a FileReader object to read the file as a data URL
                const reader = new FileReader();

                // Event listener for when the FileReader has successfully read the file
                reader.onload = function(event) {
                    const imageDataURL = event.target.result; // Get the data URL of the selected image
                    localStorage.setItem('capturedImage', imageDataURL); // Save the image data URL to localStorage
                    console.log('Image saved to localStorage');
                    const capturedImageDataURL = localStorage.getItem('capturedImage');
                    const imageElement = document.getElementById("img");
                    imageElement.src = capturedImageDataURL;

                };

                // Read the selected file as a data URL
                reader.readAsDataURL(selectedFile);
            }
        }
    </script>
</body>
<!--  -->
<script type="module">
    import {
        initializeApp
    } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-app.js";
    import {
        getStorage,
        ref,
        uploadString
    } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-storage.js";

    import {
        getDownloadURL
    } from "https://www.gstatic.com/firebasejs/10.8.1/firebase-storage.js";


    const firebaseConfig = {
        apiKey: "AIzaSyDdsL0Sf4IVmqlX05cx5gZ1wqrqWRC4j2c",
        authDomain: "posperity.firebaseapp.com",
        projectId: "posperity",
        storageBucket: "posperity.appspot.com",
        messagingSenderId: "210695590267",
        appId: "1:210695590267:web:7f443b818a06498882edde",
        measurementId: "G-ZY75SZ2M0J"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const storage = getStorage(app);

    // Function to upload file from Local Storage to Firebase Storage
    function uploadFileFromLocalStorage(storageRef, localStorageKey) {
        const fileData = localStorage.getItem(localStorageKey);
        const button = document.getElementById('upld');
        const urltt = document.getElementById("img_url")

        if (fileData && button) {
            // Disable the button while processing
            button.disabled = true;
            button.textContent = 'Processing...';
            try {
                // Upload file data to Firebase Storage
                uploadString(storageRef, fileData, 'data_url')

                    .then((snapshot) => {
                        console.log('Uploaded file successfully:', snapshot.ref.fullPath);
                        // Add further logic (e.g., store download URL in database)
                        return getDownloadURL(snapshot.ref); // Return the promise for chaining
                    })
                    .then((url) => {
                        console.log('Download URL:', url);
                        // Set the download URL in the specified target element
                        urltt.value = url;
                        // Optionally, you can use the URL to fetch or display the file in your application

                        // Enable the button and update its text
                        button.textContent = 'Upload Complete';
                    });
            } catch (error) {
                button.textContent = 'Upload Failed';
            }

        } else {
            console.error('No file data found in Local Storage for key:', localStorageKey);
        }
    }






    var prod_name = "<?php echo intval($_GET['product_id']); ?>";

    const storageRef = ref(storage, 'images/' + prod_name + '.txt'); // Specify the file path or name in Firebase Storage
    const localStorageKey = 'capturedImage'; // Key used to store the file data in Local Storage
    const targetElementId = 'img_url'; // ID of the target element to set the download URL
    // const buttonId = 'upld'; // ID of the button to modify



    document.getElementById('upld').addEventListener(
        'click', () => {
            uploadFileFromLocalStorage(storageRef, localStorageKey);
        }
    )
</script>

</html>