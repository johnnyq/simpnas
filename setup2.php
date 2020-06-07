<?php

	if(file_exists('config.php')){
	  header("Location: login.php");
	}

	include("functions.php");
	$os_disk = exec("findmnt -n -o SOURCE --target / | cut -c -8");

	$current_time_zone = exec("timedatectl show -p Timezone --value");
    $current_local_date = exec("timedatectl show -p TimeUSec --value | awk '{print $2}'");
    $current_local_time = exec("timedatectl show -p TimeUSec --value | awk '{print $3}'");
    exec("timedatectl list-timezones", $timezones_array);

?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>SimpNAS | Setup</title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.min.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet">
  </head>

  <body>
    <nav class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0">
      <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="index.php"><span data-feather="box"></span> SimpNAS <small>(<?php echo gethostname(); ?>)</small></a>
      <ul class="navbar-nav px-3">
        <li class="nav-item text-nowrap">
          <a class="nav-link" href="login.php">Logout</a>
        </li>
      </ul>
    </nav>

    <div class="container-fluid">
      <div class="row">

<main class="col-md-12 ml-sm-auto col-lg-12 pt-3 px-4">
  <nav>
  <ol class="breadcrumb">
    <li class="breadcrumb-item"><a href="setup.php">Setup</a></li>
    <li class="breadcrumb-item"><a href="setup.php">Network Configuration</a></li>
    <li class="breadcrumb-item active">Final Configuration</li>
  </ol>
</nav>

	<?php
    //Alert Feedback
    if(!empty($_SESSION['alert_message'])){
      ?>
        <div class="alert alert-success alert-<?php echo $_SESSION['alert_type']; ?>" id="alert">
          <?php echo $_SESSION['alert_message']; ?>
          <button class='close' data-dismiss='alert'>&times;</button>
        </div>
      <?php
      
      $_SESSION['alert_type'] = '';
      $_SESSION['alert_message'] = '';

    }

  ?>
  
  <h2>Finalize Setup</h2>
  <hr>
  <form method="post" action="post.php" autocomplete="off">

	  <div class="form-group">
	    <label>Timezone:</label>
	    <select class="form-control" name="timezone" required>
	    	<?php
	    	foreach ($timezones_array as $timezone) {
	    	?>
	    	<option <?php if($current_time_zone === $timezone){ echo "selected"; } ?> ><?php echo $timezone; ?></option>
	    	<?php
	    	}
	    	?>
	    </select>
	  </div>

	  <div class="form-group">
	    <label>Disk</small></label>
	    <select class="form-control" name="disk" required>
	  	<?php
			exec("smartctl --scan | awk '{print $1}'", $drive_list);
			foreach ($drive_list as $hdd) {
				if( $hdd == "$os_disk" )continue;
				$hdd_short_name = basename($hdd);
                $hdd_vendor = exec("smartctl -i $hdd | grep 'Model Family:' | awk '{print $3,$4,$5}'");
			    if(empty($hdd_vendor)){
			      $hdd_vendor = exec("smartctl -i $hdd | grep 'Device Model:' | awk '{print $3,$4,$5}'");
			    }
			    if(empty($hdd_vendor)){
			      $hdd_vendor = exec("smartctl -i $hdd | grep 'Vendor:' | awk '{print $2,$3,$4}'");
			    }
			    if(empty($hdd_vendor)){
			      $hdd_vendor = "-";
			    }
			    $hdd_serial = exec("smartctl -i $hdd | grep 'Serial Number:' | awk '{print $3}'");
			    if(empty($hdd_serial)){
			      $hdd_serial = "-";
			    }
			    $hdd_label_size = exec("smartctl -i $hdd | grep 'User Capacity:' | cut -d '[' -f2 | cut -d ']' -f1");
			?>
			<option value="<?php echo $hdd; ?>"><?php echo "$hdd_short_name - $hdd_vendor ($hdd_label_size)"; ?></option>

		<?php
			}
		?>

	  </select>
	  <small class="form-text text-muted">Select a disk to create your first volume on. user home and docker directories will be created here. Note SimpNAS requires at minimum two hard drives one for the OS and other for the data.</small>
	  </div>
	  <div class="form-group">
	    <label>Volume Name</label>
	    <input type="text" class="form-control" name="volume_name" required>
	  </div>

	  <div class="form-group">
		<label>File Server Type</label>
		<select class="form-control" name="server_type" id="serverType">
			<option id="standAlone" value="standalone">File Server</option>
			<option id="activeDirectory" value="AD">Directory / File Server</option>
		</select>
	  </div>

	  <div id="activeDirectorySettings">
		  <div class="form-group">
		    <label>Domain</label>
		    <input type="text" class="form-control" name="ad_domain" placeholder="ex. company.int">
		  </div>
		  
		  <div class="form-group">
		    <label>NETBIOS Domain</label>
		    <input type="text" class="form-control" name="ad_netbios_domain">
		  </div>
		  
		  <div class="form-group">
		    <label>Administrator Password</label>
		    <input type="text" class="form-control" name="ad_admin_password">
		  </div>

		  <div class="form-group">
		    <label>DNS Forwarder(s)</label>
		    <input type="text" class="form-control" name="ad_dns_forwarders">
		  </div>
	  </div>

	  <div class="form-group">
	    <label>Admin Username</label>
	    <input type="text" class="form-control" name="username" required>
	  </div>
	  
	  <div class="form-group">
	    <label>Admin Password</label>
	    <input type="password" class="form-control" name="password" required autocomplete="new-password">
	  </div>

	  <legend>Send Statistic Data</legend>
	  <p>This will collect a Unique Machine ID used for Unique Installs on our Webpage.</p>
	  <div class="form-group">
	  	<div class="custom-control custom-checkbox">
		  <input type="checkbox" class="custom-control-input" name="collect" value="1" id="collect">
		  <label class="custom-control-label" for="collect">Yes Collect Statistic Data</label>
		</div>
	  </div>
	  
	  <button type="submit" name="setup_final" class="btn btn-primary">Submit</button>
	</form>
</main>

<?php include("footer.php"); ?>