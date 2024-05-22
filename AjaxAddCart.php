<?php
include("../Connection/Connection.php");
session_start();
$selqry="select * from tbl_booking where mechanic_id='".$_SESSION["mid"]."' and booking_status='0'";
$result=$conn->query($selqry);
if($result->num_rows>0)
{
    $selqry="select MAX(booking_id) as id from tbl_booking where mechanic_id='".$_SESSION["mid"]."' and booking_status='0'";
	$res=$conn->query($selqry);
	$row=$res->fetch_assoc();
	$bid = $row["id"];
    $selqry="select * from tbl_cart where booking_id='".$bid."'and product_id='".$_GET["id"]."' and cart_status='0'";
    $result=$conn->query($selqry);
    if($result->num_rows>0)
    {
            echo "Already Added to Cart";  
    }
    else
    {
        $insQry1="insert into tbl_cart(product_id,booking_id)values('".$_GET["id"]."','".$bid."')";
        if($conn->query($insQry1))
            { 
                echo "Added to Cart";
            }
        else
        {
            echo "Failed";
        }
    }
}
else
{
    $insqry="insert into tbl_booking(mechanic_id) value('".$_SESSION['mid']."')";
    if ($conn->query($insqry))
    {
        $selqry="select MAX(booking_id) as id from tbl_booking where mechanic_id='".$_SESSION["mid"]."' and booking_status='0'";
        $res=$conn->query($selqry);
        if($row=$res->fetch_assoc())
        {
            $bid=$row["id"];
            $insQry1="insert into tbl_cart(product_id,booking_id)values('".$_GET["id"]."','".$bid."')";
            if($conn->query($insQry1))
                { 
                    echo "Added to Cart";
                }
            else
            {
                echo "Failed";
            }
        }

    }
}

?>