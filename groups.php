<?php 
  
  $config = include("config.php");
  include("simple_vars.php");
  include("header.php");
  include("side_nav.php");

  exec("awk -F: '$3 > 999 {print $1}' /etc/group | grep -v nogroup", $group_array);

?>

<main class="col-md-9 ml-sm-auto col-lg-10 pt-3 px-4">

  <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-2">
    <h2>Groups</h2>
    <a href="group_add.php" class="btn btn-outline-primary">Add Group</a>
  </div>

  <?php include("alert_message.php"); ?>

  <div class="table-responsive">
    <table class="table table-striped">
      <thead>
        <tr>
          <th>Group</th>
          <th>Users</span>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php 
        foreach ($group_array as $group){
          $users = str_replace(',',', ',exec("awk -F: '/^$group/ {print $4;}' /etc/group"));
          if(empty($users)){
            $users = "-";
          }
          
        ?>
        
        <tr>    
          <td><strong><span class="mr-2" data-feather="users"></span><?php echo $group; ?></strong></td>
          <td><?php echo $users; ?></td>
          <td>
            <?php if($group !== "admins"){ ?>
            <div class="btn-group mr-2">
              <a href="group_edit.php?group=<?php echo $group; ?>" class="btn btn-outline-secondary"><span data-feather="edit"></span></a>
              <a href="post.php?group_delete=<?php echo $group; ?>" class="btn btn-outline-danger"><span data-feather="trash"></span></a>
            </div>
            <?php }else{ ?>
            <div class="p-3"></div> 
            <?php } ?>  
          </td>
        </tr>
        
        <?php 
        unset($group_list_array);
        } 

        ?>
      
      </tbody>
    </table>
  
  </div>

</main>

<?php include("footer.php"); ?>