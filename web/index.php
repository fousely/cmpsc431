<?php
include 'functions.php';
session_start();
$r = getDBConnection();

$extraQueryConditions = "";
$minPrice = $maxPrice = $search = $category = "";
$search = filter_input(INPUT_GET, 'search');
$category = getURLParameter('category');
$minPrice = getURLParameter('minPrice');
$maxPrice = getURLParameter('maxPrice');

if (!empty($search)) {
	// Add search terms
	$extraQueryConditions = "AND (I.upc LIKE '%$search%' OR D.name LIKE '%$search%' 
					OR O.owner_id LIKE '%$search%' OR D.description LIKE '%$search%') ";
	$searchMessage = " matching '$search'";
}

if (!empty($category) && $category != -1) {
	// Add category restraints
	$category = urldecode($category);

	$categoryConstraints = "AND N.category = \"$category\" ";

	$extraQueryConditions = $extraQueryConditions . $categoryConstraints;
	$categoryMessage = " in category \"$category\"";
}

if (!empty($minPrice)) {
	// Add min price restraints
	$minPriceRestraint = "AND ((I.auction_price >= \"$minPrice\" AND 
					I.bid_end > NOW() AND I.bid_start <= NOW()) 
				OR I.list_price >= \"$minPrice\") ";

	$extraQueryConditions = $extraQueryConditions . $minPriceRestraint;
	$priceMessage = " with price greater than \$$minPrice";
}

if (!empty($maxPrice)) {
	// Add max price restraints
	$maxPriceRestraint = "AND ((I.auction_price <= \"$maxPrice\" AND I.auction_price != \"NULL\"
					AND I.bid_end > NOW() AND I.bid_start <= NOW()) 
				OR (I.list_price <= \"$maxPrice\" AND I.list_price != \"NULL\")) ";

	$extraQueryConditions = $extraQueryConditions . $maxPriceRestraint;

	if (!empty($minPrice)) {
		$priceMessage = $priceMessage . " but less than \$$maxPrice";
	} else {
		$priceMessage = " with price less than \$$maxPrice";
	}
}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">


<!BEGIN MOUSE CLOCK FROM http://rainbow.arch.scriptmania.com/scripts/mouse_clock.html>

<style type="text/css">
<!--
/*Do not Alter these. Set for alignment*/
.css1{
position:absolute;top:0px;left:0px;
width:16px;height:16px;
font-family:Arial,sans-serif;
font-size:16px;
text-align:center;
font-weight:bold;
}
.css2{
position:absolute;top:0px;left:0px;
width:10px;height:10px;
font-family:Arial,sans-serif;
font-size:10px;
text-align:center;
}
//-->
</style>
<script language="JavaScript">
<!-- Mouse Follow Clock from Rainbow Arch -->
<!-- This script and many more from : -->
<!-- http://rainbow.arch.scriptmania.com -->

