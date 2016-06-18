

		Single File PHP Gallery 4.1.0 (SFPG)

		See END USER LICENSE AGREEMENT for commercial use

		Released: 21-July-2010
		http://sye.dk/sfpg/
		By Kenny Svalgaard


____________________________________________________________
WHAT IS IN THIS FILE

 * WHAT IS IN THIS FILE (You are here :-)
 * WHAT IS SINGLE FILE PHP GALLERY?
 * END USER LICENSE AGREEMENT / EULA
 * FEATURES
 * REQUIREMENTS
 * HOW TO USE / QUICK START
 * TIPS / FAQ
 * SUPPORT / REQUESTS / HELP / CONTACT
 * KNOWN ISSUES
 * CONFIGURATION INFORMATION


____________________________________________________________
WHAT IS SINGLE FILE PHP GALLERY?

Single File PHP Gallery is a web gallery in one single file. All you have to do is copy the script to any directory containing images to make a gallery. Sub directories will be sub galleries. Thumbnails for images and directories are generated automatically. Descriptions for galleries and images can be added by making a simple text file.
Single File PHP Gallery does not require any configuration or programming skills to use.

You can see how it looks in the online demo here:

  http://sye.dk/sfpg/


____________________________________________________________
END USER LICENSE AGREEMENT / EULA

For private non commercial use Single File PHP Gallery can be used for free. When used commercially a donation for at least 10$ must be made per domain where it is used.

You are of cause still more than welcome to donate if you like the gallery, even though you only use it privately.

Under no circumstances can Single File PHP Gallery or any part of it be distributed or sold, or be part of another work that is being distributed or sold.

Making a donation:
Domain name must be clearly stated in the donation, otherwise the donation will not grant use of the script under donating conditions. Donations are not refundable.

Use the PayPal donate button on the page for donations:
  http://sye.dk/sfpg/

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.


____________________________________________________________
FEATURES

 * Free for private non commercial use 
 * Very simple Plug'n'Play like ease of use
 * Uses no Database
 * Automatically creates thumbnails
 * Option to create and use intermediate preview images
 * Displays EXIF information
 * Rotates/corrects orientation of images using EXIF information
 * Option to add watermark to all images in gallery
 * Supports JPG, JPEG, PNG and GIF
 * Unlimited numbers of sub galleries
 * See configuration information for all features

 
____________________________________________________________
REQUIREMENTS

For this gallery to function you will need the following:
 * A web server capable of running PHP 5 scripts
 * The PHP GD library (php_gd) installed. See here for information: http://php.net/gd
 * PHP write access to server
 * PHP memory limit large enough to contain both full size image and preview/thumb (converted to BMP) See the FAQ section for further information on this.


____________________________________________________________
HOW TO USE / QUICK START

The only thing you have to do is place a copy of the index.php file to any directory of your web.
When this is done all images (jpg, jpeg, png, gif) and files if enabled, in that directory and all sub directories will be shown in the gallery.

By default the script will try to create a folder called "_sfpg_data" in which thumbnails, previews and information is saved. This can be changed using the DATA_ROOT constant. If PHP only have write access in a certain folder, change the DATA_ROOT to point there.


____________________________________________________________
TIPS / FAQ

 * If some or all thumbnails is missing or show up as a red X, the reason could be that the full images is too big for the php-engine to load them into memory. You will then have to do one of the following:
     1. Make the full images smaller
     2. Increase the memory_limit in php.ini. (If the memory_limit is 8MB, it gives a maximum image size on about 1600*1600)

 * If no thumbnails or images show, it could be that the script does not have the php tag "<?php" as the very first. The php tag must come before any other output. A blank line above or even a space before the tag will render the script useless. This can also happen if the auto_prepend_file option in php is used.

 * Single File PHP Gallery does not have to be called index.php, you can rename it to whatever you like.


____________________________________________________________
SUPPORT / REQUESTS / HELP / CONTACT

If you need help getting Single File PHP Gallery to work, then please start by running the SFPG TEST script file on the server. The SFPG TEST script is a normal PHP page, and you can download it from the Single File PHP Gallery page. The script tests to see if the requirements for the script to run are met, and generates an output explaining what could be the issue. If the output from the script does not provide enough information for you to make the script work, then please use the contact form on the Single File PHP Gallery page. Describe the issue you are having and also copy/paste the information from the SFPG TEST script along with the description.

