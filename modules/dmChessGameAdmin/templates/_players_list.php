<?php
include_partial('dmAdminGenerator/relationForeign', array('record' => $dm_chess_game, 'alias' => 'Players', 'options' => array(
  'new' => false
)));