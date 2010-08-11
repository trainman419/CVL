#!/bin/bash
for f in pics/*.tar.gz; do
	echo $f
	tar --extract --overwrite -z -m -f $f -C pics
done

for f in pics/*/*.html; do
	echo $f
	sed 's_</h2>_&<center><a href="../../index.html">Back to CVL Home page</a></center>_' <$f>temp
	sed 's_</table>_&<center><a href="../../index.html">Back to CVL Home page</a></center>_' <temp>$f
	rm -f temp
done

for f in pics/*/*Pages/*.html; do
	echo $f
	sed 's_<br>_&<center><a href="../../../index.html">Back to CVL Home page</a>_' <$f>temp
	sed 's_</body>_</center>&_' <temp>$f
	rm -f temp
done