If you have comments, questions, requests, greetings etc. regarding Single File PHP Gallery, please use the contact form on the Single File PHP Gallery page.

  REMEMBER TO WRITE YOUR E-MAIL IF YOU WOULD LIKE AN ANSWER

  http://sye.dk/sfpg/

  REMEMBER TO WRITE YOUR E-MAIL IF YOU WOULD LIKE AN ANSWER

____________________________________________________________
KNOWN ISSUES

 * If next or previous buttons is pressed before current image have loaded, the image will flicker when displayed. This will be fixed in a future version.
 
 * Information like Date, Full Image Dimensions and File size in bytes does not show correctly at the very first visit or view. This is because the data files holding this information are created at first visit. This is by design, to have the gallery run faster, and will not be fixed. Just refresh the page.

 * A directory thumbnail will stay unchanged when first set. Delete _info.sfpg inside directory or add or remove directories, images or files to reset. This is by design, and will not be fixed.

 * If a thumbnail for a given image exists, it will be used. So remember to delete old thumbnails if you make changes to the way thumbnails are created. If, for an example, you change the size of thumbnails, you will need to delete the old ones already saved, in order to have the changes apply, and have new thumbnails created. This is by design, and will not be fixed.

 * First access to a gallery containing many images or directories, will take a long time to load, because the script will have to create all the thumbnails. If the script timeout, the rest of the folders will be left without thumbnails and information. Then just refresh the page and the script will continue from where it came. The folder where the script stopped might not have a thumbnail. If this is so just click the gallery, and the thumb will be created. This is by design, and will not be fixed.

 * File-preview images are not used for gallery thumbnails. I recommend using a dir image (DIR_IMAGE_FILE). This is by design, and will not be fixed.

 * Thumbnails and data files are not automatically deleted when images or files are removed from the gallery. So remember to cleanup in the data area if a lot of images is moved or removed. This is by design, and will not be fixed.

 * Images smaller than preview size will still have preview size images created, and will also have the option to load full-size, even though they is the same size.

 * Single File PHP Gallery version 4.x is a stand-alone gallery. It is not designed to be included on existing pages. This is by design, and will not be fixed.


____________________________________________________________
CONFIGURATION INFORMATION

The script can be edited in a plain text editor. Open the index.php and you will find the configuration section at the top of the script.

Refer to the descriptions below for configuration.


____________________________________________________________
define("GALLERY_ROOT", "./");

Set the path of the gallery root, where the images and sub directories containing images is placed.
The default value is "./" which is the directory where the script is placed.
If SHOW_FILES is TRUE, the files will only be downloadable if a relative path like the one in example 1 is used.

GALLERY_ROOT should always end with a slash like the examples below.

Example 1: "./images/"
Example 2: $_SERVER[DOCUMENT_ROOT]."/gallery/images/"
Example 3: "/users/bob_the_user/wwwroot/images/my_pictures/"


____________________________________________________________
define("DATA_ROOT", "./_sfpg_data/");

Set the path to where thumbnails and other data should be saved. PHP needs to have write access to this directory. If PHP only have write access in a certain directory, change the DATA_ROOT to point there.

By default DATA_ROOT points to a directory called "_sfpg_data" in the same directory where the script is placed. If the directory do not exists, the script will try to create it. The "_sfpg_data" directory is by default excluded from the gallery, using the $dir_exclude option.

DATA_ROOT should always end with a slash like the examples below.

Example 1: "./thumbs/"
Example 2: $_SERVER[DOCUMENT_ROOT]."/gallery/thumbs/"
Example 3: "/users/bob_the_user/data/sfpg_thumbs/"


____________________________________________________________
define("SECURITY_PHRASE", "change this text!");

Set this option to a random phrase or a number of random chars, and don't tell anyone! The string will be used to make the gallery URL's tamper resistant to avoid creative or malicious use.

