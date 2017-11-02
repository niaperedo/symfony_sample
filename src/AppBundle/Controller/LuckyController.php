<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class LuckyController extends Controller
{
    /**
     * @Route("/lucky/number", name="lucky_number")
     */
    public function apiNumberAction()
    {
        $number = rand(0, 100);

        /** Long version **/
        // return new Response(
        //     json_encode($number),
        //     200,
        //     array('Content-Type' => 'application/json')
        // );
        

        /** Short version **/
        return new JsonResponse($number);
    }

    /**
     * @Route("/lucky/numbers/{count}")
     */
    public function numberAction($count = 1) 
    {
        $numbers = array();

        for ($i = 0; $i < $count; $i++) {
            $numbers[] = rand(0, 100);
        }

        $numberList = implode(', ', $numbers);

        /** Long version */
        // $html = $this->container->get('templating')->render(
        //     'lucky/number.html.twig',
        //     array('numberList' => $numberList)
        // );

        // return new Response($html);
        
        /** Short version **/
        return $this->render(
            'lucky/number.html.twig',
            array('numberList' => $numberList)
        );
        
    }
}
