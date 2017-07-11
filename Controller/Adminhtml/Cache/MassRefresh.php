<?php
/**
 * Copyright © 2016 iFlair Web Technologies. All rights reserved.
 */
 
namespace IFlair\EasyCacheCleaner\Controller\Adminhtml\Cache;

use \Magento\Framework\Exception\LocalizedException;
use \Magento\Framework\Controller\ResultFactory;

class MassRefresh extends \Magento\Backend\Controller\Adminhtml\Cache
{
    /**
     * @var JsonFactory
     */
    private $jsonFactory;

   /**
    * @variable Cache type ids
    */
    private $types = ['config',
    'layout',
    'block_html',
    'collections',
    'reflection',
    'db_ddl',
    'eav',
    'config_integration',
    'config_integration_api',
    'full_page',
    'translate',
    'config_webservice'];

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param \Magento\Framework\App\Cache\StateInterface $cacheState
     * @param \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList,
        \Magento\Framework\App\Cache\StateInterface $cacheState,
        \Magento\Framework\App\Cache\Frontend\Pool $cacheFrontendPool,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
    ) {
        parent::__construct($context, $cacheTypeList, $cacheState, $cacheFrontendPool, $resultPageFactory);
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Mass action for cache refresh
     *
     * @return json response data
     */
            
    public function execute()
    {
        try {
            /** @var \Magento\Framework\Controller\Result\Json $resultJson */
            $resultJson = $this->jsonFactory->create();

            $response = [];
            $types = $this->types;
            $updatedTypes = 0;
            if (!is_array($types)) {
                $types = [];
            }
            $this->_validateTypes($types);
            foreach ($types as $type) {
                $this->_cacheTypeList->cleanType($type);
                $updatedTypes++;
            }
            if ($updatedTypes > 0) {
                $response['type'] = 'success';
                $response['success'] = true;
                $response['message'] = __("%1 cache type(s) refreshed.", $updatedTypes);
            }
        } catch (LocalizedException $e) {
            $response['type'] = 'error';
            $response['error'] = true;
            $response['message'] = $e->getMessage();
        } catch (\Exception $e) {
            $response['type'] = 'error';
            $response['error'] = true;
            $response['message'] = __('An error occurred while refreshing cache.');
        }

        return $resultJson->setData($response);
    }
}
