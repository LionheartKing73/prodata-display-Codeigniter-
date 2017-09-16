<head>
<base href="http://report-site.com/">
<link rel="stylesheet" type="text/css" href="public/bootstrap/css/bootstrap.min.css">
<link rel="stylesheet" type="text/css" href=public/bootstrap/css/bootstrap-theme.min.css">
<script type="text/javascript" src=public/bootstrap/js/bootstrap.min.js"></script>
<script type="text/javascript" src="public/js/jquery-1.11.2.min.js"></script>
</head>
<body>
  <div class="container">
   
  <h2><small>Ad status has been updated successfully!</small> <br /> Ad's Updated State is:</h2>
    <table class="table">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>GroupId</th>
        <th>Status</th>
      </tr>
    </thead>
    <tbody>
        <?php
                echo "<tr class="."success".">";  
                echo "<td>$adId</td>";              
                echo "<td>$adName</td>"; 
                echo "<td>$groupId</td>"; 
                echo "<td>$adStatus</td>"; 
                echo "</tr>";           
        ?>
      </tr>
    </tbody>
  </table>
</div>
</body>