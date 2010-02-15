<?php

require_once(dirname(__FILE__).'/helper/dmChessUnitTestHelper.php');

$helper = new dmChessUnitTestHelper();
$helper->boot();

$t = new lime_test();

$t->comment('Create a new game');

$player1 = dmDb::table('DmChessPlayer')->startNewGame();

$t->comment('Test player1');
$t->ok($player1 instanceof DmChessPlayer, 'Got a DmChessPlayer');
$t->ok($player1->exists(), 'Player exists');
$t->is($player1->color, 'white', 'Player color is white');
$t->is($player1->isCreator, true, 'Player is creator');
$t->is($player1->isWinner, false, 'Player is not winner');
$t->is($player1->isAi, false, 'Player is not an A.I.');
$t->is(strlen($player1->code), 8, 'Player has an 8 chars code: '.$player1->code);

$t->comment('Test game');
$game = $player1->Game;
$t->ok($game instanceof DmChessGame, 'Got a DmChessGame');
$t->ok($game->exists(), 'Game exists');
$t->is($game->isStarted, false, 'Game is not started');
$t->is($game->isFinished, false, 'Game is not finished');
$t->is($game->turns, 0, 'Game has 0 turns');
$t->is($game->Players->count(), 1, 'Game has one player');
$t->is($game->Players[0], $player1, 'Game player is $player1)');
$t->is(count($game->Pieces), 16, 'Game has 16 pieces');

$t->comment('Test board');
$board = $game->Board;

$t->isa_ok($board, 'dmChessBoard', 'Got a dmChessBoard');
$t->is(count($board->getSquares()), 64, 'The board has 64 squarres');
$t->ok($board->getSquareByPos(1, 1)->isBlack(), 'The board A1 square is black');
$t->ok($board->getSquareByPos(8, 1)->isWhite(), 'The board H1 square is white');
$t->ok($board->getSquareByPos(1, 8)->isWhite(), 'The board A8 square is white');
$t->ok($board->getSquareByPos(8, 8)->isBlack(), 'The board H8 square is black');
$t->ok($board->getSquareByPos(1, 2)->isWhite(), 'The board A2 square is white');
$t->ok($board->getSquareByPos(2, 1)->isWhite(), 'The board B1 square is white');
$t->is($board->humanPosToKey('b4'), 's24', 'b4 => s24');
$t->is($board->keyToHumanPos('s24'), 'b4', 's24 => b4');

$t->comment('Test pieces');
$t->is($player1->Pieces->count(), 16, 'Player has 16 pieces');
$t->isa_ok($player1->Pieces[0], 'DmChessPawn', 'Player first piece is a DmChessPawn');
$t->is(count($player1->Pawns), 8, 'Player has 8 pawns');
$t->isa_ok($player1->Pawns[0], 'DmChessPawn', 'Player first pawn is a DmChessPawn');
$t->isa_ok($player1->King, 'DmChessKing', 'Player king is a DmChessKing');
$t->isa_ok($player1->Queens[0], 'DmChessQueen', 'Player queen is a DmChessQueen');
$t->is(count($player1->Rooks), 2, 'Player has 2 rooks');
$t->isa_ok($player1->Rooks[0], 'DmChessRook', 'Player first rook is a DmChessRook');
$t->is(count($player1->Knights), 2, 'Player has 2 knights');
$t->isa_ok($player1->Knights[0], 'DmChessKnight', 'Player first knight is a DmChessKnight');
$t->is(count($player1->Bishops), 2, 'Player has 2 bishops');
$t->isa_ok($player1->Bishops[0], 'DmChessBishop', 'Player first bishop is a DmChessBishop');

$t->comment('Test piece position');

foreach(array(
  's12' => $player1->Pawns[0],
  's22' => $player1->Pawns[1],
  's32' => $player1->Pawns[2],
  's82' => $player1->Pawns[7],
  's11' => $player1->Rooks[0],
  's81' => $player1->Rooks[1],
  's21' => $player1->Knights[0],
  's71' => $player1->Knights[1],
  's31' => $player1->Bishops[0],
  's61' => $player1->Bishops[1],
  's41' => $player1->Queens[0],
  's51' => $player1->King,
) as $squareKey => $piece)
{
  $t->is($piece->getSquareKey(), $squareKey, 'Player '.$piece.' square key is '.$squareKey);
}

