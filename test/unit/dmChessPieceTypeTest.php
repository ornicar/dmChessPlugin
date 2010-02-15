<?php

require_once(dirname(__FILE__).'/helper/dmChessUnitTestHelper.php');

$helper = new dmChessUnitTestHelper();
$helper->boot();

$t = new lime_test();

$t->comment('Create a new game');

$game = dmDb::table('DmChessPlayer')->startNewGame()->Game;

dmDb::table('DmChessPlayer')->joinGame($game);

$gameId = $game->id;
$game->free(true);
unset($game);

$t->comment('Reload game from DB');
$game = dmDb::table('DmChessGame')->preload($gameId);

$helper->checkPieceSquareInteraction($game, $t);

foreach($game->Pieces as $piece)
{
  $t->isa_ok($piece, $class = 'DmChess'.ucfirst($piece->type), $piece.' is a '.$class);
}