<!-- Mouse Follow Clock from www.rainbow.arch.scriptmania.com
//Hide from older browsers 
if (document.getElementById&&!document.layers){

// *** Clock colours
dCol='#000000';   //date colour.
fCol='#000000';   //face colour.
sCol='#000000';   //seconds colour.
mCol='#000000';   //minutes colour.
hCol='#000000';   //hours colour.

// *** Controls
del=0.6;  //Follow mouse speed.
ref=40;   //Run speed (timeout).

//  Alter nothing below!  Alignments will be lost!
var ieType=(typeof window.innerWidth != 'number');
var docComp=(document.compatMode);
var docMod=(docComp && docComp.indexOf("CSS") != -1);
var ieRef=(ieType && docMod)
?document.documentElement:document.body;
theDays=new Array("SUNDAY","MONDAY","TUESDAY","WEDNESDAY","THURSDAY","FRIDAY","SATURDAY");
theMonths=new Array("JANUARY","FEBRUARY","MARCH","APRIL","MAY","JUNE","JULY","AUGUST","SEPTEMBER","OCTOBER","NOVEMBER","DECEMBER");
date=new Date();
day=date.getDate();
year=date.getYear();
if (year < 2000) year=year+1900; 
tmpdate=" "+theDays[date.getDay()]+" "+day+" "+theMonths[date.getMonth()]+" "+year;
D=tmpdate.split("");
N='3 4 5 6 7 8 9 10 11 12 1 2';
N=N.split(" ");
F=N.length;
H='...';
H=H.split("");
M='....';
M=M.split("");
S='.....';
S=S.split("");
siz=40;
eqf=360/F;
eqd=360/D.length;
han=siz/5.5;
ofy=-7;
ofx=-3;
ofst=70;
tmr=null;
vis=true;
mouseY=0;
mouseX=0;
dy=new Array();
dx=new Array();
zy=new Array();
zx=new Array();
tmps=new Array();
tmpm=new Array(); 
tmph=new Array();
tmpf=new Array(); 
tmpd=new Array();
var sum=parseInt(D.length+F+H.length+M.length+S.length)+1;
for (i=0; i < sum; i++){
dy[i]=0;
dx[i]=0;
zy[i]=0;
zx[i]=0;
}

algn=new Array();
for (i=0; i < D.length; i++){
algn[i]=(parseInt(D[i]) || D[i]==0)?10:9;
document.write('<div id="_date'+i+'" class="css2" style="font-size:'+algn[i]+'px;color:'+dCol+'">'+D[i]+'<\/div>');
tmpd[i]=document.getElementById("_date"+i).style;
}
for (i=0; i < F; i++){
document.write('<div id="_face'+i+'" class="css2" style="color:'+fCol+'">'+N[i]+'<\/div>');
tmpf[i]=document.getElementById("_face"+i).style; 
}
for (i=0; i < H.length; i++){
document.write('<div id="_hours'+i+'" class="css1" style="color:'+hCol+'">'+H[i]+'<\/div>');
tmph[i]=document.getElementById("_hours"+i).style;
}
for (i=0; i < M.length; i++){
document.write('<div id="_minutes'+i+'" class="css1" style="color:'+mCol+'">'+M[i]+'<\/div>');
tmpm[i]=document.getElementById("_minutes"+i).style; 
}
for (i=0; i < S.length; i++){
document.write('<div id="_seconds'+i+'" class="css1" style="color:'+sCol+'">'+S[i]+'<\/div>');
tmps[i]=document.getElementById("_seconds"+i).style;         
}

function onoff(){
if (vis){ 
 vis=false;
 document.getElementById("control").value="Clock On";
 }
else{ 
 vis=true;
 document.getElementById("control").value="Clock Off";
 Delay();
 }
kill();
}

function kill(){
if (vis) 
 document.onmousemove=mouse;
else 
 document.onmousemove=null;
} 

function mouse(e){
var msy = (!ieType)?window.pageYOffset:0;
if (!e) e = window.event;    
 if (typeof e.pageY == 'number'){
  mouseY = e.pageY + ofst - msy;
  mouseX = e.pageX + ofst;
 }
 else{
  mouseY = e.clientY + ofst - msy;
  mouseX = e.clientX + ofst;
 }
if (!vis) kill();
}
document.onmousemove=mouse;

function winDims(){
winH=(ieType)?ieRef.clientHeight:window.innerHeight; 
winW=(ieType)?ieRef.clientWidth:window.innerWidth;
}
winDims();
window.onresize=new Function("winDims()");

function ClockAndAssign(){
time = new Date();
secs = time.getSeconds();
sec = Math.PI * (secs-15) / 30;
mins = time.getMinutes();
min = Math.PI * (mins-15) / 30;
hrs = time.getHours();
hr = Math.PI * (hrs-3) / 6 + Math.PI * parseInt(time.getMinutes()) / 360;

for (i=0; i < S.length; i++){
 tmps[i].top=dy[D.length+F+H.length+M.length+i]+ofy+(i*han)*Math.sin(sec)+scrollY+"px";
 tmps[i].left=dx[D.length+F+H.length+M.length+i]+ofx+(i*han)*Math.cos(sec)+"px";
 }
for (i=0; i < M.length; i++){
 tmpm[i].top=dy[D.length+F+H.length+i]+ofy+(i*han)*Math.sin(min)+scrollY+"px";
 tmpm[i].left=dx[D.length+F+H.length+i]+ofx+(i*han)*Math.cos(min)+"px";
 }
for (i=0; i < H.length; i++){
 tmph[i].top=dy[D.length+F+i]+ofy+(i*han)*Math.sin(hr)+scrollY+"px";
 tmph[i].left=dx[D.length+F+i]+ofx+(i*han)*Math.cos(hr)+"px";
 }
for (i=0; i < F; i++){
 tmpf[i].top=dy[D.length+i]+siz*Math.sin(i*eqf*Math.PI/180)+scrollY+"px";
 tmpf[i].left=dx[D.length+i]+siz*Math.cos(i*eqf*Math.PI/180)+"px";
 }
for (i=0; i < D.length; i++){
 tmpd[i].top=dy[i]+siz*1.5*Math.sin(-sec+i*eqd*Math.PI/180)+scrollY+"px";
 tmpd[i].left=dx[i]+siz*1.5*Math.cos(-sec+i*eqd*Math.PI/180)+"px";
 }
if (!vis)clearTimeout(tmr);
}

buffW=(ieType)?80:90;
function Delay(){
scrollY=(ieType)?ieRef.scrollTop:window.pageYOffset;
if (!vis){
 dy[0]=-100;
 dx[0]=-100;
}
else{
 zy[0]=Math.round(dy[0]+=((mouseY)-dy[0])*del);
 zx[0]=Math.round(dx[0]+=((mouseX)-dx[0])*del);
}
for (i=1; i < sum; i++){
 if (!vis){
  dy[i]=-100;
  dx[i]=-100;
 }
 else{
  zy[i]=Math.round(dy[i]+=(zy[i-1]-dy[i])*del);
  zx[i]=Math.round(dx[i]+=(zx[i-1]-dx[i])*del);
 }
if (dy[i-1] >= winH-80) dy[i-1]=winH-80;
if (dx[i-1] >= winW-buffW) dx[i-1]=winW-buffW;
}

tmr=setTimeout('Delay()',ref);
ClockAndAssign();
}
window.onload=Delay;
}
//-->
</script>