Data stored in the DATA_ROOT uses this phrase. So if this option is changed, the contents of DATA_ROOT directory should be deleted. It will then regenerate automatically.

Example 1: "WI5M2YzMjekzNmM3"
Example 2: "I.like.icecream"


____________________________________________________________
define("DIR_NAME_FILE", "_name.txt");

Set the name of the file that can be placed in every directory of the gallery.
If a file with the given name is found, the first line of text from the file will be used instead of the directory name. Directories will still be sorted after the actual name.

TIP 1: This is useful if you would like to use chars that cannot be used in directory names.
TIP 2: See SORT_DIVIDER for an easy way of sorting directories, images and files.


____________________________________________________________
define("DIR_IMAGE_FILE", "_image.jpg");

Set the name of the image that can be placed in every directory in the gallery.
If an image with the given name is found, it will be used as thumbnail for that directory. The image will not be displayed inside the directory.

TIP 1: This is very useful when having directories with only files of non-supported image types for download.
TIP 2: You do not have to resize the image, it will be resized like all other images in the gallery.


____________________________________________________________
define("DIR_DESC_FILE", "_desc.txt");

Set the name of the description file that can be placed in every directory of the gallery (including the GALLERY_ROOT).
If a file with the given name is found, the text will be shown in the gallery. The text can include HTML tags.


____________________________________________________________
define("DIR_SORT_REVERSE", FALSE);

Set to TRUE to sort directories in reverse order (highest to lowest).
Set to FALSE to sort directories in normal order (lowest to highest).

See also DIR_SORT_BY_TIME and SORT_NATURAL for other sorting options for dirs.


____________________________________________________________
define("DIR_SORT_BY_TIME", FALSE);

Set to TRUE to sort directories by modified time.
Set to FALSE to sort directories by name.

The modified time of a directory is normally updated when changes is made to the contents of the given directory. Even deleting a file or image can make the directory "new". (This might differ from platform to platform).

See also DIR_SORT_REVERSE and SORT_NATURAL for other sorting options for dirs.


____________________________________________________________
$dir_exclude = array("_sfpg_data", "_sfpg_icons");

Set an array of directory names that should not be shown in the gallery.

Directory names should be entered in lower case.
Exclusion is not case sensitive. If you exclude "oldimages", all the following directories will all be excluded: "OldImages", "oldImages","OLDIMAGES" and so on.

Example 1: array("cgi-bin");
Example 2: array("cgi-bin", "include", "old_images");

See also $file_exclude and $file_ext_exclude for other ways of excluding elements from the gallery.


____________________________________________________________
define("SHOW_IMAGE_EXT", FALSE);

Set to TRUE to show image name extensions.
Set to FALSE to not show image name extensions.


____________________________________________________________
define("IMAGE_SORT_REVERSE", FALSE);

Set to TRUE to sort images in reverse order (highest to lowest).
Set to FALSE to sort images in normal order (lowest to highest).

See also IMAGE_SORT_BY_TIME and SORT_NATURAL for other sorting options for images.


____________________________________________________________
define("IMAGE_SORT_BY_TIME", FALSE);

Set to TRUE to sort images by modified time.
Set to FALSE to sort images by name.

See also IMAGE_SORT_REVERSE and SORT_NATURAL for other sorting options for images.


____________________________________________________________
define("ROTATE_IMAGES", TRUE);

Set to TRUE to have images rotated according to the orientation information in EXIF.
Set to FALSE to not rotate images.

Rotated images are saved on the server in the DATA_ROOT. This could take up a lot of disk space.
If images have been saved on server you will have to delete them in order to have changes to this setting apply.


____________________________________________________________
define("IMAGE_JPEG_QUALITY", 90);

Set the quality for jpeg images. Range from 0 (worst quality and smallest file size) to 100 (best quality and largest file size).
If images have been saved on server you will have to delete them in order to have changes to this setting apply.
This setting is used for full size and preview images. These are saved on server after being rotated or watermarks have been added.


____________________________________________________________
define("SHOW_FILES", TRUE);

Set to TRUE to show non-image files and images of non-supported types as download links in the gallery.
Set to FALSE to only show images of supported types in the gallery.

