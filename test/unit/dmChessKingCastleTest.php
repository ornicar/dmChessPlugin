<?php

require_once(dirname(__FILE__).'/helper/dmChessUnitTestHelper.php');

$helper = new dmChessUnitTestHelper();
$helper->boot();

$helper->setLimeTest($t = new lime_test());

$t->comment('Create a new game');

$player1 = dmDb::table('DmChessPlayer')->startNewGame();

$player2 = dmDb::table('DmChessPlayer')->joinGame($player1->Game);

$game = $player1->Game;
$board = $game->Board;

$king = $player1->King;

$t->is($board->getPieceByHumanPos('e1'), $king, 'King is on e1');

$t->ok($king->canCastleKingside(), 'King can castle kingside');
$t->ok($king->canCastleQueenside(), 'King can castle queenside');

$t->comment('Test kingside');

$t->ok(!in_array('s71', $king->getTargetKeys()), 'King cannot go to s71');

$helper
->move($game, 'g2', 'g3')
->move($game, 'f1', 'h3');

$t->ok(!in_array('s71', $king->getTargetKeys()), 'King cannot go to s71');

$helper
->move($game, 'g1', 'f3');

$t->ok(in_array('s71', $king->getTargetKeys()), 'King can go to s71');

$t->comment('Move kingside rook');

$helper
->move($game, 'h3', 'f5') // move bishop, it's in the way
->move($game, 'h2', 'h4')
->move($game, 'h1', 'h3');

$t->ok(!$king->canCastleKingside(), 'King cannot castle kingside');
$t->ok($king->canCastleQueenside(), 'King can castle queenside');
$t->ok(!in_array('s71', $king->getTargetKeys()), 'King cannot go to s71');

$helper
->move($game, 'h3', 'h1');

$t->ok(!$king->canCastleKingside(), 'King cannot castle kingside');
$t->ok($king->canCastleQueenside(), 'King can castle queenside');
$t->ok(!in_array('s71', $king->getTargetKeys()), 'King cannot go to s71');

$t->comment('Test queenside');

$t->ok(!in_array('s31', $king->getTargetKeys()), 'King cannot go to s31');

$helper
->move($game, 'd2', 'd3')
->move($game, 'c1', 'g5');

$t->ok(!in_array('s31', $king->getTargetKeys()), 'King cannot go to s31');

$helper
->move($game, 'd1', 'd2')
->move($game, 'b1', 'a3');

$t->ok(in_array('s31', $king->getTargetKeys()), 'King can go to s31');

$t->comment('Do castle queenside');

$t->is($rook = $board->getPieceByHumanPos('a1'), $player1->Rooks[0], 'Player first rook is on a1');

$helper->move($game, 'e1', 'c1');

$t->is($board->getPieceByHumanPos('c1'), $king, 'King is on c1');
$t->is((string) $board->getPieceByHumanPos('d1'), (string) $rook, 'Rook is on d1');

$events = array(
  array(
    'action'  => 'piece_move',
    'piece'   => $king->id,
    'from'    => $board->humanPosToKey('e1'),
    'to'      => $board->humanPosToKey('c1')
  ),
  array(
    'action'    => 'piece_castle',
    'side'      => 'queen',
    'king'      => $king->id,
    'king_from' => $board->humanPosToKey('e1'),
    'king_to'   => $board->humanPosToKey('c1'),
    'rook'      => $rook->id,
    'rook_from' => $board->humanPosToKey('a1'),
    'rook_to'   => $board->humanPosToKey('d1')
  )
);

$t->is_deeply($player1->getEvents(), $events, 'The move event and castle event have been recorded');
$t->is($player1->getStringEvents(), $stringEvents = 'e1 c1', 'The string event is '.$stringEvents);

$t->ok(!$king->canCastleKingside(), 'King cannot castle kingside');
$t->ok(!$king->canCastleQueenside(), 'King cannot castle queenside');

$t->comment('Create a new game');

$player1 = dmDb::table('DmChessPlayer')->startNewGame();

$player2 = dmDb::table('DmChessPlayer')->joinGame($player1->Game);

$game = $player1->Game;
$board = $game->Board;

$king = $player1->King;

$t->is($board->getPieceByHumanPos('e1'), $king, 'King is on e1');

$t->ok($king->canCastleKingside(), 'King can castle kingside');
$t->ok($king->canCastleQueenside(), 'King can castle queenside');