<!END MOUSE CLOCK FROM http://rainbow.arch.scriptmania.com/scripts/mouse_clock.html>





<head>
<meta content="en-us" http-equiv="Content-Language" />
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Lil' Bits Computer Hardware</title>
<style type="text/css">
.auto-style1 {
	font-size: xx-large;
}
.auto-style2 {
	font-size: 40pt;
}
.auto-style3 {
	text-align: right;
}
.auto-style4 {
	font-size: x-large;
}
.auto-style5 {
	text-align: center;
}
.auto-style6 {
	text-align: center;
	text-decoration: underline;
}
</style>
</head>

<body bgcolor="#CCFFFF">

<?php
insertTopOfPage();
if (!empty($_SESSION['name'])) {
	echo "<br>Hello, " . $_SESSION['name']. "!";
}



echo '<p>&nbsp;</p><p><form method="get"><div class="row">';
echo 'Search: <input type="text" name="search" style="width:50%" value="' . $search . '">  ';
echo 'Min Price: <input type="number" name="minPrice" min="1" style="width:5%" value="' . $minPrice . '">  
	Max Price: <input type="number" name="maxPrice" min="1" style="width:5%" value="' . $maxPrice . '">  ';
addCategoriesDropdown($category);
echo '<button type="submit">Search</button></div></form></p>';

echo'<p>&nbsp;</p>
	<p class="auto-style4">' . "All Items$searchMessage$categoryMessage$priceMessage:" . '</p>';

echo '<table style="width: 100%">
		<tr>
			<td class="auto-style6" width="200"><strong>Name</strong></td>
			<td class="auto-style6"><strong>Description</strong></td>
			<td class="auto-style6" width="100"><strong>List Price</strong></td>
			<td class="auto-style6" width="100"><strong>Auction Price</strong></td>
		</tr>';

endAuctions();
$query = "SELECT DISTINCT(I.pid), D.name, D.description, I.list_price, I.auction_price, I.bid_end,  
	B.auction_price2
	FROM Owns O, IsIn N, Items I
	LEFT JOIN (Select B.pid, Max(B.amount) as auction_price2 From Bid B GROUP BY B.pid) B
	ON B.pid = I.pid
	LEFT JOIN ItemDesc D
	ON D.upc = I.upc
	WHERE I.upc = D.upc 
	AND (I.bid_end = 0 OR (I.bid_end > NOW() AND I.bid_start <= NOW()) OR I.list_price > 0)
	AND I.included_in = 1 AND I.upc = N.upc AND O.pid = I.pid $extraQueryConditions
	ORDER BY (D.name)";

$rs = mysql_query($query);
$count = 0;

while ($row = mysql_fetch_assoc($rs)) {
	$count = $count + 1;
	echo "<tr>";
	echo "<td class=\"auto-style5\"><a href=" . getItemURL($row['pid']) . ">" . 
		$row['name'] . "</td>" .
		"<td class=\"auto-style5\">" . $row['description'] . "</td><td>";
		
	if (is_null($row['list_price'])) {
		echo "Auction only";
	} else {
		echo "$" . $row['list_price'];
	}

	echo "</td><td class=\"auto-style5\">";

	if (is_null($row['auction_price'])) {
		echo "Buy only";
	} else if (time() < strtotime($row['bid_end'])) {
		if ($row['auction_price']>$row['auction_price2']) {
			echo "$" . $row['auction_price'];
			}
		else {
			echo "$" . $row['auction_price2'];
			}
	} else {
		echo "Auction ended with no winner";
	}

	echo "</td></tr>";

}

if ($count == 0) {
	echo '<tr><td>No items found</td></tr>';
}

?>
</table>

</body>

</html>
