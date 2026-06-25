<?php defined('BASEPATH') OR exit('No direct script access allowed');

    

class Stripe extends CI_Controller {

    private string $stripe_api_key;

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function __construct() {

       parent::__construct();

       $this->load->library("session");

       $this->load->helper('url');

       $this->load->config('stripe');
       $this->stripe_api_key = config_item('stripe_api_key');

    }

     

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function index()

    {

        $this->load->view('paypal_test/stripe');

    }

        

    /**

     * Get All Data from this method.

     *

     * @return Response

    */

    public function payment()

    {

      require_once('application/libraries/stripe/init.php');

     

      // $stripeSecret = 'STRIPE_SECRET_KEY';

      \Stripe\Stripe::setApiKey($this->stripe_api_key);

      

        $stripe = \Stripe\Charge::create ([

                "amount" => $this->input->post('amount'),

                "currency" => "usd",

                "source" => $this->input->post('tokenId'),

                "description" => "This is from nicesnippets.com"

        ]);

             

       // after successfull payment, you can store payment related information into your database

              

        $data = array('success' => true, 'data'=> $stripe);

 

        echo json_encode($data);

    }

}