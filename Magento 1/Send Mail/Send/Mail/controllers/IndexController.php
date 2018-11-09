<?php
class Send_Mail_IndexController extends Mage_Core_Controller_Front_Action
{
	public function indexAction()
	{
		$url_product = $_POST['url'];
		//get data from phtml file 	
		$name = $this->getRequest()->getPost('name');
		$email = $this->getRequest()->getPost('email');
		$phone = $this->getRequest()->getPost('telephone');
		$brand = $this->getRequest()->getPost('brand');
		$mess = $this->getRequest()->getPost('message');
		$name_product = $_POST['name_product'];
		
		//get email in admin config
		$emailto = Mage::getStoreConfig('trans_email/ident_general/email');		

		//Send email
		$headers = "From: " . $email . "\r\n";
		$headers .= "Reply-To: ". $email . "\r\n";
		$headers .= "CC: test@gmail.com";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$to_admin      = $emailto;
		$to_customer      = $email;
		$subject = 'Request Quote';
		$message_admin = nl2br("Hi, \n A customer have requested a quote for product : <a href='$url_product' >".$name_product.
			"</a> \n Information :\nName : ".$name.
			"\nTelephone : ".$phone.
			"\nBrand : ".$brand.
			"\nMessage : ".$mess);
		$message_customer = nl2br("Hi ".$name.", \nYou have request quote for product : <a href='$url_product' >".$name_product.
			"</a> \n Information :\nName : ".$name.
			"\nTelephone : ".$phone.
			"\nBrand : ".$brand.
			"\nMessage : ".$mess.
			"\n\nWe will reply soon.\nThank you!"
		);

		mail($to_admin, $subject, $message_admin, $headers);
		mail($to_customer, $subject, $message_customer, $headers);

		//get url product to redirect
		
		$this->_redirectUrl($_POST['url']);
		Mage::getSingleton('core/session')->addSuccess('Request Quote Product Success');
	}
	
}  