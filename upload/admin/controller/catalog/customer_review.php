<?php
class ControllerCatalogTestimonials extends Controller {
	private $error = array();
  
	public function index() {
		$this->load->language('catalog/customerreview');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/customerreview');	
		$this->load->model('setting/setting');	

		$this->getList();
	}
	protected function getList() {
	$this->load->model('catalog/customerreview');	
		if (isset($this->request->get['sort'])) {
			$sort = $this->request->get['sort'];
		} else {
			$sort = 'id.title';
		}

		if (isset($this->request->get['order'])) {
			$order = $this->request->get['order'];
		} else {
			$order = 'ASC';
		}

		if (isset($this->request->get['page'])) {
			$page = $this->request->get['page'];
		} else {
			$page = 1;
		}

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['breadcrumbs'] = array();

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('text_home'),
			'href' => $this->url->link('common/dashboard', 'user_token=' . $this->session->data['user_token'], true)
		);

		$data['breadcrumbs'][] = array(
			'text' => $this->language->get('heading_title'),
			'href' => $this->url->link('catalog/customerreview', 'user_token=' . $this->session->data['user_token'] . $url, true)
		);

		$data['add'] = $this->url->link('catalog/customerreview/add', 'user_token=' . $this->session->data['user_token'] . $url, true);
		$data['delete'] = $this->url->link('catalog/customerreview/delete', 'user_token=' . $this->session->data['user_token'] . $url, true);

		$data['customerreview'] = array();

		$filter_data = array(
			'sort'  => $sort,
			'order' => $order,
			'start' => ($page - 1) * $this->config->get('config_limit_admin'),
			'limit' => $this->config->get('config_limit_admin')
		);
          		 
          $customerreviews_total = $this->model_catalog_customerreviews->getTotalCustomerreviews();
          //echo $testimonials_total;
	


		$results = $this->model_catalog_customerreviews->getCustomerreviews($filter_data);
         	//die('aa');
		foreach ($results as $result) {
			$data['customerreviews'][] = array(
				'customerreview_id' => $result['customerreview_id'],
				'title'          => $result['author'],
				'sort_order'     => $result['sort_order'],
				'edit'           => $this->url->link('catalog/customerreviews/edit', 'user_token=' . $this->session->data['user_token'] . '&customerreview_id=' . $result['customerreview_id'] . $url, true)
			);
			 
		}
     

		if (isset($this->error['warning'])) {
			$data['error_warning'] = $this->error['warning'];
		} else {
			$data['error_warning'] = '';
		}

		if (isset($this->session->data['success'])) {
			$data['success'] = $this->session->data['success'];

			unset($this->session->data['success']);
		} else {
			$data['success'] = '';
		}

		if (isset($this->request->post['selected'])) {
			$data['selected'] = (array)$this->request->post['selected'];
		} else {
			$data['selected'] = array();
		}

		$url = '';

		if ($order == 'ASC') {
			$url .= '&order=DESC';
		} else {
			$url .= '&order=ASC';
		}

		if (isset($this->request->get['page'])) {
			$url .= '&page=' . $this->request->get['page'];
		}

		$data['sort_title'] = $this->url->link('catalog/customerreview', 'user_token=' . $this->session->data['user_token'] . '&sort=id.title' . $url, true);
		
		$data['sort_sort_order'] = $this->url->link('catalog/customerreview', 'user_token=' . $this->session->data['user_token'] . '&sort=i.sort_order' . $url, true);

		$url = '';

		if (isset($this->request->get['sort'])) {
			$url .= '&sort=' . $this->request->get['sort'];
		}

		if (isset($this->request->get['order'])) {
			$url .= '&order=' . $this->request->get['order'];
		}

		$pagination = new Pagination();
		$pagination->total = $customerreviews_total;
		$pagination->page = $page;
		$pagination->limit = $this->config->get('config_limit_admin');
		$pagination->url = $this->url->link('catalog/customerreview', 'user_token=' . $this->session->data['user_token'] . $url . '&page={page}', true);

		$data['pagination'] = $pagination->render();

		$data['results'] = sprintf($this->language->get('text_pagination'), ($customerreviews_total) ? (($page - 1) * $this->config->get('config_limit_admin')) + 1 : 0, ((($page - 1) * $this->config->get('config_limit_admin')) > ($customerreviews_total - $this->config->get('config_limit_admin'))) ? $customerreviews_total : ((($page - 1) * $this->config->get('config_limit_admin')) + $this->config->get('config_limit_admin')), $customerreviews_total, ceil($customerreviews_total / $this->config->get('config_limit_admin')));

		$data['sort'] = $sort;
		$data['order'] = $order;

		$data['header'] = $this->load->controller('common/header');
		$data['column_left'] = $this->load->controller('common/column_left');
		$data['footer'] = $this->load->controller('common/footer');

		$this->response->setOutput($this->load->view('catalog/customerreviews_list', $data));
	}
	public function edit() {
		
		$this->load->language('catalog/customerreview');

		$this->document->setTitle($this->language->get('heading_title'));

		$this->load->model('catalog/customerreview');
		$this->load->model('setting/setting');
		//echo '<pre>';print_r($this->request->post);die;

		if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validateForm()) {

			$this->model_catalog_testimonials->editcustomerreview($this->request->get['customerreview_id'], $this->request->post);

			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/customerreview', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}

		$this->getForm();
	}

	public function add() {
	
		$this->load->language('catalog/customerreview');

		$this->document->setTitle($this->language->get('heading_title'));
	
		$this->load->model('catalog/customerreview');
			//echo'<pre>';print_r($this->request->post); die;

		if (($this->request->server['REQUEST_METHOD'] == 'POST')&& $this->validateForm()) {
		
	
			$this->model_catalog_customerreview->addcustomerreview($this->request->post);
//die('c');
			$this->session->data['success'] = $this->language->get('text_success');

			$url = '';

			if (isset($this->request->get['sort'])) {
				$url .= '&sort=' . $this->request->get['sort'];
			}

			if (isset($this->request->get['order'])) {
				$url .= '&order=' . $this->request->get['order'];
			}

			if (isset($this->request->get['page'])) {
				$url .= '&page=' . $this->request->get['page'];
			}

			$this->response->redirect($this->url->link('catalog/customerreview', 'user_token=' . $this->session->data['user_token'] . $url, true));
		}
        
		$this->getForm();
	}


	




	