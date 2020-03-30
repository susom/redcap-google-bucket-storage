This code will be a little confusing because I started off trying to just use a token to
access my bucket.  I was able to do it through Postman so I started implementing it here.
But then I realized that I think I need to use a signedURL because of CORS. So there is reference
to tokens in the code but I am not actually using them.  I am just retrieving a signedURL.

This is the CORS page that I got stuck on when trying to upload or download files:

https://cloud.google.com/storage/docs/configuring-cors#configure-cors-bucket

I could not make the preflight check work since I was not running a secure local server.

The only code that is usable is the PickFile.php code which will allow you to pick a file
from your computer and upload it (although most of that is commented out. I started
very simple).  This code has the reading of the file commented out since
I was only trying to get something working.

Also, the code to create the signed URL is commented out because I focused on uploading
a text document up to my bucket.  Instead, I just retrieved a signed URL from my google sandbox
which performs the timed URL updates for me.