TIP: Use $file_exclude and/or $file_ext_exclude to exclude files you do not want to have displayed.


____________________________________________________________
define("SHOW_FILE_EXT", TRUE);

Set to TRUE to show file name extensions.
Set to FALSE to not show file name extensions.


____________________________________________________________
define("FILE_IN_NEW_WINDOW", TRUE);

Set to TRUE to have files open in a pop-up window.
Set to FALSE to have files open in the same window.


____________________________________________________________
define("FILE_THUMB_EXT", ".jpg");

Set the extension of file thumbnail files. Place an image with the same name as a file with the set extension added to the file name, to have the image used as a thumbnail for the file. The image will be resized like any other thumbnail in the gallery.

INFO 1: Can be set to any of the supported image types.
INFO 2: Case sensitivity in this constant follows the case sensitivity of the server on which the script runs.
INFO 3: Extensions should be entered with a dot in front, like the example below.

Example: If set to ".jpg" and one of the files is named "In_The_Snow.avi". You can then make an image called "In_The_Snow.avi.jpg", and place it in the same folder as the image.


____________________________________________________________
define("FILE_SORT_REVERSE", FALSE);

Set to TRUE to sort files in reverse order (highest to lowest).
Set to FALSE to sort files in normal order (lowest to highest).

See also FILE_SORT_BY_TIME and SORT_NATURAL for other sorting options for files.


____________________________________________________________
define("FILE_SORT_BY_TIME", FALSE);

Set to TRUE to sort files by modified time.
Set to FALSE to sort files by name.

See also FILE_SORT_REVERSE and SORT_NATURAL for other sorting options for files.


____________________________________________________________
$file_exclude = array();

Set an array of file names that should not be shown in the gallery.

INFO 1: File names should be entered in lower case.
INFO 2: Exclusions works only on non-supported file types.
INFO 3: Exclusion is not case sensitive. If you exclude "readme.txt", all the following files will be excluded: "readme.txt", "ReadMe.Txt","README.TXT" and so on.

Example 1: array("readme.txt");
Example 2: array("readme.txt", "admin.php", "style.css");

See also $file_ext_exclude and $dir_exclude for other ways of excluding elements from the gallery.


____________________________________________________________
$file_ext_exclude = array(".php", ".txt");

Set an array of file extensions for files that should not be shown in the gallery.

INFO 1: Extensions should be entered in lower case with a dot in front, like the examples below.
INFO 2: Exclusions works only on non-supported file types. You cannot exclude the supported image types.
INFO 3: Exclusion is not case sensitive. If you exclude ".txt", all files with the following extensions will be excluded: ".txt", ".Txt", ".TXT" and so on.

Example 1: array(".php");
Example 2: array(".php", ".txt", ".inc", ".html");

See also $file_exclude and $dir_exclude for other ways of excluding elements from the gallery.


____________________________________________________________
$file_ext_thumbs = array();

Set an array to associate extensions with images for them to be used as thumbnails for non-image files.
To use this option you must:
 1. Create a directory called "_sfpg_icons" in the GALLERY_ROOT.
 2. Place the images that are to be used as thumbnails in the directory.
 3. Associate the images with extensions using the $file_ext_thumbs array.

INFO: Extensions should be entered in lower case with a dot in front, like the examples below.

Example 1: array(".pdf" => "pdf.png");
Example 2: array(".pdf" => "pdf.png", ".zip" => "zip.png");

In example 1, an image called "pdf.png" have been created and placed in the "_sfpg_icons" directory in the GALLERY_ROOT.
This will make all PDF files have the given image as thumbnail.

TIP: You do not have to resize the image, it will be resized like all other images in the gallery. 


____________________________________________________________
define("LINK_BACK", "");

Set a URL to show a button that will function as a link to the set URL. This can be used as a "Back to my site" button.
Set to "" to not show the button.

Example 1: Set to "/" to have button take you to the root of your web.
Example 2: Set to "http://www.yoursite.com/page.html".

See TEXT_LINK_BACK for the text on the button.


____________________________________________________________
define("CHARSET", "iso-8859-1");

Set the charset to be used. In order to have special chars display correctly, a charset that support the chars used in the gallery, must be set.

