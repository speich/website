<?php
set_time_limit(300);
header('Content-type: multipart/x-mixed-replace;boundary="NEXTPART"');
print "\n--NEXTPART\n";
$pmt = array("-", "\\", "|", "/");
for ($i = 0; $i < 10; $i++) {
	// only works with \n\n as line endings
	print "Content-type: text/plain\n\n";
	print "Part $i\t".$pmt[$i % 4];
	print "\n--NEXTPART\n";
	ob_flush();
	flush();
	sleep(1);
}
print "Content-type: text/plain\n\n";
print "The end\n";
print "--NEXTPART--\n";
?>