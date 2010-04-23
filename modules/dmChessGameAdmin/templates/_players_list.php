<?php
echo $sf_context->getServiceContainer()->mergeParameter('related_records_view.options', array(
  'record'  => $dm_chess_game,
  'alias'   => 'Players',
  'new'     => false
))->getService('related_records_view')->render();