Below is a short list of charsets that can be used (use a search engine on the Internet for others):

Universal Alphabet:        "utf-8"
Western Alphabet:          "iso-8859-1"
Central European Alphabet: "iso-8859-2"
Latin 3 Alphabet:          "iso-8859-3"
Baltic Alphabet:           "iso-8859-4"
Cyrillic Alphabet:         "iso-8859-5"
Arabic Alphabet:           "iso-8859-6"
Greek Alphabet:            "iso-8859-7"
Hebrew Alphabet:           "iso-8859-8"
Japanese:                  "shift-jis"
Chinese Traditional:       "big5"


____________________________________________________________
define("DATE_FORMAT", "Y-m-d h:i:s");

Set the format of the date/time to be shown after the TEXT_DATE.
See a PHP manual or the following page for information on the date format: http://php.net/date


____________________________________________________________
define("DESC_EXT", ".txt");

Set the extension of the description files. Place a file with the same name as an image or file with the set extension added to the image name, to have the text shown with the image or file.

Case sensitivity in this constant follows the case sensitivity of the server on which the script runs.
Extensions should be entered with a dot in front, like the example below.

Example: If set to ".txt" and one of the images is named
         "IMG_10a.jpg". You can then make a file called
         "IMG_10a.jpg.txt", and place it in the same folder as the image.

If SHOW_FILES is set to TRUE you can use $file_ext_exclude to exclude the description files from the view. So if you would like to list .txt but don't want the descriptions files shown, you should set to any other extension that you do not use (feel free to use ".sfpg") and exclude that one using $file_ext_exclude.
	 

____________________________________________________________
define("SORT_DIVIDER", "--");

Set the string that will function as a divider between the part of the name to sort after and the name to be shown.
SORT_DIVIDER works on directories, images and files.

Example: If you have a directory called "Apples" and one called "Bananas", and would like "Bananas" to be the first one on the list. You could then call them: "001--Bananas" and "002--Apples". The directories will then be listed in the order you like, and only the text after the SORT_DIVIDER string will be shown.


____________________________________________________________
define("SORT_NATURAL", TRUE);

Set to TRUE to have directories, images and files sorted in a case insensitive "natural order".
Set to FALSE to have elements sorted normally.
See here for information: http://php.net/natcasesort


____________________________________________________________
define("FONT_SIZE", 12);

Set the Font size.


____________________________________________________________
define("UNDERSCORE_AS_SPACE", TRUE);

Set to TRUE to have names shown as spaces.
Set to FALSE to have names shown as is.

Works on directory, image and file names.


____________________________________________________________
define("NL_TO_BR", FALSE);

Set to TRUE to have the script add a HTML line break (BR) where line breaks is found in the description files.
Works on descriptions for directories, images and files.


____________________________________________________________
define("THUMB_MAX_WIDTH", 160);

Set the maximum number of pixels a thumbnails width may be.
If thumbnails have been saved on server you will have to delete them in order to have changes to this setting apply.


____________________________________________________________
define("THUMB_MAX_HEIGHT", 120);

Set the maximum number of pixels a thumbnails height may be.
If thumbnails have been saved on server you will have to delete them in order to have changes to this setting apply.


____________________________________________________________
define("THUMB_ENLARGE", FALSE);

Set to TRUE to have thumbnails for images that is smaller than THUMB_MAX_WIDTH and THUMB_MAX_HEIGHT enlarged.
Set to FALSE to have thumbnails for images that are smaller than THUMB_MAX_WIDTH and THUMB_MAX_HEIGHT stay the size they are.


____________________________________________________________
define("THUMB_JPEG_QUALITY", 75);

Set the quality for jpeg thumbnails. Range from 0 (worst quality and smallest file size) to 100 (best quality and largest file size).
If thumbnails have been saved on server you will have to delete them in order to have changes to this setting apply.


____________________________________________________________
define("USE_PREVIEW", FALSE);

Set to TRUE to have the script generate intermediate preview images. The preview images will be shown when a thumbnail is clicked, and a "Full resolution" button will be enabled to allow switching between full resolution and preview.
The preview images are generated when accessed for the first time, and will be saved on the server for later usage.
Set to FALSE to not use intermediate preview images.
This option is useful if the gallery contains large images that will take long time to load.


