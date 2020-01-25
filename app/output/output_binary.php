<?php
header('Content-Type: '.$rc->mime);
readfile($rc->path);