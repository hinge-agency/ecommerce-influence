<?php

namespace LeadpagesWP\ServiceProviders;

use Leadpages\Auth\LeadpagesLogin;

class SplitTestApi
{

    public $apiTestsEndpoint;
    /**
     * @var LeadpagesLogin
     */
    private $login;

    public function __construct(LeadpagesLogin $login)
    {
        $this->apiTestsEndpoint = "https://api.center.io/splittest/v2/tests?expand=true";
        $this->login = $login;
    }

    public function getUsersTests()
    {
        $response = wp_remote_get(
            $this->apiTestsEndpoint,
            [
                'headers'     => [
                    'LP-Security-Token' => $this->login->token
                ],
            ]
        );

        return $response['body'];
    }

    public function getTestsObject()
    {
        return json_decode($this->getUsersTests())->_items;
    }

    public function getActiveSplitTests()
    {
        //return an array because thats what the pages loop is expecting
        $returnArray = [];

        /**
         * needs to be in format
         *$array['id' => 'id', 'name' => 'pagename', ['_meta']['xor_hex_id']
         **/

        /**
         * pages loop is expecint an xor_id to be set for builder 2 pages,
         *no relavence except for need to be there
        **/

        $testObject = $this->getTestsObject();
        // echo '<pre>'; print_r($testObject);die();
        
        $i = 0;
        foreach($testObject as $test){
            if($test->status == 'active'){
                $returnArray[$i]['name'] = $test->name . ' (Split Test)';
                $returnArray[$i]['_meta'] = [
                    'xor_hex_id' => 0,
                    'updated' => $test->_meta->updated,
                    'url' => $test->_meta->uri,
                    'variationsCount' => count($test->variations),
                    'id' => $test->_meta->id,
                ];

                foreach($test->variations as $variation){
                    if($variation->control == 'true'){
                        $returnArray[$i]['id'] = $variation->assetId;
                        $returnArray[$i]['_meta']['controlUrl'] = $variation->uri;
                    }
                }
            }
            $i++;
        }
        return $returnArray;
    }
}
