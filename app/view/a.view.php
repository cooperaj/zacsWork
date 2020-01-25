<?php // create the attributes list. this is by no means exhaustive.
$attrib = "";
$attrib .= isset( $this->title ) ? " title=\"" . $this->title . "\"" : ""; 
$attrib .= isset( $this->onclick ) ? " onclick=\"" . $this->onclick . "\"" : ""; ?>
<a href="<?php echo $this->basepath . $this->path; ?>"<?php echo $attrib; ?>><?php echo $this->text; ?></a>