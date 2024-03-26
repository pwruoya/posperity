<?php
session_start();
include 'dbconfig.php';
include "redisconnect.php";

// Select the last inserted product ID from the product table
$sql = "SELECT MAX(product_id) as last_product_id FROM product";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // Output data of each row
    while ($row = $result->fetch_assoc()) {
        $_SESSION['lastProductId'] = $row["last_product_id"];
        // echo "Last inserted product ID: " . $lastProductId;
        // You can store $lastProductId in a PHP variable for further use
    }
} else {
    echo "No products found";
}

$conn->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Item</title>
    <link rel="stylesheet" href="add.css">
    <script src="title.js"></script>
    <script src="https://kit.fontawesome.com/f7e75704ad.js" crossorigin="anonymous"></script>
    <!-- <script type="module" src="upload_image.js"></script> -->

</head>

<body>
    <header>
        <h1>
            Add item to Inventory
        </h1>
    </header>
    <div class="container">
        <h2>Add New Item</h2>
        <form id="productForm" action="add_product.php" method="POST">

            <label for="name">Name:</label>
            <input type="text" id="name" name="name" required>

            <label for="description">Description:</label>
            <textarea id="description" name="description" rows="3" required></textarea>

            <label for="price">Price:</label>
            <input type="number" id="price" name="price" step="0.01" required>

            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" required>

            <label for="img_url">Add product image:</label>
            <input type="text" id="img_url" name="img_url" style="display: none;">


            <!-- Buttons for image upload -->
            <input type="file" id="imageInput" accept="image/*" style="display: none;">
            <div class="spacebtn">
                <button type="button" onclick="openFilePicker()">Choose Image</button>
                <button type="button" onclick="openCamera()">Capture Image</button>
            </div>
            <br>
            <br>
            <div id="imgdiv">
                <img id="img" src="" alt="">
            </div>
            <br>
            <button type="button" id="upld">upload selected image</button>
            <br>
            <br>
            <div class="spacebtn">
                <button type="button" onclick="toInventory()"><i class="fa-solid fa-angles-left" style="color: #ffffff;"></i></i></button>
                <input type="submit" value="Add Product">
            </div>
        </form>

        <?php
        include 'dbconfig.php';
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

                // Additional sanitization and validation can be added here


                // Example user and merchant values (adjust as needed)
                if ($redis->exists('merchantid')) {
                    $user = $redis->get('userid');
                    $merchant = $redis->get('merchantid');

                    // Prepare and bind parameters for the SQL statement
                    $sql = "INSERT INTO product (name, description, price, quantity, img_url, user_id, merchant_id) VALUES (?, ?, ?, ?, ?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ssdissi", $name, $description, $price, $quantity, $img_url, $user, $merchant);



                    // Execute the SQL statement
                    if ($stmt->execute()) {
                        echo "Item added successfully.";
                        // You can redirect the user to another page if needed
                        // header("Location: inventory.php");
                        // exit();
                    } else {
                        echo "Error adding item: " . $stmt->error;
                    }

                    // Close the statement and connection
                    $stmt->close();
                } else {
                    echo 'mid empty';
                }
                $conn->close();
            } else {
                echo "All fields are required.";
            }
        }
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

        // Function to open file picker for storage selection
        function openFilePicker() {
            document.getElementById('imageInput').click();
        }



        // Function to open camera for picture capture
        function openCamera() {
            const constraints = {
                video: {
                    facingMode: 'environment'
                } // Use the back camera
            };

            navigator.mediaDevices.getUserMedia(constraints)
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
    function uploadFileFromLocalStorage(storageRef, localStorageKey, targetElementId, buttonId) {
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






    var prod_name = "<?php echo $_SESSION['lastProductId'] + 1; ?>";
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