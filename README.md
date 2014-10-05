nfl-gamepass-session-cookie
===========================

This is a simple PHP script that logs into NFL Gamepass and stores a working session cookie in a MySQL table. 
On subsequent requests to the page, it checks if the stored cookie still works. If it does, it returns it. If not, 
it logs in again and stores the new cookie. 

## Install
1. First you need to create the MySQL table. Run the SQL in database_create.sql in the database you'll be using.
2. Edit database_details.php with the MySQL credentials where you created the table.
3. Edit gamepass_cookie.php and change $gamepass->gamepassUsername and $gamepass->gamepassPassword to your NFL Game Pass login.
4. Upload folder to a public folder on your webserver. Ensure that gamepass_cookiejar_file is writeable.

Now whenever you visit nfl-gamepass-session-cookie/gamepass_cookie.php it should return a working JSESSION cookie.
