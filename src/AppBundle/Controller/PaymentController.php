<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/payment")
 */
class PaymentController extends Controller
{


	/**
     * @Route("/index", name="indexpayment")
     * Template()
     */
    public function indexAction(Request $request)
    {
        // replace this example code with whatever you need
        return $this->render('payment/index.html.twig', array(
            "allo" => "allo",
        ));
    }


    /**
     * @Route("/create", name="addpayment")
     * Function that create a new payment
     */
    public function createAction(Request $request)
    {
       	if ($request->isMethod('POST')) {
       		# code...
       	}

    }
}
