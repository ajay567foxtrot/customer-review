<?php
class ControllerCatalogTestimonials extends Controller {
	private $error = array();
  
	public function index() {
		$this->load->language('catalog/customer_review');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/customerreview');	
		$this->load->model('setting/setting');	

		$this->getList();
	}

	