$helper
->move($game, 'g2', 'g3')
->move($game, 'f1', 'h3')
->move($game, 'g1', 'f3');

$t->comment('Do castle kingside');

$t->is($rook = $board->getPieceByHumanPos('h1'), $player1->Rooks[1], 'Player second rook is on h1');

$helper->move($game, 'e1', 'g1');

$t->is($board->getPieceByHumanPos('g1'), $king, 'King is on g1');
$t->is((string) $board->getPieceByHumanPos('f1'), (string) $rook, 'Rook is on f1');

$events = array(
  array(
    'action'  => 'piece_move',
    'piece'   => $king->id,
    'from'    => $board->humanPosToKey('e1'),
    'to'      => $board->humanPosToKey('g1')
  ),
  array(
    'action'    => 'piece_castle',
    'side'      => 'king',
    'king'      => $king->id,
    'king_from' => $board->humanPosToKey('e1'),
    'king_to'   => $board->humanPosToKey('g1'),
    'rook'      => $rook->id,
    'rook_from' => $board->humanPosToKey('h1'),
    'rook_to'   => $board->humanPosToKey('f1')
  )
);

$t->is_deeply($player1->getEvents(), $events, 'The move event and castle event have been recorded');
$t->is($player1->getStringEvents(), $stringEvents = 'e1 g1', 'The string event is '.$stringEvents);

$t->ok(!$king->canCastleKingside(), 'King cannot castle kingside');
$t->ok(!$king->canCastleQueenside(), 'King cannot castle queenside');

$t->comment('Create a new game');

$player1 = dmDb::table('DmChessPlayer')->startNewGame();
$player2 = dmDb::table('DmChessPlayer')->joinGame($player1->Game);

$game = $player1->Game;
$board = $game->Board;

$king = $player1->King;

$t->is($board->getPieceByHumanPos('e1'), $king, 'King is on e1');

$t->ok($king->canCastleKingside(), 'King can castle kingside');
$t->ok($king->canCastleQueenside(), 'King can castle queenside');

$helper
->move($game, 'g2', 'g4')
->move($game, 'f2', 'f4')
->move($game, 'e7', 'e6')
->move($game, 'f1', 'h3')
->move($game, 'g1', 'f3');

$t->ok(in_array('s71', $king->getTargetKeys()), 'King can go to s71');

$helper
->move($game, 'd8', 'h4');

$t->ok($king->isAttacked(), 'Queen attacks king');

$t->ok(!in_array('s71', $king->getTargetKeys()), 'King cannot go to s71');

$helper
->move($game, 'h4', 'g4');

$t->ok(!$king->isAttacked(), 'Queen no more attacks king, but prevents castle');

$t->ok(!in_array('s71', $king->getTargetKeys()), 'King cannot go to s71');

$t->comment('Remove queen menace');
$helper
->move($game, 'g4', 'h5');

$t->ok(in_array('s71', $king->getTargetKeys()), 'King can go to s71');

$t->comment('Do castle kingside');

$t->is($rook = $board->getPieceByHumanPos('h1'), $player1->Rooks[1], 'Player second rook is on h1');

$helper->move($game, 'e1', 'g1');

$t->is($board->getPieceByHumanPos('g1'), $king, 'King is on g1');
$t->is((string) $board->getPieceByHumanPos('f1'), (string) $rook, 'Rook is on f1');

$events = array(
  array(
    'action'  => 'piece_move',
    'piece'   => $king->id,
    'from'    => $board->humanPosToKey('e1'),
    'to'      => $board->humanPosToKey('g1')
  ),
  array(
    'action'    => 'piece_castle',
    'side'      => 'king',
    'king'      => $king->id,
    'king_from' => $board->humanPosToKey('e1'),
    'king_to'   => $board->humanPosToKey('g1'),
    'rook'      => $rook->id,
    'rook_from' => $board->humanPosToKey('h1'),
    'rook_to'   => $board->humanPosToKey('f1')
  )
);

$t->is_deeply($player1->getEvents(), $events, 'The move event and castle event have been recorded');
$t->is($player1->getStringEvents(), $stringEvents = 'e1 g1', 'The string event is '.$stringEvents);

$t->ok(!$king->canCastleKingside(), 'King cannot castle kingside');
$t->ok(!$king->canCastleQueenside(), 'King cannot castle queenside');