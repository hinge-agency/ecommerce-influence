<?php

namespace Lumberjack\Functions;
use Lumberjack\PostTypes\Post;

class Blocks
{

    /**
     * The ACF field key were we want to get our layouts from
     * @var string
     */
    protected $acfFieldKey = 'block';

    /**
     * layouts contains all of our acf layouts returned from the acf_field_key
     * @var array
     */
    public  $layouts;

    /**
     * Constructor that gets the layouts from the acf field and constructs our layouts
     * with all of the required data for the view
     */
    public function __construct()
    {
        $this->layouts = get_field($this->acfFieldKey);
        $this->constructLayouts();
    }

    /**
     * Loop over all of our layouts and add / fetch data to be used as required
     * @return array | A parsed/build version of the acf_fc_layout. 
     */
    public function constructLayouts()
    {
        // Check that there is some layouts to iterate over
        if ($this->layouts){
            // Iterate over our layouts
            foreach ($this->layouts as $key => &$block){
                if (is_array($block)){
                    // Set a new key called layout to tidy our array up
                    $block['layout'] = $block['acf_fc_layout'] ;
                    // Unset the default acf_fc_layout key
                    unset($block['acf_fc_layout']);
                    // Switch through all of the layouts and build them out as required
                    switch ($block['layout']) {
                        case 'custom_block':
                                $this->constructBlock($block);
                            break;
                    }
                }
            }
        }
        return $this->layouts;
    }

    /**
     * Manipulates the acf_layout fields to something structured that can be used 
     * in twig
     * @param  array &$block 
     * @return array
     */

    private function constructBlock(&$block)
    {

        
    }

    
}


