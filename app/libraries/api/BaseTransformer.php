<?php

defined('BASEPATH') OR exit('No direct script access allowed');

abstract class BaseTransformer
{
	/**
	 * Definition
	 *
	 * @var array
	 */
	protected $resource = null;

	/**
	 * Transform items
	 *
	 * @var array
	 */
	protected $transformItems = array();

	/**
	 * Total items in full collection
	 *
	 * @var int
	 */
	protected $totalItems = 0;

	/**
	 * @var CI Instance placeholder
	 */
	public $ci;

	/**
	 * @var GET Request placeholder
	 */
	private $request = null;

	public function __construct()
	{
		$this->ci=& get_instance(); // set the CI instance
	}

	/**
	 * Transform request collection to get the transformation
	 *
	 * @param array $resource
	 * @return array $response
	 */
	public function smartTransformCollection(array $resource)
	{
		$this->resource = $resource;
		$functions = $this->getRequestFunctions();
		$response = array();

		foreach ($functions as $function) {
			if (is_string($function) && method_exists($this, $function)) {
				//a response that should contain an array with headers (optional) and data (kinda mandatory?)
				return $this->$function();
			}

			if (is_array($function)) {
				foreach ($function as $subFunction) {
					if (!method_exists($this, $subFunction)) {
						continue;
					}
					//a response that should contain an array with headers (optional) and data (kinda mandatory?)
					return $this->$subFunction();
				}
			}
		}

		$response['data'] = $this->defaultTransformCollection($resource);
		$response['totalItems'] = $this->totalItems;
		return $response;
	}

	/**
	 * Transform items in a smart way
	 * This is something I thought of and added to enable methods within transformers to be called
	 * leaving controllers to be used by frontend alone and api running via transformers.
	 *
	 * @param array $resource
	 * @param int $id
	 * @return mixed
	 */
	public function smartTransform($resource, $id)
	{
		$functions = $this->getRequestFunctions();

		foreach ($functions as $function) {
			if (is_string($function) && method_exists($this, $function)) {
				//a response that should contain an array with data (kinda mandatory?)
				return $this->$function($id, $this->request);
			}

			if (is_array($function)) {
				foreach ($function as $subFunction) {
					if (!method_exists($this, $subFunction)) {
						continue;
					}
					//a response that should contain an array with data (kinda mandatory?)
					return $this->$subFunction($id);
				}
			}
		}

		$response['data'] = $this->defaultTransformItem($resource, $id);
		return $response;
	}

	/**
	 * Used to send default response if method not found in transformer
	 *
	 * @param $resource
	 * @return array
	 */
	public function defaultTransformCollection($resource)
	{
		$this->ci->load->model($resource['model'], 'model');

		$perPage = ($this->ci->input->get('per-page')) ? (int) $this->ci->input->get('per-page') : 10;
		$page = ($this->ci->input->get('page')) ? (int) $this->ci->input->get('page') : 0;

		$filteredAndPaginatedData = $this->ci->model->getItems($page, $perPage); // All get items are to be paginated

		$items = $filteredAndPaginatedData['items'];
		$this->totalItems = $filteredAndPaginatedData['totalItems'];

		$transformedItems = array();
		foreach ($items as $item) {
			$transformedItems[] = $this->transform($item);
		}

		return $transformedItems;
	}

	/**
	 * Used to send default response if method not found in transformer on item
	 *
	 * @param $resource
	 * @param $id
	 * @return array
	 */
	public function defaultTransformItem($resource, $id)
	{
		$this->ci->load->model($resource['model'], 'model');

		$item = $this->ci->model->getItem($id);

		return $this->transform($item);
	}

	/**
	 * Transform object
	 *
	 * @param array $item Item
	 * @return array
	 */
	public function transform($item)
	{
		// Build response related to the object parameters
		$transform = array();
		foreach ($this->transformItems as $key => $value) {
			if (is_string($value) && array_key_exists($value, $item)) {
				$transform[$value] = $item[$value];
				continue;
			}
			$transform[$key] = $value;
		}
		return $transform;
	}

	/**
	 * Transform collection to array
	 *
	 * @param array Collection $items Items
	 * @return array
	 */
	public function transformCollection(array $items)
	{
		return array_map(array($this, 'transform'), $items);
	}

	/**
	 * Create item based on the resource given
	 *
	 * @param array $resource
	 * @return null
	 */
	public function createItem($resource)
	{
		$this->ci->load->model($resource['model'], 'model');

		$postData = $this->ci->input->post();

		$this->ci->model->createItem($postData);

		return 'Resource created successfully';
	}

	/**
	 * Update item based on the resource given
	 *
	 * @param array $resource
	 * @param int $id
	 * @return string
	 */
	public function updateItem($resource, $id)
	{
		$this->ci->load->model($resource['model'], 'model');

		$postData = $this->ci->input->post(null, true);

		//@todo: Get the fillable fields and run a filter on what was given
		$this->ci->model->updateItem($id, $postData);

		return 'Resource updated successfully';
	}

	/**
	 * Delete item based on the resource given
	 *
	 * @param array $resource
	 * @param int $id
	 * @return string
	 */
	public function deleteItem($resource, $id)
	{
		// Here would be a good place to add some sort of permission if users are split by permissions and roles
		$this->ci->load->model($resource['model'], 'model');

		$this->ci->model->deleteItem($id);

		return 'Resource deleted successfully';
	}

	/**
	 * Get request functions from parameters
	 *
	 * @return array
	 */
	protected function getRequestFunctions()
	{
		$functions = array('full' => '');
		foreach ($this->ci->input->get() as $key => $value) {
			$functions['full'] .= $this->formatRequestParam($key) . $this->formatRequestParam($value);
			if (!isset($functions[$key])) {
				$functions[$key] = array();
			}
			$functions[$key][] = lcfirst($this->formatRequestParam($key) . $this->formatRequestParam($value));
			$functions[$key][] = lcfirst($this->formatRequestParam($key));
		}
		$functions['full'] = lcfirst($functions['full']);
		return $functions;
	}

	/**
	 * Format request params to build a specific function of current transformer
	 *
	 * @param $param
	 * @return mixed|string
	 */
	protected function formatRequestParam($param)
	{
		$param = preg_replace('/[-_]/', ' ', $param);   // hello-world => hello world
		$param = ucwords($param);                       // hello world => Hello World
		$param = str_replace(' ', '', $param);          // Hello World => HelloWorld
		return $param;
	}
}
