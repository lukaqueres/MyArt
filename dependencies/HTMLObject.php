<?php

namespace Dependencies;

use ErrorException;

/**
 * File storing all functions/classes used with database connection, or for processing results
 *
 * All functions/classes with contact with database or results should be placed in here
 *
 * Links as reference:
 * @link https://developer.wordpress.org/coding-standards/inline-documentation-standards/php/
 * 
 * @package HTMLObject
 * @subpackage HTMLObject | CSS
 * @since 8.0.0
 * @version 1:1.0.0
 * @author Łukasz <lkocieln@gmail.com>
 */

      /**
     * HTML Objects class
     *
     * Creates and manages HTML DOM Elements with CSS coverage
     * Available and working __toString() method, returns HTML ready element
     * Available PUBLIC functions:
     *  - @see tag ( Changes / Returns tag of an object );
     *  - @see text ( Changes / Returns inner text of an object );
     *  - @see innerHtml ( Changes / Returns innerHTML of an object )
     *  - @see outerHtml ( Mostly returns outerHTML of an object, can assign value, not used yet )
     *  - @see noSpecials ( Returns outerHTML of an object, without HTML special caracters)
     *  - @see childs ( Assigns / Returns childs of an element )
     *  - @see append ( Appends given element to an object )
     *  - @see remove ( Removes object from element childs )
     * 
    */
    class HTMLObject {
        /**
         * @var string $tag Tag of an element
         * @var string $text Inner text of an element
         * @var string $innerHTML Inner HTML of an object 
         * @var string $outerHTML Outer HTML of an object
         * @var array $childs List of childs of an element
         * @var array $attributes List of attributes and their values of an element
         * @var Css $css Css rules for an element 
         * @var Attributes $attributes Attributes object storing and providing support about attributes of an element
        */
        protected string $tag;
        protected string $text;
        protected string $innerHTML;
        protected string $outerHTML;
        protected array $childs;
        public Css $css;
        public Attributes $attributes;

        /**
         * @param string $tag Tag of new element
         * @param string $text Text node in element
         * @param string $html Html code to put inside of element 
         * @param array $classList List o f classes for element
         * 
         * @return HTMLObject returns instance of HTMLObject object
         */
        function __construct($tag, $text = null, $html = null, array $classList = Null) {
            $this->childs = array();
            $this->attributes = new Attributes();

            $this->tag = "";
            $this->text = "";
            $this->innerHTML = "";

            $this->tag($tag);
            $this->text($text);
            $this->innerHTML($html);

            $this->css = new Css(classList: $classList);
        }

        /**
         * String representation of element, ready for HTML
         * 
         * @return string HTML Code of element
         */
        function __toString(): string {
            $innerHTML = $this->text;
            if ( count($this->childs) > 0 ) {
                foreach ( $this->childs as $child ) {
                    $innerHTML .= $child;
                }
            }
            return sprintf("{$this->openTag()} %s </{$this->tag}>", $innerHTML);
        }

        /**
         * Creates open tag for element
         * 
         * @return string $tag Opening tag for element, with all attributes and css
         */
        protected function openTag(): string {
            $attributes = "";
            foreach($this->attributes->all() as $attribute => $value) {
                $attributes .= " {$attribute} = \"{$value}\"";
            }
            if ( count($this->css->styles()) <> 0) {
                $attributes .= " style=\"";
                foreach( $this->css->styles() as $name => $value ) {
                    $attributes .= " {$name}: {$value};";
                }
                $attributes .= "\"";
            }
            if ( count($this->css->classList()) <> 0) {
                $attributes .= " class=\"";
                foreach( $this->css->classList() as $class ) {
                    $attributes .= " {$class}";
                }
                $attributes .= " \"";
            }
            $tag = "<{$this->tag()} {$attributes}>";
            return $tag;
        }

        /**
         * Regenerates inner and outer HTML of element
         * 
         * @return void
         */
        protected function rebuild(): void {
            $this->innerHtml();
            $this->outerHtml();
        }
        /**
         * Generates string without HTML special caracters
         * 
         * @return string HTML representation without HTML special caracters
         */
        public function noSpecials(): string {
            $outerHTML = $this->outerHtml();
            return htmlspecialchars($outerHTML);
        }

        /**
         * Returns and / or not changes existing tag
         * 
         * @param string Optional new tag value to replace previous one 
         * 
         * @return string New or existing tag of element
         */
        public function tag($tag = Null): string {
            $this->tag = $tag?$tag:$this->tag;
            return $this->tag;
        }

        /**
         * Changes ( or not ) and returns curent text of element
         * 
         * @param string $text Optional new text to replace old
         * 
         * @return string $text Of element
         */
        public function text($text = Null): string {
            $this->text = $text?$text:$this->text;
            return $this->text;
        }

        public function innerHtml($html = Null): string {
            $innerHTML = $this->text;
            if ( count($this->childs) > 0 ) {
                foreach ( $this->childs as $child ) {
                    $innerHTML .= $child;
                }
            }
            $this->innerHTML = $html?$html:$innerHTML;
            return $this->innerHTML;
        }

        public function outerHtml($html = Null): string {
            $outerHTML = $this->innerHtml();
            if ( count($this->childs) > 0 ) {
                foreach ( $this->childs as $child ) {
                    $outerHTML .= $child;
                }
            }
            $outerHTML = sprintf("<%s>{$outerHTML}</%s>", $this->openTag(), $this->tag);
            $this->outerHTML = $html?$html:$outerHTML;
            return $this->outerHTML;
        }

        public function childs($childs = Null): array {
            if ( $childs == Null ) { return $this->childs; };
            if ( !is_array($childs) ) { throw new ErrorException(message: "Childs must be an array", line: "136", filename: "HTMLObject.php"); }
            foreach( $childs as $object ) {
                if ( !$object instanceof HTMLObject) { throw new ErrorException(message: "Child must be a HTMLObject instance", line: "138", filename: "HTMLObject.php"); }
            }
            return $this->childs;
        }

        public function append($child, int $id = Null): array | HTMLObject {
            if ( !is_array( $child) ) { $childs = array($child); }
            else { $childs = $child; }
            if ( $id === Null || $id > count($this->childs) ) {
                $this->childs = array_merge($this->childs, $childs);
            } elseif ( $id == 0 ) {
                $this->childs = array_merge($childs, $this->childs);
            } else {
                print_r(array_slice(array: $this->childs, offset: $id));
                $this->childs = array_merge(array_slice(array: $this->childs, offset: 0, length: $id), $childs, array_slice(array: $this->childs, offset:$id));
            }
            return $child;
        }

        public function remove($id = Null) {
            $id = $id?$id:count($this->childs)-1;
            array_splice($this->childs, $id, 1);
        }
    }

    /**
     * HTML Objects's attributes handler class
     *
     * Processes element's attributes
     * Available PUBLIC functions:
     *  - @see all ( Returns array with all attributes )
     *  - @see add ( Adds array of new attributes to existing one )
     *  - @see get ( Returns an attribute of given name )
     *  - @see set ( Changes already set attributes to new values )
     *  - @see remove ( Removes attributes by their names )
     * 
    */
    class Attributes {
        protected array $attributes;

        /**
         * Creates Attribute object. Sets Attributes param to array.
         * 
         * @return void returns instance of Attributes object
        */ 
        function __construct() {
            $this->attributes = array();
        }
        /**
         * Returns all attributes of an element
         * 
         * @return array List of attributes $name => $parameter
        */ 
        public function all(): array {
            return $this->attributes;
        }

        /**
         * Adds array of new attributes to already exisiting list 
         * 
         * @param array $attributes List of attributes in pairs $name => $value
         * 
         * @return array $attributes Passed attributes   
         */
        public function add( array $attributes ): array {
            $this->attributes = array_merge($this->attributes, $attributes);
            return $attributes;
        }

        /**
         * Gets values of provided names of parameters
         * 
         * @param array $names Names of attributes that will be returned
         * 
         * @return array $attributes Attribute pairs of names
         */
        public function get(array $names ): array {
            $attributes = array();
            foreach ( $names as $name ) {
                $attributes[$name] = $this->attributes[$name];
            }
            return $attributes;
        }

        /**
         * Changes already existing attributes to given values
         * 
         * @param array $attributes Names and new values of attributes
         * 
         * @return void Returns void, what shoud it be?
         */
        public function set( array $attributes ): void {
            foreach($attributes as $name => $value) {
                $this->attributes[$name] = $value;
            }
        }

        /**
         * Removes given names ( and values ) from attributes
         * 
         * @param array $names Names of attributes that will removed
         * 
         * @return array $attributes Attribute pairs of names
         */
        public function remove ( array $attributes ): array {
            $spliced = array();
            foreach ( $attributes as $attribute ) {
                $spliced[] = array_splice($this->attributes, array_search($attribute,array_keys($this->attributes)), 1);
            }
            return $spliced;
        }
    }

     /**
     * CSS use multi-purpose class
     *
     * Handles multiple tasks in topic of CSS class and rules processing
     * Available PUBLIC functions:
     *  - @see fetch ( Select columns);
     *  - @see insert ( Insert one row );
     *  - @see exec ( Execute $sql querry and return result )
     *  - @see exists ( Check if records matching statement exist )
     * 
    */

    class Css {
        /**
         * @var array $classList - List of classes
         * @var array $styles - Styles list
         */
        protected array $classList;
        protected array $styles;

        function __construct($classList = Null) {
            $this->classList = array();
            $this->styles = array();
            if ( $classList ) {
                $this->classList($classList);
            }
        }

        public function classList( array $classes = Null ) : array {
            if ( $classes === Null ) {
                return $this->classList;
            } else {
                $this->classList = $classes;
                return $classes;
            }
        }

        public function addClass(array $condClasses): array {
            $passed = array();
            foreach ( $condClasses as $condition => $class) {
                if ( $condition) { $this->classList[] = $class; $passed[] = $class; }
            }
            return $passed;
        }

        public function styles(array $styles = Null): array {
            if ( $styles === Null ) {
                return $this->styles;
            } else {
                $this->styles = $styles;
                return $styles;
            }
        }

        public function addStyle( array $condStyles ): array {
            $passed = array();
            foreach ( $condStyles as $condition => $style) {
                if ( $condition) { 
                    foreach ( $style as $name => $value ) {
                        $this->styles[$name] = $value; 
                        $passed[] = $style; 
                    }
                }
            }
            return $passed;
        }

        public function changeStyle( array $style ): void {
            foreach ( $style as $name => $value) {
                $this->styles[$name] = $value;
            }
        }

    }

    /*
    spl_autoload_register(function ($class_name) {
        include $class_name . '.php';
        });

    */