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

       			$em->persist($payment);
       			$em->flush();

       			$apiUrl = "https://homologation.lydia-app.com";
       			$initPaymentUrl = "/api/request/do.json";

       			$data = array(
				    'user_token' => '+33684761046',
				    'amount' => "12.40",
				    'currency' => "EUR",
				    'type' => "email",
				    'recipient' => "stephane.bolu@gmail.com",
				    'message' => 'Anniv bob',
       			);


       			try{
       				$ch = curl_init();

       				if ($ch === false) {
				        throw new Exception('failed to initialize');
				    }

					curl_setopt($ch, CURLOPT_URL, $apiUrl.$initPaymentUrl);
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
					curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json"));
					curl_setopt($ch, CURLOPT_POST, 1);
					curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
					curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
					curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
					$result = curl_exec($ch);

					if ($result === false) {
				        throw new \Exception(curl_error($ch), curl_errno($ch));
				    }


       			} catch(Exception $e) {
				    trigger_error(sprintf('Curl failed with error #%d: %s',$e->getCode(), $e->getMessage()),E_USER_ERROR);
				}
       			

       			var_dump($result);
       			die();


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
