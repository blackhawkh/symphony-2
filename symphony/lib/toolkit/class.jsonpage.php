<?php
	/**
	 * @package toolkit
	 */
	/**
	 * JSONPage extends the Page class to provide an object representation
	 * of a Symphony backend JSON page.
	 */

	require_once(TOOLKIT . '/class.page.php');

	Abstract Class JSONPage extends Page {

		/**
		 * The root node for the response of the JSONPage
		 * @var array
		 */
		protected $_Result;

		/**
		 * The constructor for `JSONPage`. This sets the page status to `Page::HTTP_STATUS_OK`,
		 * the default content type to `application/json` and initialises `$this->_Result`
		 * with an `array`. The constructor also starts the Profiler for this
		 * page template.
		 *
		 * @see toolkit.Profiler
		 */
		public function __construct() {
			$this->_Result = array();
			
			$this->setHttpStatus(self::HTTP_STATUS_OK);
			$this->addHeaderToPage('Content-Type', 'application/json');
			
			Symphony::Profiler()->sample('Page template created', PROFILE_LAP);
		}

		/**
		 * This function is called by Administration class when a user is not authenticated
		 * to the Symphony backend. It sets the status of this page to
		 * `Page::HTTP_STATUS_UNAUTHORIZED` and appends a message for generation
		 */
		public function handleFailedAuthorisation(){
			$this->setHttpStatus(self::HTTP_STATUS_UNAUTHORIZED);
			$this->_Result = array('status' => __('You are not authorised to access this page.'));
		}

		/**
		 * Calls the view function of this page. If a context is passed, it is
		 * also set.
		 *
		 * @see view()
		 * @param array $context
		 *  The context of the page as an array. Defaults to null
		 */
		public function build($context = null){
			if($context) $this->_context = $context;
			$this->view();
		}

		/**
		 * The generate functions outputs the correct headers for
		 * this `JSONPage`, adds `$this->getHttpStatusCode()` code to the root attribute
		 * before calling the parent generate function and generating
		 * the `$this->_Result` json string
		 *
		 * @return string
		 */
		public function generate($page = null) {
			// Set the actual status code in the xml response
			$this->_Result['status'] = $this->getHttpStatusCode();

			parent::generate($page);

			return json_encode($this->_Result);
		}

		/**
		 * All classes that extend the `JSONPage` class must define a view method
		 * which contains the logic for the content of this page. The resulting values
		 * must be appended to `$this->_Result` where it is generated as json on build
		 *
		 * @see build()
		 */
		abstract public function view();

	}
