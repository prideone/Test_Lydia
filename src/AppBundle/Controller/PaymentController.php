<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Form\Extension\Core\Type\TextType;

use AppBundle\Entity\Payment;

/**
 * @Route("/payment")
 */
class PaymentController extends Controller{


	/**
     * @Route("/index", name="indexpayment")
     * 
     */
    public function indexAction(Request $request){
        // replace this example code with whatever you need

    	$payment = new Payment();

        $form = $this->createFormBuilder($payment)
            ->add('firstName', TextType::class, array('required' => true, 'label' => 'First Name'))
            ->add('lastName', TextType::class, array('required' => true, 'label' => 'Last Name'))
            ->add('email', TextType::class, array('required' => true, 'label' => 'Email'))
            ->getForm();


        return $this->render('payment/index.html.twig', array(
            "form" => $form->createView(),
        ));
    }


    /**
     * @Route("/create", name="addpayment")
     * Function that create a new payment and stores it in the db
     */
    public function createAction(Request $request){

    	if (!$request->isXmlHttpRequest()) {
	        return new JsonResponse(array('message' => 'You can access this only using Ajax!'), 400);
	    }

    	$em = $this->getDoctrine()->getManager();

    	$payment = new Payment();

    	$form = $this->createFormBuilder($payment)
            ->add('firstName', TextType::class, array('required' => true, 'label' => 'First Name'))
            ->add('lastName', TextType::class, array('required' => true, 'label' => 'Last Name'))
            ->add('email', TextType::class, array('required' => true, 'label' => 'Email'))
            ->getForm();

       	if ($request->isMethod('POST')) {
       		$form->handleRequest($request);
       		if ($form->isValid()) {

				$payment = $this->get('api_lydia')->doRequest($payment);

				$em->persist($payment);
       			$em->flush();

       			return new JsonResponse(array('message' => 'Success!'), 200);
       		}else{
       			$errorString = var_export($this->getErrorMessages($form), true);
       			return new JsonResponse(array('message' => 'Error in the form submited!'.$errorString), 400);
       		}
       	}else{
       		return new JsonResponse(array('message' => 'Form not submited with POST method!'), 400);
       	}
    }



}
