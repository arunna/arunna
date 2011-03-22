<h1>Arunna</h1>
<p>Arunna is a solution that creates a place where social media, networking and community collaborate. You and your customers can have the option to connect and share content online. Our platform gives the opportunity for both hosted community and white label self hosted sites to take the advantage to express themselves. With arunna, small organization to large enterprises can build brand awareness, engage their community and much more. That's what we call connecting the clouds. Find out more at 
<a href="http://arunna.com">http://arunna.com</a></p>

<p><strong><em>WARNING: Arunna is in early alpha. There may be bugs and security risks involved in running it on your web server. PROCEED AT YOUR OWN RISK!</em></strong></p>

<h2>SYSTEM REQUIREMENTS</h2>
<ul>
<li>PHP 5.2 or higher with cURL, GD, and mod rewrite driver enabled</li>
<li>MySQL 5.0.3 or higher</li>
</ul>

<h2>INSTALLATION</h2>
<p>Please follow these steps to install arunna on your server</p>

<h3>Download the files</h3>
<ul>
<li>Click the Download Button on the top of this page</li>
<li>Choose the file type that you want zip or .tar.gz</li>
<li>Extract the file into your accesable web folder (I suggest you to create a sub folder named arunna). </li>
</ul>

<h3>Database Configuration</h3>
<ul>
<li>Open your MySQL and then create your new database name</li>
<li>Import the database file at "/db/arunna_alpha.sql" into your new database name</li>
<li>Once the import process done, go to <em>lumonata_meta_data</em> table and then change the <em>site_url</em> meta name. For example change it to "localhost/arunna"</li>
<li>Open <em>lumonata_config.php</em> file and change your database name user and password</li>
</ul>

<h3>Administrator Access</h3>
<ul>
<li>When you finish configure your database, now you can access Arunna on your active web server (http://localhost/arunna) </li>
<li>To access the administrator area, please visit http://localhost/arunna/lumonata-admin/</li>
<li>The default username and password are: username: admin, password:1234567</li>
</ul>	

<h2>DISCUSSION</h2>
<p>More discussion and support about Arunna please visit and post it to <a href="http://groups.google.com/group/arunna/">Arunna milist at Google Group</a></p>

<h2>LICENSE</h2>
<p>Arunna's source code is licensed under the <a href="http://www.gnu.org/licenses/gpl.html">GNU General Public License</a></p>

<h2>EXTERNAL LIBRARIES</h2>
<ul>
<li><a href="http://openinviter.com/">Openinviter</a></li>
<li><a href="http://colorpowered.com/colorbox/">Colorbox</a></li>
<li><a href="http://fancybox.net/">FancyBox</a></li>
</ul>	