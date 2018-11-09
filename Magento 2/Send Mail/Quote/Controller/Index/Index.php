<?php
namespace Smartwave\Quote\Controller\Index;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{

	protected $_pageFactory;
	protected $request;
	protected $scopeConfig;

	public function __construct(
		\Magento\Framework\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $pageFactory,
		\Magento\Framework\App\Request\Http $request,
		ScopeConfigInterface $scopeConfig
		)
	{
		$this->scopeConfig = $scopeConfig;
		$this->request = $request;

		$this->_pageFactory = $pageFactory;
		return parent::__construct($context);
	}
	public function execute()
	{
		$emailto = $this->scopeConfig->getValue('trans_email/ident_support/email',ScopeInterface::SCOPE_STORE);

		$url_product = $_POST['url'];
		//get data from phtml file
		$name = $this->getRequest()->getPost('name');
		$email = $this->getRequest()->getPost('email');
		$company = $this->getRequest()->getPost('company');
		$mess = $this->getRequest()->getPost('message');

		$name_product = $_POST['name_product'];

		//get email in admin config
		//$emailto = Mage::getStoreConfig('trans_email/ident_general/email');

		//Send email
		$headers = "From: " . $email . "\r\n";
		$headers .= "Reply-To: ". $email . "\r\n";
		$headers .= "CC: sales@farrwestenv.com";
		$headers .= "MIME-Version: 1.0\r\n";
		$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

		$to_admin      = $emailto;  
		$to_customer      = $email;

		$subject = 'Request Quote';
		$message_admin = nl2br("Hi, \n A customer have requested a quote for product : <a href='$url_product' >".$name_product.
			"</a> \n Information :\nName : ".$name.
			"\nCompany or Organization : ".$company.
			"\nMessage : ".$mess);
		$message_customer = nl2br("Hi ".$name.", \nYou have request quote for product : <a href='$url_product' >".$name_product.
			"</a> \n Information :\nName : ".$name.
			"\nCompany or Organization : ".$company.
			"\nMessage : ".$mess.
			"\n\nWe will reply soon.\nThank you!"
		);

		mail($to_admin, $subject, $message_admin, $headers);
		mail($to_customer, $subject, $message_customer, $headers);

		//get url product to redirect

		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		$resultRedirect->setPath($url_product);
		

		$this->messageManager->addSuccessMessage('Request Quote Product Success');
		return $resultRedirect;

	}
}
