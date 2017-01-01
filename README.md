# atrontoc
A simple Table of Contents file generator for the Turtle Beach Audiotron written in PHP

Dear Audiotron users, you're welcome to use and improve this script as you like. I've used a few of the existing TOC generators and I decided to write a simple one with PHP. 
The script needs getid3, which you can get from http://getid3.sourceforge.net/ or if you are using Ubuntu/Debian, install it with the following command:<br />
<code>sudo apt install php-getid3</code><br/>
Afterwards you can use it like this:
<code>php atrontoc.php /path/to/music/files</code><br />
The script will rip off the base path from the string and work with relative paths to create an atrontc.vtc file in the aforementioned path.
