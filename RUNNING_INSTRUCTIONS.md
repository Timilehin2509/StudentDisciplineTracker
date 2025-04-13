# Running Instructions for the Student Disciplinary Record Management System

## Prerequisites
- Ensure you have a local server environment set up (e.g., XAMPP, MAMP, or a similar PHP server).
- Make sure you have MySQL installed and running.

## Steps to Run the Project

1. **Clone the Repository**: If you haven't already, clone the repository to your local machine.

2. **Set Up the Database**:
   - Open the `database.sql` file located in the project root.
   - Execute the SQL commands in your MySQL database management tool (e.g., phpMyAdmin) to create the database and tables.

3. **Configure Database Connection**:
   - Open the `includes/db_connect.php` file.
   - Update the following variables with your database credentials:
     ```php
     $servername = "localhost"; // or your server name
     $username = "root"; // your database username
     $password = ""; // your database password
     $dbname = "disciplinary_system"; // the name of the database you created
     ```

4. **Start the Local Server**:
   - If using XAMPP or MAMP, start the Apache and MySQL services.

5. **Access the Application**:
   - First verify your server is running:
     1. Try accessing `http://localhost` (or `http://localhost:8080`)
     2. You should see your server's default page or directory listing
   - If the server is running but the site isn't found:
     1. Check where you placed the project folder - it should be in your server's document root (typically `htdocs` or `www`)
     2. Create a test file `test.php` with `<?php phpinfo(); ?>` and try accessing it
     3. Common solutions:
        - Make sure the folder name in URL matches exactly (case-sensitive)
        - Try different ports (80, 8080, 8888)
        - Check server error logs for specific issues
   - If using XAMPP/MAMP:
     - Default URL would be: `http://localhost:8080/StudentDisciplineTracker/index.php`
     - Or: `http://localhost/StudentDisciplineTracker/index.php` if using port 80

6. **Login**:
   - Use the credentials you set up in the database to log in to the application.

## Additional Notes
- Ensure that all required PHP extensions are enabled in your server configuration.
- If you encounter any issues, check the server error logs for more information.

Feel free to reach out if you have any questions or need further assistance!
