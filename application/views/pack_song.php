<html>
  <head>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
<script>
$(document).ready(function(){
console.log("js is working");

    $(".charts").click(function(){
        $(this) // the current a-element that was clicked
            .closest('.charts-container') // .product
                .find('.charts-details')
                    .slideToggle("slow");
    });
});

</script>


<style>

table a:link {
	color: #666;
	font-weight: bold;
	text-decoration:none;
}
table a:visited {
	color: #999999;
	font-weight:bold;
	text-decoration:none;
}
table a:active,
table a:hover {
	color: #bd5a35;
	text-decoration:underline;
}
table {
	font-family:Arial, Helvetica, sans-serif;
	color:#666;
	font-size:12px;
	text-shadow: 1px 1px 0px #fff;
	background:#eaebec;
	margin:20px;
	border:#ccc 1px solid;

	-moz-border-radius:3px;
	-webkit-border-radius:3px;
	border-radius:3px;

	-moz-box-shadow: 0 1px 2px #d1d1d1;
	-webkit-box-shadow: 0 1px 2px #d1d1d1;
	box-shadow: 0 1px 2px #d1d1d1;
}
table th {
	padding:21px 25px 22px 25px;
	border-top:1px solid #fafafa;
	border-bottom:1px solid #e0e0e0;

	background: #ededed;
	background: -webkit-gradient(linear, left top, left bottom, from(#ededed), to(#ebebeb));
	background: -moz-linear-gradient(top,  #ededed,  #ebebeb);
}
table th:first-child {
	text-align: left;
	padding-left:20px;
}
table tr:first-child th:first-child {
	-moz-border-radius-topleft:3px;
	-webkit-border-top-left-radius:3px;
	border-top-left-radius:3px;
}
table tr:first-child th:last-child {
	-moz-border-radius-topright:3px;
	-webkit-border-top-right-radius:3px;
	border-top-right-radius:3px;
}
table tr {
	text-align: center;
	padding-left:20px;
}
table td:first-child {
	text-align: left;
	padding-left:20px;
	border-left: 0;
}
table td {
	padding:18px;
	border-top: 1px solid #ffffff;
	border-bottom:1px solid #e0e0e0;
	border-left: 1px solid #e0e0e0;

	background: #fafafa;
	background: -webkit-gradient(linear, left top, left bottom, from(#fbfbfb), to(#fafafa));
	background: -moz-linear-gradient(top,  #fbfbfb,  #fafafa);
}
table tr.even td {
	background: #f6f6f6;
	background: -webkit-gradient(linear, left top, left bottom, from(#f8f8f8), to(#f6f6f6));
	background: -moz-linear-gradient(top,  #f8f8f8,  #f6f6f6);
}
table tr:last-child td {
	border-bottom:0;
}
table tr:last-child td:first-child {
	-moz-border-radius-bottomleft:3px;
	-webkit-border-bottom-left-radius:3px;
	border-bottom-left-radius:3px;
}
table tr:last-child td:last-child {
	-moz-border-radius-bottomright:3px;
	-webkit-border-bottom-right-radius:3px;
	border-bottom-right-radius:3px;
}
table tr:hover td {
	background: #f2f2f2;
	background: -webkit-gradient(linear, left top, left bottom, from(#f2f2f2), to(#f0f0f0));
	background: -moz-linear-gradient(top,  #f2f2f2,  #f0f0f0);	
}

h2 {
    font-family: "BebasNeueRegular",sans-serif;
    color: #333;
}

.charts-details {
	display: none;
}

.charts {
	width:100px;;

}
</style>

    <title>Stepmania Song Search</title>
  </head>
    <body>



<?php

echo "<h2>$pack->packname - ".round($pack->size_bytes/1024/1024)."MB</h2>";
echo "<table>";
echo "<th>Song Title</th>";
echo "<th>Artist</th>";
echo "<th>Banner</th>";

foreach($songs as $song){

	echo "<pre>";
	//print_r($song);
	echo "</pre>";
	echo "<tr>";
	echo "<td>$song[title]</td><td>$song[artist]</td>";
	echo "<td>";
	if (!empty($song['banner'])) echo "<img src=\"/static/images/songs/$song[banner]\"";
	echo "</td>";
	echo "<td class='charts-container'>";

	echo "<div class='charts'>";
	echo "Show ".sizeof($song['notes'])." Charts";
	echo "</div>";
	echo "<table class='charts-details'>";
	echo "<th>Difficulty</th>";
        echo "<th>Meter</th>";
	echo "<th>Type</th>";
	echo "<th>Taps</th>";
	echo "<th>Jumps</th>";
        echo "<th>Holds</th>";
        echo "<th>Rolls</th>";
	foreach($song['notes'] as $key=>$chart){
		//print_r($chart);
		echo "<tr>";
		echo "<td>$chart[difficulty]</td>";
                echo "<td>$chart[meter]</td>";
                echo "<td>$chart[type]</td>";
                echo "<td>$chart[taps]</td>";
                echo "<td>$chart[jumps]</td>";
                echo "<td>$chart[holds]</td>";
	       	echo "<td>$chart[rolls]</td>";
		echo "</tr>";
	}
	echo "</table>";


	echo "</td>";
	echo "</tr>";
}

echo "</table>";

?>



  </body>
</html>
