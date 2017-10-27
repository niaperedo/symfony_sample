<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class DeliverController extends Controller
{   
    /**
     * @Route("/deliver/pizza")
     */
    public function deliverPizzaAction(Request $request) 
    {
        $number = $request->query->get('number');

        $response = $this->forward(
            'AppBundle:Hello:pizza',
            array(
                'delivered_pizza_num' => $number
            )
        );


        return $response;
    }
}