____________________________________________________________
define("PREVIEW_MAX_WIDTH", 600);

Set the maximum number of pixels a preview image width may be.
If preview images have been saved on server you will have to delete them in order to have changes to this setting apply.


____________________________________________________________
define("PREVIEW_MAX_HEIGHT", 400);

Set the maximum number of pixels a preview image height may be.
If preview images have been saved on server you will have to delete them in order to have changes to this setting apply.


____________________________________________________________
define("PREVIEW_ENLARGE", FALSE);

Set to TRUE to have preview images for images that are smaller than PREVIEW_MAX_WIDTH and PREVIEW_MAX_HEIGHT enlarged.
Set to FALSE to have preview images for images that are smaller than PREVIEW_MAX_WIDTH and PREVIEW_MAX_HEIGHT stay the size they are.


____________________________________________________________
define("PREVIEW_JPEG_QUALITY", 75);

Set the quality for jpeg preview images. Range from 0 (worst quality and smallest file size) to 100 (best quality and largest file size).
If preview images have been saved on server you will have to delete them in order to have changes to this setting apply.


____________________________________________________________
define("WATERMARK", "");

Set the filename of the image that is to be used as watermark on all images in gallery.
This will make all images, preview images and thumbnails contain the watermark in the lower right corner of the image.

To use this option you must:
 1. Create a directory called "_sfpg_icons" in the GALLERY_ROOT.
 2. Place the image that is to be used as watermark in the directory.
 3. Add the name of the image to the WATERMARK constant.

Example: define("WATERMARK", "watermark.png");

In the example above, an image called "watermark.png" have been created and placed in the "_sfpg_icons" directory in the GALLERY_ROOT.

INFO 1: If set, all images containing watermark will be cached in the DATA_ROOT. Depending on the set IMAGE_JPEG_QUALITY, this could take up the same or more space than the original images in GALLERY_ROOT.
INFO 2: If set or changed after images, thumbnails or preview images have been created, then all thumbnails need to be deleted from the DATA_ROOT in order to have new images including watermark created.
INFO 3: Case sensitivity in this constant follows the case sensitivity of the server on which the script runs.

The watermark image will not be resized. It will be be applied in the original size.


____________________________________________________________
define("INFO_BOX_WIDTH", 250);

Set the width in pixels of the information box. If thumbnail size is increased, this probably also needs to be increased.


____________________________________________________________
define("MENU_BOX_HEIGHT", 70);

Set the height in pixels of the menu box. If font size is increased, this probably also needs to be increased.


____________________________________________________________
define("NAV_BAR_HEIGHT", 25);

Set the height in pixels of the navigation bar. If font size is increased, this probably also needs to be increased.


____________________________________________________________
define("THUMB_BORDER_WIDTH", 1);

Set the number of pixels for the thumbnail border width.


____________________________________________________________
define("THUMB_MARGIN", 10);

Set the number of pixels that the thumbnail box should be larger than the thumbnail all the way round.


____________________________________________________________
define("THUMB_BOX_MARGIN", 7);

Set the number of pixels the thumbnail boxes margin should be. The thumbnail boxes will be two times the set number apart.


____________________________________________________________
define("THUMB_BOX_EXTRA_HEIGHT", 14);

Set the number of pixels that will be added to the thumbnail boxes height. This would normally be a little larger than the set font size. So if the font size is increased, this probably also needs to be increased.
If THUMB_CHARS_MAX is set to 0, to not show thumbnail names, this should also be set to 0.


____________________________________________________________
define("THUMB_CHARS_MAX", 20);

Set the maximum number of chars that will be printed below thumbnails.
Set to 0 to not show thumbnail names. If set to 0 THUMB_BOX_EXTRA_HEIGHT should also be set to 0.


____________________________________________________________
define("FULLIMG_BORDER_WIDTH", 5);

Set the number of pixels for the border width of the full image.


____________________________________________________________
define("NAVI_CHARS_MAX", 100);

Set the maximum number of chars that will be printed in the navigation bar. When the navigation path gets longer than the set number, then the first links/buttons will be replaced with "..."


