<?php ?>
<head>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.0/jquery.min.js"></script>
</head>







<script>

function scan(){
	$.get( "scan/extractPacks", function( data ) {
  		$( ".result" ).append( data );
  		scan();
	});
};


scan();
</script>


<html>

	<div class="result">



	</div>


</html>
