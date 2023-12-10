<?php

function dd($value) {
  echo '<pre>';
  var_dump($value);
  echo '</pre>';

  die();
}

function basePath($path) {
 return BASE_PATH . $path;
}