____________________________________________________________
define("OVERLAY_OPACITY", 90);

Set the opacity percent for the overlaying layer that hides the thumbnails when a full image or preview image is shown.
Set to 0 to have the layer be invisible.
Set to 100 to have a solid color.


____________________________________________________________
define("FADE_FRAME_PER_SEC", 30);

Set the number of changes per second when fading images. Setting this high will make the fade more smooth, but will also be harder on slow machines.


____________________________________________________________
define("FADE_DURATION_MS", 300);

Set the duration of the fade of images in milliseconds.


____________________________________________________________
define("LOAD_FADE_GRACE", 500);

Set the number of milliseconds the script will allow the browser to change the image when it has fully loaded. Setting this too low will in some browsers result in fading-in the image before it have been actually placed.


____________________________________________________________
define("TEXT_GALLERY_NAME", "Single File PHP Gallery");

Text that will be set as title on the page.


____________________________________________________________
define("TEXT_HOME", "Home");

Text on first button in the navigation bar that takes you back to the root of the gallery.


____________________________________________________________
define("TEXT_CLOSE_IMG_VIEW", "Close Image");

Text on button that closes full size image view.


____________________________________________________________
define("TEXT_ACTUAL_SIZE", "Actual Size");

Text on button that can switch between fitting image to screen and actual size.


____________________________________________________________
define("TEXT_FULLRES", "Full resolution");

Text on button that will be shown if USE_PREVIEW is set to TRUE. Used to switch between preview and full resolution images.


____________________________________________________________
define("TEXT_PREVIOUS", "<< Previous");

Text on previous button.


____________________________________________________________
define("TEXT_NEXT", "Next >>");

Text on next button.


____________________________________________________________
define("TEXT_INFO", "Information");

Text on button that will show/hide information.


____________________________________________________________
define("TEXT_DOWNLOAD", "Download full-size image");

Text for the link in information box that allows download of full-size image.
Set to "" to have download link removed and function disabled. Just keep in mind that users still can save all images in a number of ways.


____________________________________________________________
define("TEXT_NO_IMAGES", "No Images in gallery");

Text for the information to be shown instead of a thumbnail if there are no images in a directory.


____________________________________________________________
define("TEXT_DATE", "Date");

Text for the date label.


____________________________________________________________
define("TEXT_FILESIZE", "File size");

Text for the file size label.


____________________________________________________________
define("TEXT_IMAGESIZE", "Full Image");

Text for the image size label.


____________________________________________________________
define("TEXT_DISPLAYED_IMAGE", "Displayed Image");

Text for the displayed image dimensions label.


____________________________________________________________
define("TEXT_DIR_NAME", "Gallery Name");

Text for the gallery name label.


____________________________________________________________
define("TEXT_IMAGE_NAME", "Image Name");

Text for the image name label.


____________________________________________________________
define("TEXT_FILE_NAME", "File Name");

Text for the file name label.


____________________________________________________________
define("TEXT_DIRS", "Sub galleries");

Text for the sub galleries label.


____________________________________________________________
define("TEXT_IMAGES", "Images");

Text for the images label.


____________________________________________________________
define("TEXT_IMAGE_NUMBER", "Image number");

Text for the image number label.


____________________________________________________________
define("TEXT_FILES", "Files");

Text for the files label.


____________________________________________________________
define("TEXT_DESCRIPTION", "Description");

Text for the description label.

____________________________________________________________
define("TEXT_DIRECT_LINK_GALLERY", "Direct link to Gallery");

Text for the direct link to gallery link. The link is shown in the information box, and can be used to link directly to the current sub gallery.


____________________________________________________________
define("TEXT_DIRECT_LINK_IMAGE", "Direct link to Image");

Text for the direct link to image link. The link is shown in the information box, and can be used to link directly to the current image.


____________________________________________________________
define("TEXT_NO_PREVIEW_FILE", "No Preview for file");

Text for the information to be shown instead of a thumbnail if there is no preview for a file.


____________________________________________________________
define("TEXT_IMAGE_LOADING", "Image Loading ");

Text for the information to show when images is loading.


