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
     * @variable Cache type ids
     */
    protected $_types = array('config','layout','block_html','collections','reflection','db_ddl','eav','config_integration','config_integration_api','full_page','translate','config_webservice');
    /**
     * Mass action for cache refresh
     *
     * @return json response data
     */
            
    public function execute()
    {
        try {
            $response = array();
            $types = $this->_types;
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

        echo json_encode($response);
    }
}
