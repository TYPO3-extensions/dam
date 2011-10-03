#
# Table structure for table 'tx_media_mediatype'
#
CREATE TABLE tx_media_mediatype (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	media_type varchar(255) DEFAULT '' NOT NULL,

	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);

INSERT INTO tx_media_mediatype VALUES ('1', '0', 'Text', '0', '0');
INSERT INTO tx_media_mediatype VALUES ('2', '0', 'Image', '0', '0');
INSERT INTO tx_media_mediatype VALUES ('3', '0', 'Audio', '0', '0');
INSERT INTO tx_media_mediatype VALUES ('4', '0', 'Video', '0', '0');
INSERT INTO tx_media_mediatype VALUES ('5', '0', 'Software', '0', '0');

#
# Table structure for table 'tx_media_mimetype'
#
DROP TABLE IF EXISTS tx_media_mimetype;
CREATE TABLE tx_media_mimetype (
	uid int(11) NOT NULL auto_increment,
	pid int(11) DEFAULT '0' NOT NULL,

	mime_type varchar(255) DEFAULT '' NOT NULL,
	mime_type_name varchar(255) DEFAULT '' NOT NULL,
	media_type int(11) unsigned DEFAULT '0',

	deleted tinyint(4) unsigned DEFAULT '0' NOT NULL,
	hidden tinyint(4) unsigned DEFAULT '0' NOT NULL,

	PRIMARY KEY (uid),
	KEY parent (pid)
);


INSERT INTO tx_media_mimetype VALUES ('', '0', 'au', 'audio/basic', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'snd', 'audio/basic', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mid', 'audio/midi', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'midi', 'audio/midi', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'kar', 'audio/midi', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mpga', 'audio/mpeg', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mpega', 'audio/mpeg', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'm3u', 'audio/mpegurl', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sid', 'audio/prs.sid', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'aifc', 'audio/x-aiff', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'aif', 'audio/x-aiff', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'aiff', 'audio/x-aiff', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'faif', 'audio/x-aiff', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pae', 'audio/x-epac', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'gsm', 'audio/x-gsm', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'uni', 'audio/x-mod', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mtm', 'audio/x-mod', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mod', 'audio/x-mod', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 's3m', 'audio/x-mod', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'it', 'audio/x-mod', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'stm', 'audio/x-mod', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ult', 'audio/x-mod', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xm', 'audio/x-mod', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mp2', 'audio/x-mpeg', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mp3', 'audio/x-mpeg', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wax', 'audio/x-ms-wax', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wma', 'audio/x-ms-wma', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pac', 'audio/x-pac', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ram', 'audio/x-pn-realaudio', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ra', 'audio/x-pn-realaudio', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'rm', 'audio/x-pn-realaudio', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wav', 'audio/x-wav', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'm4a', 'audio/x-m4a', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'm4b', 'audio/mp4a-latm', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'm4p', 'audio/mp4a-latm', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'm4r', 'audio/aac', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'oga', 'audio/ogg', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ogg', 'audio/ogg', '3', '0', '0');

INSERT INTO tx_media_mimetype VALUES ('', '0', 'z' , 'encoding/x-compress', '0', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'gz', 'encoding/x-gzip', '0', '0', '0');

INSERT INTO tx_media_mimetype VALUES ('', '0', 'bmp', 'image/bitmap', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'gif', 'image/gif', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ief', 'image/ief', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'jpg', 'image/jpeg', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'jpeg', 'image/jpeg', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'jpe', 'image/jpeg', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pcx', 'image/pcx', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'png', 'image/png', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tiff', 'image/tiff', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tif', 'image/tiff', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wbmp', 'image/vnd.wap.wbmp', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ras', 'image/x-cmu-raster', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'cdr', 'image/x-coreldraw', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pat', 'image/x-coreldrawpattern', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'cdt', 'image/x-coreldrawtemplate', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'cpt', 'image/x-corelphotopaint', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'jng', 'image/x-jng', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pcd', 'image/x-photo-cd', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pnm', 'image/x-portable-anymap', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pbm', 'image/x-portable-bitmap', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pgm', 'image/x-portable-graymap', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ppm', 'image/x-portable-pixmap', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'rgb', 'image/x-rgb', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xbm', 'image/x-xbitmap', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xpm', 'image/x-xpixmap', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xwd', 'image/x-xwindowdump', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'svg', 'image/svg+xml', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xcf', 'image/xcf', '2', '0', '0');