____________________________________________________________
define("TEXT_LINKS", "Links");

Text for the links label.


____________________________________________________________
define("TEXT_NOT_SCALED", "Not Scaled");

Text for the label that will be shown in information box if the current full size or preview image is not resized to fit screen.


____________________________________________________________
define("TEXT_LINK_BACK", "Back to my site");

Text for an optional link button. See LINK_BACK for further information.


____________________________________________________________
define("TEXT_THIS_IS_FULL", "Full");

Text for the label that will show if the current image is full size.


____________________________________________________________
define("TEXT_THIS_IS_PREVIEW", "Preview");

Text for the label that will show if the current image is a preview size.


____________________________________________________________
define("TEXT_SCALED_TO", "Scaled to");

Text for the scaled to label.


____________________________________________________________
define("TEXT_YES", "Yes");

Text for the yes label.


____________________________________________________________
define("TEXT_NO", "No");

Text for the no label.


____________________________________________________________
define("TEXT_EXIF_DATE", "EXIF Date");

Text for the EXIF Date label.
If changed after thumbnails have been created, thumbnails have to be deleted for this setting to apply.


____________________________________________________________
define("TEXT_EXIF_CAMERA", "Camera");

Text for the Camera label.
If changed after thumbnails have been created, thumbnails have to be deleted for this setting to apply.


____________________________________________________________
define("TEXT_EXIF_ISO", "ISO");

Text for the ISO label.
If changed after thumbnails have been created, thumbnails have to be deleted for this setting to apply.

____________________________________________________________
define("TEXT_EXIF_SHUTTER", "Shutter Speed");

Text for the Shutter Speed label.
If changed after thumbnails have been created, thumbnails have to be deleted for this setting to apply.


____________________________________________________________
define("TEXT_EXIF_APERTURE", "Aperture");
	
Text for the Aperture label.
If changed after thumbnails have been created, thumbnails have to be deleted for this setting to apply.


____________________________________________________________
define("TEXT_EXIF_FOCAL", "Focal Length");
	
Text for the Focal Length label.
If changed after thumbnails have been created, thumbnails have to be deleted for this setting to apply.


____________________________________________________________
define("TEXT_EXIF_FLASH", "Flash fired");
	
Text for the Flash Fired label.
If changed after thumbnails have been created, thumbnails have to be deleted for this setting to apply.


____________________________________________________________
define("TEXT_EXIF_MISSING", "No EXIF informatin in image file");

Text for the label that will be shown if there are no EXIF info in image file.
If changed after thumbnails have been created, thumbnails have to be deleted for this setting to apply.


____________________________________________________________
Set the colors to be used. If the color variable names is not enough to indicate where it goes, then just change it to red (#ff0000) or blue (#0000ff) and refresh the page, you should then be able to spot it.

$color_body_back = "#000000";
$color_body_text = "#aaaaaa";
$color_body_link = "#b0b0b0";
$color_body_hover = "#ffffff";

$color_thumb_border = "#606060";
$color_fullimg_border = "#ffffff";
	
$color_dir_box_border = "#505050";
$color_dir_box_back = "#000000";
$color_dir_box_text = "#aaaaaa";
$color_dir_hover = "#ffffff";
$color_dir_hover_text = "#000000";

$color_img_box_border = "#505050";
$color_img_box_back = "#202020";
$color_img_box_text = "#aaaaaa";
$color_img_hover = "#ffffff";
$color_img_hover_text = "#000000";

$color_file_box_border = "#404040";
$color_file_box_back = "#101010";
$color_file_box_text = "#aaaaaa";
$color_file_hover = "#ffffff";
$color_file_hover_text = "#000000";

$color_button_border = "#808080";
$color_button_back = "#000000";
$color_button_text = "#aaaaaa";
$color_button_border_off = "#505050";
$color_button_back_off = "#000000";
$color_button_text_off = "#505050";
$color_button_hover = "#ffffff";
$color_button_hover_text = "#000000";
$color_button_on = "#aaaaaa";
$color_button_text_on = "#000000";
	
$color_overlay = "#000000";
$color_menu_hover = "#ffffff";

____________________________________________________________
EOF