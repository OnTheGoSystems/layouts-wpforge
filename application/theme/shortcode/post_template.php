<?php

if( class_exists('WPDDL_Integration_Theme_Shortcode_Post_Template') ) return;

class WPDDL_Integration_Theme_Shortcode_Post_Template
    extends WPDDL_Shortcode_Abstract {

    public function setup() {
        $this->setId( 'cornerstone-post-template' );
        $this->setTemplate( dirname( __FILE__ ) . '/view/post_template.php' );

        $this->setMediaButton( 'Post Template' );

        $option = new WPDDL_Shortcode_Option_Default();
        $option->setLabel( 'Post Template' );
        $option->setName( 'Post Template' );

        $attribute = new WPDDL_Shortcode_Option_Attribute_Default();
        $attribute->setId( 'display-options' );
        $attribute->setLabel( 'Display Options' );
        $attribute->setHeader( 'Display Options' );

        $field = new WPDDL_Shortcode_Option_Attribute_Field_Default();
        $field->setId( 'output' );
        $field->setLabel( 'How to implement the template?' );
        $field->setDescription( '' );
        $field->setType( 'radio' );
        $field->addOption( new WPDDL_Shortcode_Option_Attribute_Field_Option_Default(
            $this->getId(),
            $field->getId(),
            'default',
            'Genesis Output with all Hooks',
            true
        ) );


        $field->addOption( new WPDDL_Shortcode_Option_Attribute_Field_Option_Default(
            $this->getId(),
            $field->getId(),
            'editable',
            'Genesis Output - editable in Editor',
            false,
            dirname( __FILE__ ) . '/view/post_template_editable.php'
        ) );

        $attribute->addField( $field );
        $option->addAttribute( $attribute );

        $this->setOption( $option );

        add_filter('ddl-do-not-apply-overlay-for-post-editor', array(&$this, 'add_cornerstone_shortcode') );

        parent::setup();
    }

    function add_cornerstone_shortcode($codes){
        $codes[] = 'cornerstone-post-template';
        return $codes;
    }
}