INSERT INTO tx_media_mimetype VALUES ('', '0', 'iges', 'model/iges', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'igs', 'model/iges', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'msh', 'model/mesh', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'silo', 'model/mesh', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mesh', 'model/mesh', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'vrml', 'model/vrml', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wrl', 'model/vrml', '5', '0', '0');

INSERT INTO tx_media_mimetype VALUES ('', '0', 'vfb', 'text/calendar', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ifb', 'text/calendar', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ics', 'text/calendar', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'csv', 'text/comma-separated-values', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'css', 'text/css', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'patch', 'text/diff', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'html', 'text/html', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xhtml', 'text/html', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'htm', 'text/html', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'shtml', 'text/html', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mml', 'text/mathml', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'log', 'text/plain', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'txt', 'text/plain', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'po', 'text/plain', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'asc', 'text/plain', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'diff', 'text/plain', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'text', 'text/plain', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'rtx', 'text/richtext', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sgml', 'text/sgml', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sgm', 'text/sgml', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tsv', 'text/tab-separated-values', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wml', 'text/vnd.wap.wml', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wmls', 'text/vnd.wap.wmlscript', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'hxx', 'text/x-c++hdr', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'hpp', 'text/x-c++hdr', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'h++', 'text/x-c++hdr', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'hh', 'text/x-c++hdr', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'cc', 'text/x-c++src', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'c++', 'text/x-c++src', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'cpp', 'text/x-c++src', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'cxx', 'text/x-c++src', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'h' , 'text/x-chdr', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'c' , 'text/x-csrc', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'java', 'text/x-java', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pas', 'text/x-pascal', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'p' , 'text/x-pascal', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'etx', 'text/x-setext', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tk', 'text/x-tcl', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ltx', 'text/x-tex', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sty', 'text/x-tex', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'cls', 'text/x-tex', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'vcs', 'text/x-vcalendar', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'vcf', 'text/x-vcard', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xsl', 'text/xml', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xml', 'text/xml', '1', '0', '0');

INSERT INTO tx_media_mimetype VALUES ('', '0', 'dl', 'video/dl', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'gl', 'video/gl', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mpg', 'video/mpeg', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mpeg', 'video/mpeg', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mpe', 'video/mpeg', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'qt', 'video/quicktime', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mov', 'video/quicktime', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mxu', 'video/vnd.mpegurl', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'iff', 'video/x-anim', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'anim3', 'video/x-anim', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'anim7', 'video/x-anim', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'anim', 'video/x-anim', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'anim5', 'video/x-anim', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'flc', 'video/x-flc', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'fli', 'video/x-fli', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'flv', 'video/x-flv', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mng', 'video/x-mng', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'asx', 'video/x-ms-asf', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'asf', 'video/x-ms-asf', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wm', 'video/x-ms-wm', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wmv', 'video/x-ms-wmv', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wmx', 'video/x-ms-wmx', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wvx', 'video/x-ms-wvx', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'avi', 'video/x-msvideo', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'avx', 'video/x-rad-screenplay', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mv', 'video/x-sgi-movie', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'movi', 'video/x-sgi-movie', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'movie', 'video/x-sgi-movie', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'vcr', 'video/x-sunvideo', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mp4', 'video/mp4v-es', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'm4v', 'video/x-m4v', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mp4v', 'video/mp4v-es', '4', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ogv', 'video/ogg', '4', '0', '0');

INSERT INTO tx_media_mimetype VALUES ('', '0', 'ez', 'application/andrew-inset', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'cu', 'application/cu-seeme', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'csm', 'application/cu-seeme', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tsp', 'application/dsptype', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'fif', 'application/fractals', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'spl', 'application/futuresplash', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'hqx', 'application/mac-binhex40', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mdb', 'application/msaccess', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xls', 'application/msexcel', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xlw', 'application/msexcel', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'hlp', 'application/mshelp', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ppt', 'application/mspowerpoint', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mpx', 'application/msproject', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mpw', 'application/msproject', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mpp', 'application/msproject', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mpt', 'application/msproject', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mpc', 'application/msproject', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'doc', 'application/msword', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'so', 'application/octet-stream', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'bin', 'application/octet-stream', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'exe', 'application/octet-stream', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'oda', 'application/oda', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pdf', 'application/pdf', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pgp', 'application/pgp-signature', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'eps', 'application/postscript', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ai', 'application/postscript', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ps', 'application/postscript', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'rtf', 'application/rtf', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'smi', 'application/smil', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'smil', 'application/smil', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xlb', 'application/vnd.ms-excel', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pot', 'application/vnd.ms-powerpoint', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pps', 'application/vnd.ms-powerpoint', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sxc', 'application/vnd.sun.xml.calc', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'stc', 'application/vnd.sun.xml.calc.template', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sxd', 'application/vnd.sun.xml.draw', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'std', 'application/vnd.sun.xml.draw.template', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sxi', 'application/vnd.sun.xml.impress', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sti', 'application/vnd.sun.xml.impress.template', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sxm', 'application/vnd.sun.xml.math', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sxw', 'application/vnd.sun.xml.writer', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sxg', 'application/vnd.sun.xml.writer.global', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'stw', 'application/vnd.sun.xml.writer.template', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'vsd', 'application/vnd.visio', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wbxml', 'application/vnd.wap.wbxml', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wmlc', 'application/vnd.wap.wmlc', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wmlsc', 'application/vnd.wap.wmlscriptc', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wp5', 'application/wordperfect5.1', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wk', 'application/x-123', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'aw', 'application/x-applix', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'bcpio', 'application/x-bcpio', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'vcd', 'application/x-cdlink', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pgn', 'application/x-chess-pgn', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'Z' , 'application/x-compress', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'cpio', 'application/x-cpio', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'csh', 'application/x-csh', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'deb', 'application/x-debian-package', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'dcr', 'application/x-director', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'dxr', 'application/x-director', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'dir', 'application/x-director', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'dms', 'application/x-dms', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'dot', 'application/x-dot', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'dvi', 'application/x-dvi', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'fmr', 'application/x-fmr', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pcf', 'application/x-font', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pcf.Z', 'application/x-font', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'gsf', 'application/x-font', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pfb', 'application/x-font', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pfa', 'application/x-font', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'fr', 'application/x-fr', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'gnumeric', 'application/x-gnumeric', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tgz', 'application/x-gtar', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'gtar', 'application/x-gtar', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'hdf', 'application/x-hdf', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pht', 'application/x-httpd-php', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'php', 'application/x-httpd-php', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'phtml', 'application/x-httpd-php', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'php3', 'application/x-httpd-php3', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'php3p', 'application/x-httpd-php3-preprocessed', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'phps', 'application/x-httpd-php3-source', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'php4', 'application/x-httpd-php4', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ica', 'application/x-ica', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'class', 'application/x-java', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'js', 'application/x-javascript', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'chrt', 'application/x-kchart', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'kil', 'application/x-killustrator', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'skd', 'application/x-koan', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'skt', 'application/x-koan', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'skp', 'application/x-koan', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'skm', 'application/x-koan', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'kpr', 'application/x-kpresenter', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'kpt', 'application/x-kpresenter', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ksp', 'application/x-kspread', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'kwt', 'application/x-kword', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'kwd', 'application/x-kword', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'latex', 'application/x-latex', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'lha', 'application/x-lha', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'lzh', 'application/x-lzh', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'lzx', 'application/x-lzx', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'frm', 'application/x-maker', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'book', 'application/x-maker', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'fbdoc', 'application/x-maker', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'fm', 'application/x-maker', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'frame', 'application/x-maker', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'fb', 'application/x-maker', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'maker', 'application/x-maker', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mif', 'application/x-mif', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'mi', 'application/x-mif', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wmd', 'application/x-ms-wmd', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'wmz', 'application/x-ms-wmz', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'bat', 'application/x-msdos-program', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'com', 'application/x-msdos-program', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'dll', 'application/x-msdos-program', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'msi', 'application/x-msi', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'nc', 'application/x-netcdf', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'cdf', 'application/x-netcdf', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'proxy', 'application/x-ns-proxy-autoconfig', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'o' , 'application/x-object', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ogg', 'application/x-ogg', '3', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'oza', 'application/x-oz-application', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'perl', 'application/x-perl', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pm', 'application/x-perl', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pl', 'application/x-perl', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'qxd', 'application/x-quark-xpress-3', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'rpm', 'application/x-redhat-package-manager', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sh', 'application/x-sh', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'shar', 'application/x-shar', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'swf', 'application/x-shockwave-flash', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'swfl', 'application/x-shockwave-flash', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sit', 'application/x-stuffit', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tar', 'application/x-tar', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tcl', 'application/x-tcl', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tex', 'application/x-tex', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'gf', 'application/x-tex-gf', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pk', 'application/x-tex-pk', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'PK', 'application/x-tex-pk', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'texinfo', 'application/x-texinfo', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'texi', 'application/x-texinfo', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tki', 'application/x-tkined', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tkined', 'application/x-tkined', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', '%', 'application/x-trash', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'sik', 'application/x-trash', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', '~', 'application/x-trash', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'old', 'application/x-trash', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'bak', 'application/x-trash', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'tr', 'application/x-troff', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'roff', 'application/x-troff', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 't', 'application/x-troff', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'man', 'application/x-troff-man', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'me', 'application/x-troff-me', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ms', 'application/x-troff-ms', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'zip', 'application/x-zip-compressed', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xht', 'application/xhtml+xml', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'psd', 'application/photoshop', '2', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'odt', 'application/vnd.oasis.opendocument.text', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'otf', 'application/vnd.oasis.opendocument.formula-template', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ott', 'application/vnd.oasis.opendocument.text-template', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'oth', 'application/vnd.oasis.opendocument.text-web', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'odm', 'application/vnd.oasis.opendocument.text-master', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'odg', 'application/vnd.oasis.opendocument.graphics', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'otg', 'application/vnd.oasis.opendocument.graphics-template', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'odp', 'application/vnd.oasis.opendocument.presentation', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'otp', 'application/vnd.oasis.opendocument.presentation-template', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ods', 'application/vnd.oasis.opendocument.spreadsheet', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ots', 'application/vnd.oasis.opendocument.spreadsheet-template', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'odc', 'application/vnd.oasis.opendocument.chart', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'odf', 'application/vnd.oasis.opendocument.formula', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'odb', 'application/vnd.oasis.opendocument.database', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'odi', 'application/vnd.oasis.opendocument.image', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'oxt', 'application/vnd.openofficeorg.extension', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'docm', 'application/vnd.ms-word.document.macroEnabled.12', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'docx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', '1', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'dotm', 'application/vnd.ms-word.template.macroEnabled.12', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'dotx', 'application/vnd.openxmlformats-officedocument.wordprocessingml.template', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ppsm', 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'ppsx', 'application/vnd.openxmlformats-officedocument.presentationml.slideshow', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pptm', 'application/vnd.ms-powerpoint.presentation.macroEnabled.12', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'pptx', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xlsb', 'application/vnd.ms-excel.sheet.binary.macroEnabled.12', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xlsm', 'application/vnd.ms-excel.sheet.macroEnabled.12', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xlsx', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', '5', '0', '0');
INSERT INTO tx_media_mimetype VALUES ('', '0', 'xps', 'application/vnd.ms-xpsdocument', '5', '0', '0');
