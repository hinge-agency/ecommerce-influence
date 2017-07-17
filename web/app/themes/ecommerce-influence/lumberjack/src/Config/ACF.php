<?php

namespace Lumberjack\Config;

class ACF
{
    public static function register()
    {

        if( function_exists('acf_add_local_field_group') ):

			acf_add_local_field_group(array (
				'key' => 'group_593ead5fbe750',
				'title' => 'Page Blocks',
				'fields' => array (
					array (
						'key' => 'field_593ead6deec58',
						'label' => 'Blocks',
						'name' => 'blocks',
						'type' => 'flexible_content',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array (
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'layouts' => array (
							'593ead7413e00' => array (
								'key' => '593ead7413e00',
								'name' => 'custom_block',
								'label' => 'Custom Block',
								'display' => 'block',
								'sub_fields' => array (
								),
								'min' => '',
								'max' => '',
							),
						),
						'button_label' => 'Add Block',
						'min' => '',
						'max' => '',
					),
				),
				'location' => array (
					array (
						array (
							'param' => 'post_type',
							'operator' => '==',
							'value' => 'page',
						),
					),
				),
				'menu_order' => 0,
				'position' => 'normal',
				'style' => 'default',
				'label_placement' => 'top',
				'instruction_placement' => 'label',
				'hide_on_screen' => '',
				'active' => 1,
				'description' => '',
			));
		endif;
    }
}
