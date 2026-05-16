<?php

$email = strtolower(trim($_GET['email']));

$existingEmails = [
    "admin@gmail.com",
    "test@gmail.com",
    "smart@gmail.com"
];

if(in_array($email, $existingEmails)){

    echo "Email already exists";

}
else{

    echo "Email available";

}

?>
