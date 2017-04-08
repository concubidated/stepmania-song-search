<div class="row">
  <div class="col-lg-8 col-lg-offset-2">
    <form class="" action="/" method="POST">
   
      <div class="input-group">
	<select name="type" class="show-tick selectpicker " title="Type">
    	  <option <?php if(@$type=='title') echo 'selected="selected"';?> value="title">Title</option>
    	  <option <?php if(@$type=='artist') echo 'selected="selected"';?> value="artist">Artist</option>
    	  <option <?php if(@$type=='packs') echo 'selected="selected"';?> value="packs">Packs</option>
  	</select>
        <input type="text" id="search" name="search" class="form-control" aria-label="...">
        <div class="input-group-btn">
          <button type="submit" name="submit" class="btn btn-success">Search</button>
        </div>
      </div><!-- /input-group -->

    </form>
  </div><!-- /.col-lg-6 -->
</div><!-- /.row -->


<?php if(isset($results)): ?>
	<div class="container">
	  <h2 style="color: #fff;"><?php echo sizeof($results); ?> Results for <?php echo $type?>: <?php echo $search?></h2>
	  <table style="background-color: #f5f5f5;" class="table">
    
	<?php if($type == "title" || $type == "artist") : ?>
    	<thead>
	      <tr>
		<th>Title</th>
		<th>Artist</th>
		<th>Banner</th>
		<th>Pack</th>
		<th class="text-center">Download</th>
	      </tr>
	</thead>

	<tbody>
	<?php foreach($results as $song): ?>
	<tr>
		<td class="vertical-align-middle"><?php echo $song['title']; ?></td>
		<td class="vertical-align-middle"><?php echo $song['artist']; ?></td>
		<td><?php if($song['banner']): ?><img style="max-height:100px;" class="img-responsive img-rounded" src="/static/images/songs/<?php echo $song['banner']; ?>"</img><?php endif; ?></td>
		<td class="vertical-align-middle" ><a href="/pack/id/<?php echo urlencode($song['packname']); ?>" ><?php echo $song['packname']; ?></a></td>
		<td class="text-center vertical-align-middle"><a href="http://simfiles.stepmania-online.com/<?php echo $song['packname'].".zip"; ?>" ><i class="material-icons">file_download</i></a></td>

	</tr>
	<?php endforeach; ?>
	<?php endif; ?>


	<?php if($type == "packs"): ?>
	 <thead>
	      <tr>
		<th>Pack</th>
		<th>Size</th>
		<th>Song Count</th>
		<th class="text-center">Download</th>
	      </tr>
	    </thead>

		<tbody>
		<?php foreach($results as $pack): ?>
		<tr>
			<td><a href="/pack/id/<?php echo $pack['id']; ?>" ><?php echo $pack['packname']; ?></a></td>
			<td><?php echo round($pack['size']/1024/1024); ?> MB</td>
			<td><?php echo $pack['songcount'] ?></td>
			<td class="text-center"><a href="http://simfiles.stepmania-online.com/<?php echo $pack['packname'].".zip"; ?>" ><span class="glyphicon glyphicon-download-alt"></span></a></td>

		</tr>
		<?php endforeach; ?>
	<?php endif; ?>


    </tbody>
  </table>
</div>


<?php endif; ?>