$helper->checkPieceSquareInteraction($game, $t);

$t->comment('Add a second player');

$player2 = dmDb::table('DmChessPlayer')->joinGame($game);

$t->comment('Test player2');
$t->ok($player2 instanceof DmChessPlayer, 'Got a DmChessPlayer');
$t->ok($player2->exists(), 'Player exists');
$t->is($player2->color, 'black', 'Player color is black');
$t->is($player2->isCreator, false, 'Player is not creator');
$t->is($player2->isWinner, false, 'Player is not winner');
$t->is($player2->isAi, false, 'Player is not an A.I.');
$t->is(strlen($player2->code), 8, 'Player has an 8 chars code: '.$player2->code);
$t->is($player1->getOpponent(), $player2, 'Player2 is player1 opponent');
$t->is($player2->getOpponent(), $player1, 'Player1 is player2 opponent');
$t->is($player1->isMyTurn(), true, 'Player1 turn: YES');
$t->is($player2->isMyTurn(), false, 'Player2 turn: FALSE');

$t->comment('Test game');
$game = $player2->Game;
$t->is((string)$player1->Game, (string)$player2->Game, 'Both player play on the same game');
$t->ok($game->exists(), 'Game exists');
$t->is($game->isStarted, true, 'Game is started');
$t->is($game->isFinished, false, 'Game is not finished');
$t->is($game->turns, 0, 'Game has 0 turns');
$t->is($game->Players->count(), 2, 'Game has two players');
$t->is($game->Players[0], $player1, 'Game player[0] is $player1)');
$t->is($game->Players[1], $player2, 'Game player[1] is $playerZ)');
$t->is(count($game->Pieces), 32, 'Game has 32 pieces');

$t->comment('Test pieces');
$t->is($player2->Pieces->count(), 16, 'Player has 16 pieces');
$t->isa_ok($player2->Pieces[0], 'DmChessPawn', 'Player first piece is a DmChessPawn');
$t->is(count($player2->Pawns), 8, 'Player has 8 pawns');
$t->isa_ok($player2->Pawns[0], 'DmChessPawn', 'Player first pawn is a DmChessPawn');
$t->isa_ok($player2->King, 'DmChessKing', 'Player king is a DmChessKing');
$t->isa_ok($player2->Queens[0], 'DmChessQueen', 'Player queen is a DmChessQueen');
$t->is(count($player2->Rooks), 2, 'Player has 2 rooks');
$t->isa_ok($player2->Rooks[0], 'DmChessRook', 'Player first rook is a DmChessRook');
$t->is(count($player2->Knights), 2, 'Player has 2 knights');
$t->isa_ok($player2->Knights[0], 'DmChessKnight', 'Player first knight is a DmChessKnight');
$t->is(count($player2->Bishops), 2, 'Player has 2 bishops');
$t->isa_ok($player2->Bishops[0], 'DmChessBishop', 'Player first bishop is a DmChessBishop');

$t->comment('Test piece position');

foreach(array(
  's17' => $player2->Pawns[0],
  's27' => $player2->Pawns[1],
  's37' => $player2->Pawns[2],
  's87' => $player2->Pawns[7],
  's18' => $player2->Rooks[0],
  's88' => $player2->Rooks[1],
  's28' => $player2->Knights[0],
  's78' => $player2->Knights[1],
  's38' => $player2->Bishops[0],
  's68' => $player2->Bishops[1],
  's48' => $player2->Queens[0],
  's58' => $player2->King,
) as $squareKey => $piece)
{
  $t->is($piece->getSquareKey(), $squareKey, 'Player '.$piece.' square key is '.$squareKey);
}

$helper->checkPieceSquareInteraction($game, $t);

$t->comment('Compile board');

$helper->checkPieceSquareInteraction($game, $t);

/*
 * Cleanup
 */
$game->delete();