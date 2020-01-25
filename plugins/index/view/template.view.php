<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
  <title><?php if ( isset( $layout->title ) ) echo $layout->title; ?></title>

  <meta http-equiv="content-type" content="text/html; charset=utf-8" />
  <meta name="robots" content="noindex,nofollow" />
  <meta name="DC.title" content="<?php if ( isset( $layout->title ) ) echo $layout->title; ?>" />
  <meta name="DC.creator" content="networkPie.co.uk" />

  <link rel="shortcut icon" href="<?php echo $this->basepath . $layout->assetpath ?>favicon.ico" type="image/x-icon" />
  <?php echo $layout->js(); ?>
  <?php echo $layout->css() ?>
</head>
<body>
  
  <?php echo $this->content; ?>
 
</body>
</html>