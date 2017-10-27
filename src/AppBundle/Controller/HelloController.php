<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

class HelloController extends Controller
{   
    /**
     * @Route("/hello/{name}", name="hello")
     */
    public function indexAction(Request $request, $name = "anon") 
    {   // returns SessionInterface
        $session = $request->getSession();
        // get attribute in session, use default value if the attribute doesn't exist
        $pizza_num = $session->get('pizza_num', 0);

        return $this->render(
            'hello/index.html.twig', 
            array('name' => $name, 'pizza_num' => $pizza_num)
        );
    }

    /**
     * @Route("/somewhere")
     */
    public function somewhereAction()
    {   

        /** Long version **/
        // $this->get('session')->getFlashBag()->add(
        //     'notice', 
        //     'Oh, I see you came here for pizza.'
        // );

        /** Short version **/
        $this->addFlash(
            'notice',
            'Oh, I see you came here for pizza.'
        );

        /** Long version **/
        // return $this->redirect($this->generateUrl('hello'));

        /** Short version **/
        return $this->redirectToRoute('hello');
    }

    /**
     * @Route("/pizza")
     */
    public function pizzaAction(Request $request, $delivered_pizza_num)
    {       
        $number = $request->query->get('number');

        if (isset($number)) {
            // returns SessionInterface
            $session = $request->getSession();
            // Store attribute in session    
            $session->set('pizza_num', $number);

            return new Response("<html><body>Get $number pizza(s)</body></html>");
        }

        if (isset($delivered_pizza_num)) {
            return new Response("<html><body>You received $delivered_pizza_num pizza(s)</body></html>");
        }

        return new Response("<html><body>No Pizza for you mate :( </body></html>");
    }
}
