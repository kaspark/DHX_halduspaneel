<!doctype html>
<html lang="en">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <title>DHX halduspaneel</title>
        <link href="css/bootstrap.min.css" rel="stylesheet">

    <style>
      .bd-placeholder-img {
        font-size: 1.125rem;
        text-anchor: middle;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
      }

      @media (min-width: 768px) {
        .bd-placeholder-img-lg {
          font-size: 3.5rem;
        }
      }
      .errorDesc {
        display: none;
      }
    </style>
    <!-- Custom styles for this template -->
    <link href="css/dashboard.css" rel="stylesheet">
  </head>
  <body>
    <nav class="navbar navbar-dark fixed-top bg-dark flex-md-nowrap p-0 shadow">
  <a class="navbar-brand col-sm-3 col-md-2 mr-0" href="index.php">DHX halduspaneel</a>
  <ul class="navbar-nav navbar-expand-lg">
    <li class="nav-item text-nowrap mr-4">
        <a class="nav-link disabled" href="#"><?php echo $_SESSION['user']; ?></a>
    </li>
    <li class="nav-item text-nowrap mr-4">
      <a class="nav-link" href="login.php?logout=1">Logi välja</a>
    </li>
  </ul>
</nav>

<div class="container-fluid">
  <div class="row">
    <?php if(count($inst_list) > 1) { ?>
    <nav class="col-md-2 d-none d-md-block bg-light sidebar">
      <div class="sidebar-sticky">

        <h6 class="sidebar-heading d-flex justify-content-between align-items-center px-3 mt-4 mb-1 text-muted">
          <span>Vali asutus</span>
        </h6>
        <ul class="nav flex-column mb-2">
		<?php
			//$listofDHX = doPDO("select datname from pg_database where datname like ? order by datname;", [$DB_wildcard])->fetchAll();
			//foreach ($listofDHX as $key => $val )
			foreach ($inst_list as $key => $val)
			{
				$active = ($key == $inst) ? "active" : "";
				echo '<li class="nav-item">
					<a class="nav-link '.$active.'" href="index.php?inst='.$key.'">
					  <span data-feather="file-text"></span>
					  '.$val[0].'
					</a>
				  </li>';
			}
		?>
        </ul>
      </div>
    </nav>
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-4">
    <?php } else { ?>
    <main role="main" class="w-100 px-4">
    <?php } ?>
	