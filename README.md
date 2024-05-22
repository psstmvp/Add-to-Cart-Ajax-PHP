# Shopping Cart Ajax Handler

This PHP script handles the addition of products to a cart for a shopping site. It ensures that the product is not already in the cart before adding it, and it manages booking sessions for a user. The script is designed to be used with Ajax requests for a seamless user experience.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Database Structure](#database-structure)
3. [Installation](#installation)
4. [Usage](#usage)
5. [Code Explanation](#code-explanation)

## Prerequisites

Before using this script, ensure you have the following:

- PHP environment set up on your server.
- MySQL database with the necessary tables (`tbl_booking` and `tbl_cart`).
- A session mechanism in place for managing user sessions, specifically the user's session (`$_SESSION["uid"]`).

## Database Structure

Ensure your database has the following structure:

### Table: `tbl_booking`

| Column          | Type         | Description                               |
|-----------------|--------------|-------------------------------------------|
| `booking_id`    | INT          | Primary key, auto-incremented booking ID. |
| `user_id`       | INT          | ID of the user.                           |
| `booking_status`| INT          | Status of the booking (0 for active).     |
| `booking_amount`| INT          | Total amount after checkout.              |
| `booking_date`  | INT          | Date of booking.                          |

### Table: `tbl_cart`

| Column        | Type         | Description                                |
|---------------|--------------|--------------------------------------------|
| `cart_id`     | INT          | Primary key, auto-incremented cart ID.     |
| `product_id`  | INT          | ID of the product being added.             |
| `booking_id`  | INT          | ID of the associated booking.              |
| `cart_status` | INT          | Status of the cart item (0 for active).    |

## Installation

1. **Clone or Download the Repository**: Place the script in your project directory.

2. **Configure Database Connection**: Update the `Connection.php` file with your database connection details.

```php
<?php
$servername = "your_server_name";
$username = "your_username";
$password = "your_password";
$dbname = "your_database_name";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
```

3. **Session Management**: Ensure sessions are started and the user's ID is stored in `$_SESSION["uid"]`.

## Usage

This script is designed to be called via an Ajax request when a product is added to the cart. Ensure the request includes the product ID in the query string.

### Example Ajax Request

```javascript
function addCart(pid){
    $.ajax({
        url: 'path/to/your/AjaxAddCart.php?pid=' + pid,
        success: function(response) {
            alert(response);
        }
    });
}
```

## Code Explanation

### Step-by-Step Breakdown

1. **Include Database Connection and Start Session**

   ```php
   include("../Connection/Connection.php");
   session_start();
   ```

2. **Check for Existing Booking**

   - Query `tbl_booking` to check if there's an active booking for the current user.
   - If a booking exists, fetch the latest booking ID for the user.

   ```php
   $selqry = "select * from tbl_booking where user_id='".$_SESSION["uid"]."' and booking_status='0'";
   $result = $conn->query($selqry);
   ```

3. **If Booking Exists**

   - Retrieve the booking ID.

   ```php
   $selqry = "select MAX(booking_id) as id from tbl_booking where user_id='".$_SESSION["uid"]."' and booking_status='0'";
   $res = $conn->query($selqry);
   $row = $res->fetch_assoc();
   $bid = $row["id"];
   ```

4. **Check if Product is Already in Cart**

   - Query `tbl_cart` to check if the product is already added to the cart within the active booking.

   ```php
   $selqry = "select * from tbl_cart where booking_id='".$bid."' and product_id='".$_GET["id"]."' and cart_status='0'";
   $result = $conn->query($selqry);
   ```

5. **Add Product to Cart**

   - If the product is not in the cart, insert it into `tbl_cart`.

   ```php
   if ($result->num_rows == 0) {
       $insQry1 = "insert into tbl_cart(product_id, booking_id) values('".$_GET["id"]."', '".$bid."')";
       if ($conn->query($insQry1)) {
           echo "Added to Cart";
       } else {
           echo "Failed";
       }
   } else {
       echo "Already Added to Cart";
   }
   ```

6. **If No Booking Exists**

   - Create a new booking for the user.

   ```php
   else {
       $insqry = "insert into tbl_booking(user_id) value('".$_SESSION['mid']."')";
       if ($conn->query($insqry)) {
           $selqry = "select MAX(booking_id) as id from tbl_booking where user_id='".$_SESSION["uid"]."' and booking_status='0'";
           $res = $conn->query($selqry);
           if ($row = $res->fetch_assoc()) {
               $bid = $row["id"];
               $insQry1 = "insert into tbl_cart(product_id, booking_id) values('".$_GET["id"]."', '".$bid."')";
               if ($conn->query($insQry1)) {
                   echo "Added to Cart";
               } else {
                   echo "Failed";
               }
           }
       }
   }
   ```

7. **Return Response**

   - Send an appropriate response back to the client based on the outcome of the database operations.

## Conclusion

This script ensures that products are added to the cart in a controlled manner, preventing duplicates and managing session-based bookings. It is essential for shopping sites that require precise and user-friendly cart management.