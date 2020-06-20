<?php 
  
  $config = include("config.php");
  include("simple_vars.php");
  include("header.php");
  include("side_nav.php");

  if(isset($_GET['check'])){
    exec("apt update");
  }

  if(isset($_GET['upgrade'])){
    exec("apt upgrade -y");
  }

  exec("apt list --upgradeable | awk '{print $1}' | grep -v Listing", $packages_array);

?>

 <main class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-2">
    <h2>Updates</h2>
    <a href="?check" class="btn btn-outline-primary" onclick="$('#cover-spin').show(0)">Check For OS Updates</a>
    <?php
    if(!empty($packages_array)){
    ?>  
      <a href="?upgrade" class="btn btn-outline-secondary" onclick="$('#cover-spin').show(0)">Upgrade OS Packages</a>
    <?php
    }
    ?>
    <a href="post.php?upgrade_simpnas_overwrite_local_changes" class="btn btn-outline-secondary" onclick="$('#cover-spin').show(0)">Upgrade SimpNAS</a>
  </div>
  <?php
  if(!empty($packages_array)){
  ?>
    <div class="table-responsive">
      <table class="table table-striped">
        <thead>
          <tr>
            <th>Package</th>
            <th>Current Version</th>
            <th>New Version</th>
          </tr>
        </thead>
        <tbody> 
          <?php
          
          foreach($packages_array as $package){
            $nice_package_name = exec("apt list --upgradeable | grep '$package' | awk -F/ '{print $1}'");
            $current_version = str_replace(']','',exec("apt list --upgradeable | grep '$package' | awk '{print $6}'"));
            $new_version = exec("apt list --upgradeable | grep '$package' | awk '{print $2}'");

          ?>
          <tr>
            <td><?php echo $nice_package_name; ?></td>
            <td><?php echo $current_version; ?></td>
            <td><?php echo $new_version; ?></td>
          </tr>
          <?php
          }
          ?>
        </tbody>
      </table>
      
    </div>

  <?php
  }else{
  ?>
    <h3 class="text-center text-secondary mt-5">Nothing to Update</h3>
  <?php
  }
  ?>    
</main>

<?php include("footer.php"); ?>