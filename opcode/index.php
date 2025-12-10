<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>JCS Membership Form</title>
</head>
<body>
    <h1>JCS Membership Form</h1>
    <form action="generate.php" method="post">
        <label for="name">Full Name:</label>
        <input type="text" id="name" name="name" required><br><br>
        
        <label for="ic">IC Number:</label>
        <input type="text" id="ic" name="ic" required><br><br>
        
        <label for="dob">Date of Birth:</label>
        <input type="date" id="dob" name="dob" required><br><br>
        
        <label for="address">Address:</label>
        <textarea id="address" name="address" required></textarea><br><br>
        
        <label for="phone">Phone Number:</label>
        <input type="tel" id="phone" name="phone" required><br><br>
        
        <label for="email">Email:</label>
        <input type="email" id="email" name="email" required><br><br>
        
        <input type="submit" value="Generate PDF">
    </form>
</body>
</html>