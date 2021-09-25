<?php

function block_forumdashboard_getiteminstance($itemid) {
  $config = get_config('block_forumdashboard');
  if (!isset($config->metricitems)) {
    return null;
  }
  $items = json_decode($config->metricitems);

  foreach ($items as $item) {
    if ($item->id == $itemid) {
      return $item;
    }
  }

  return null;
}
