<div class="row">
  <div class="col-lg-8 col-lg-offset-2">
    <form class="" action="/" method="POST">
   
      <div class="input-group">
	<select name="type" class="show-tick selectpicker " title="Type">
    	  <option value="title">Title</option>
    	  <option value="artist">Artist</option>
    	  <option selected="selected" value="packs">Packs</option>
  	</select>
        <input type="text" id="search" name="search" class="form-control" aria-label="..." value="<?php echo @$pack->packname ?>">
        <div class="input-group-btn">
          <button type="submit" name="submit" class="btn btn-success">Search</button>
        </div>
      </div><!-- /input-group -->

    </form>
  </div><!-- /.col-lg-6 -->
</div><!-- /.row -->

<?php if(isset($pack)): ?>
	<div class="container">
	  <h2 style="color: #fff;"><?php echo $pack->packname; ?> contains <?php echo sizeof($songs); ?> Songs </h2>
	<h4 style="color: #fff;"><a href="/link/<?php echo $pack->packname.".zip"; ?>">Download</a></h4>
	  <table style="background-color: #f5f5f5;" class="table">
    

    	<thead>
	      <tr>
		<th>Title</th>
		<th>Artist</th>
		<th>Subtitle</th>
		<th>Credit</th>
		<th>Banner</th>
		<th>Date</th>
	      </tr>
	</thead>

	<tbody>
	<?php foreach($songs as $song): ?>
<?php //echo "<pre>"; print_r($song); "</pre>" ?>
	<tr>
		<td style="vertical-align: middle;"><?php echo $song['title']; ?></td>
                <td style="vertical-align: middle;"><?php echo $song['artist']; ?></td>
                <td style="vertical-align: middle;"><?php echo $song['subtitle']; ?></td>
                <td style="vertical-align: middle;"><?php echo $song['credit']; ?></td>
                <td><?php if($song['banner']): ?><img style="max-height:100px;" class="img-responsive img-rounded" src="/static/images/songs/<?php echo $song['banner']; ?>"</img><?php endif; ?></td>
                <td style="vertical-align: middle;"><?php echo $song['date']; ?></td>
	</tr>
	<?php endforeach; ?>





    </tbody>
  </table>
</div>


<?php endif; ?>
