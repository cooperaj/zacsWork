<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" debug="<?php echo $this->debug ?>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php if ( isset( $layout->title ) ) echo $layout->title; ?></title>

<?php echo $layout->js(); ?>

<link href="<?php echo $this->basepath . $this->lnfpath; ?>public/reset.css" rel="stylesheet" type="text/css" media="screen print" />
<link href="<?php echo $this->basepath . $this->lnfpath; ?>public/style.css" rel="stylesheet" type="text/css" media="screen" />
<link href="<?php echo $this->basepath . $this->lnfpath; ?>public/dialog.css" rel="stylesheet" type="text/css" media="screen" />
<?php echo $layout->css() ?>

</head>

<body>

	<div id="parent">

		<div id="container">
	
			<?php if ( isset( $this->primary_links ) ) { ?>
			<ul class="site_links">
			<?php if(is_array($this->primary_links)) {
			        foreach ($this->primary_links as $link) { ?>
				<li><?php echo $link ?></li>
			<?php   } //foreach
                  } else { ?>
                <li><?php echo $this->primary_links ?></li> 
            <?php } ?>
			</ul>
			<?php } //if ?>

			<div id="header">
				<?php if ( isset( $layout->sitename ) ) { ?>
				<h1><?php echo $layout->sitename; ?></h1>	
				<?php } //if ?>	
			</div>
		
			<?php echo $this->content; ?>
		
		</div>
		
	</div>
	
</body>
</html>
