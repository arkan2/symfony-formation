<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/game")
 */
class GameController extends Controller
{
    /**
     * @Route("/", name="app_game")
     * @Route("/play", name="app_game_play")
     * @Method("GET")
     */
    public function playAction()
    {
        $game = $this->get('app.game_runner')->loadGame();

        return $this->render('game/play.html.twig', [
            'game' => $game,
        ]);
    }

    /**
     * @Route(
     *   path="/play/{letter}",
     *   name="app_game_play_letter",
     *   requirements={
     *     "letter"="[a-z]"
     *   }
     * )
     * @Method("GET")
     */
    public function playLetterAction($letter)
    {
        $game = $this->get('app.game_runner')->playLetter($letter);

        if (!$game->isOver()) {
            return $this->redirectToRoute('app_game');
        }

        return $this->redirectToRoute($game->isWon() ? 'app_game_win' : 'app_game_fail');
    }

    /**
     * @Route("/play", name="app_game_play_word")
     * @Method("POST")
     */
    public function playWordAction(Request $request)
    {
        $game = $this->get('app.game_runner')->playWord($request->request->get('word'));

        return $this->redirectToRoute($game->isWon() ? 'app_game_win' : 'app_game_fail');
    }

    /**
     * @Route("/reset", name="app_game_reset")
     * @Method("GET")
     */
    public function resetAction()
    {
        $this->get('app.game_runner')->resetGame();

        return $this->redirectToRoute('app_game');
    }

    /**
     * @Route("/win", name="app_game_win")
     * @Method("GET")
     */
    public function winAction()
    {
        $game = $this->get('app.game_runner')->resetGameOnSuccess();

        return $this->render('game/win.html.twig', [ 'game' => $game ]);
    }

    /**
     * @Route("/fail", name="app_game_fail")
     * @Method("GET")
     */
    public function failAction()
    {
        $game = $this->get('app.game_runner')->resetGameOnFailure();

        return $this->render('game/fail.html.twig', [ 'game' => $game ]);
    }
}
