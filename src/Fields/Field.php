<?php

/**
 * @copyright (c) Simian B.V. 2019
 * @version       1.0.0
 */

namespace Simianbv\JsonSchema\Fields;

use Illuminate\Support\Str;
use Simianbv\JsonSchema\Contracts\FieldInterface;
use Simianbv\JsonSchema\Contracts\HasRelationInterface;

/**
 * @class   Field
 * @package Simianbv\JsonSchema\Fields
 */
abstract class Field implements FieldInterface
{
    /**
     * @var string
     */
    protected $field_name = '';
    /**
     * @var string
     */
    protected $field_label = '';
    /**
     * @var string
     */
    protected $field_title = '';
    /**
     * @var string
     */
    protected $field_placeholder;
    /**
     * @var string
     */
    protected $field_type = 'string';
    /**
     * @var string
     */
    protected $field_format = null;
    /**
     * @var string|integer|double
     */
    protected $field_minimum = null;
    /**
     * @var string|integer|double
     */
    protected $field_maximum = null;
    /**
     * @var array
     */
    protected $base_classes = [];
    /**
     * @var array
     */
    protected $meta = [];
    /**
     * @var mixed|null
     */
    protected $field_default_value = null;
    /**
     * @var mixed|null
     */
    protected $field_value = null;
    /**
     * @var bool
     */
    protected $visibility_browse = true;
    /**
     * @var bool
     */
    protected $visibility_read = true;
    /**
     * @var bool
     */
    protected $visibility_edit = true;
    /**
     * @var bool
     */
    protected $visibility_create = true;
    /**
     * @var bool
     */
    protected $is_required = false;
    /**
     * @var bool
     */
    protected $is_nullable = false;
    /**
     * @var string
     */
    protected $component_type = null;
    /**
     * @var string
     */
    protected $original_component_type = null;
    /**
     * @var array
     */
    protected $additional_properties = [];
    /**
     * @var array
     */
    protected $options = [];
    /**
     * @var array
     */
    protected $items = [];

    /**
     * Returns the input element type.
     * @return string
     */
    abstract protected function getFieldType(): string;

    /**
     * Get the name of the Field object.
     * @return string
     */
    abstract protected function getComponentType(): string;

    /**
     * Set up any default values you want to override by default.
     * @return void
     */
    protected function setDefaultAttributes()
    {
    }

    /**
     * Allow additional properties to be added to the returned data array.
     * @return array
     */
    public function provideAdditionalProperties(): array
    {
        return [];
    }

    /**
     * @param array $props
     *
     * @return Field
     */
    public function setAdditionalProperties(array $props): Field
    {
        $this->additional_properties = $props;
        return $this;
    }

    /**
     * Works with the `provideAdditionalProperties` method you can override and add your own attributes.
     *
     * @return array
     * @see provideAdditionalProperties
     */
    public function getAdditionalProperties(): array
    {
        $options = array_merge($this->provideAdditionalProperties(), $this->additional_properties);
        return $options;
    }

    /**
     * The start of the implementation, this bootstraps the class to define the Component or FIELD.
     *
     * @param string      $name
     * @param string|null $title
     * @param array       $attributes
     *
     * @return $this
     */
    public function make(string $name, string $title = null, array $attributes = [])
    {
        $this->initialize($name, $title, $attributes);
        return $this;
    }

    /**
     * @param string      $name
     * @param string|null $title
     * @param array       $attributes
     *
     * @return Field
     */
    public function initialize(string $name, string $title = null, array $attributes = []): Field
    {
        $this->nullable();
        $this->name($name);
        $this->setDefaultAttributes();
        $this->field_type = $this->getFieldType();
        $this->component_type = $this->getComponentType();


        if ($title) {
            $this->title($title);
        }
        if (!empty($attributes)) {
            $this->attributes($attributes);
        }

        return $this;
    }

    /**
     * @param string $name
     *
     * @return $this
     */
    public function name(string $name): Field
    {
        $name = preg_replace("/[^\da-zA-Z\-\_]/", '', $name);
        $this->field_name = $name;
        return $this;
    }

    /**
     * @param array $attributes
     *
     * @return Field
     */
    public function attributes(array $attributes): Field
    {
        foreach ($attributes as $attr => $key) {
            $this->meta[$attr] = $key;
        }
        return $this;
    }

    /**
     * @param string $field_type
     *
     * @return Field
     */
    protected function setFieldType(string $field_type): Field
    {
        $this->field_type = $field_type;
        if ($field_type == 'array') {
            $this->setAdditionalProperties(['items' => ['type' => 'object'], 'options']);
        }
        return $this;
    }

    /**
     * Set up a default value for the field to use, leave blank to not fill in a default value selected.
     *
     * @param mixed $value
     *
     * @return Field
     */
    public function defaultValue($value): Field
    {
        $this->field_default_value = $value;
        return $this;
    }

    /**
     * Set up the value for the component to use. It looks a bit like the default value, however
     * this one will override that value to be used as the value returned on that COMPONENT.
     *
     * @param mixed $value
     *
     * @return Field
     */
    public function setValue($value): Field
    {
        $this->field_value = $value;
        return $this;
    }

    /**
     * Set up the title to be displayed as the label.
     *
     * @param string $title
     *
     * @return Field
     */
    public function title(string $title): Field
    {
        $this->field_title = $title;
        return $this;
    }

    /**
     * A small utility, in case you want to add an icon to the component and its size.
     *
     * @param string $icon
     * @param string $size
     *
     * @return Field
     */
    public function icon(string $icon, $size = 'sm'): Field
    {
        $this->attributes(['icon' => $icon, 'icon_size' => $size]);
        return $this;
    }

    /**
     * Set up the size of the component field.
     *
     * @param mixed $size
     *
     * @return Field
     */
    public function size($size): Field
    {
        $this->attributes(['size' => $size]);
        return $this;
    }

    /**
     * Set the field size to small. A utility helper to quickly resize to smaller fields.
     *
     * @return Field
     * @see size
     */
    public function small(): Field
    {
        return $this->size('small');
    }

    /**
     * Set the field to medium. A utility helper to quickly resize to medium field.
     * @return Field
     * @see size
     */
    public function medium(): Field
    {
        return $this->size('medium');
    }

    /**
     * Set the field to medium. A utility helper to quickly resize to large field.
     * @return Field
     * @see size
     */
    public function large(): Field
    {
        return $this->size('large');
    }

    /**
     * Set up the placeholder text to be displayed on the component field.
     *
     * @param string $placeholder
     *
     * @return Field
     */
    public function placeholder(string $placeholder): Field
    {
        $this->field_placeholder = $placeholder;
        return $this;
    }

    /**
     * A helper to notify the component that this can be a nullable or null value. It also means
     * that this field cannot be a REQUIRED field.
     *
     * @return Field
     */
    public function nullable(): Field
    {
        $this->is_nullable = true;
        $this->is_required = false;
        return $this;
    }

    /**
     * A utility to add additional meta properties, the attribute $attribute will be used to set the key in the
     * META array, the $value can be anything you want, ie. an array, string, bool or integer.
     *
     * @param string $attribute
     * @param mixed  $value
     *
     * @return Field
     */
    public function with(string $attribute, $value = null): Field
    {
        if ($value === null && is_array($attribute)) {
            foreach ($attribute as $key => $value) {
                $this->with($key, $value);
            }
        }
        if (!is_array($attribute) && !is_object($attribute) && is_string($attribute) && $value) {
            $this->meta[$attribute] = $value;
        }
        return $this;
    }

    /**
     * Defines that this field is required and must adhere to the HTML5 spec regarding REQUIRED.
     *
     * @return Field
     */
    public function required(): Field
    {
        $this->is_required = true;
        $this->is_nullable = false;
        return $this;
    }

    /**
     * Whether this field should be hidden on the index route.
     *
     * @return Field
     */
    public function hidden(): Field
    {
        $this->visibility_browse = false;
        $this->visibility_read = false;
        $this->visibility_edit = false;
        $this->visibility_create = false;
        return $this;
    }

    /**
     * Whether this field should be hidden on the index route.
     *
     * @return Field
     */
    public function show(): Field
    {
        $this->visibility_browse = true;
        $this->visibility_read = true;
        $this->visibility_edit = true;
        $this->visibility_create = true;
        return $this;
    }

    /**
     * Whether this field should be hidden on the index route.
     *
     * @return Field
     */
    public function hideFromIndex(): Field
    {
        $this->visibility_browse = false;
        return $this;
    }

    /**
     * Whether this field should be hidden on the detail route.
     *
     * @return Field
     */
    public function hideFromDetail(): Field
    {
        $this->visibility_read = false;
        return $this;
    }

    /**
     * Whether this field should be hidden on the creation form route.
     *
     * @return Field
     */
    public function hideWhenCreating(): Field
    {
        $this->visibility_create = false;
        return $this;
    }

    /**
     * Whether this field should be hidden on the updte form route.
     *
     * @return Field
     */
    public function hideWhenUpdating(): Field
    {
        $this->visibility_edit = false;
        return $this;
    }

    /**
     * Show only on the index.
     *
     * @return Field
     */
    public function onlyOnIndex(): Field
    {
        $this->visibility_browse = true;
        $this->visibility_read = false;
        $this->visibility_edit = false;
        $this->visibility_create = false;
        return $this;
    }

    /**
     * Show only on the detail.
     *
     * @return Field
     */
    public function onlyOnDetail(): Field
    {
        $this->visibility_browse = false;
        $this->visibility_read = true;
        $this->visibility_edit = false;
        $this->visibility_create = false;
        return $this;
    }

    /**
     * Show only on the form views.
     *
     * @return Field
     */
    public function onlyOnForms(): Field
    {
        $this->visibility_browse = false;
        $this->visibility_read = false;
        $this->visibility_edit = true;
        $this->visibility_create = true;
        return $this;
    }

    /**
     * Show only on the index and detail.
     *
     * @return Field
     */
    public function exceptOnForms(): Field
    {
        $this->visibility_browse = true;
        $this->visibility_read = true;
        $this->visibility_edit = false;
        $this->visibility_create = false;
        return $this;
    }

    /**
     * Define your own visibility, requires an array containing the following view states: browse, read, create, edit
     *
     * @param array $visibility
     *
     * @return Field
     * @example
     *      $field->setVisibility([
     *          'browse' => true,
     *          'read' => false,
     *          'edit' => true,
     *          'create' => true
     *      ]);
     */
    public function setVisibility(array $visibility): Field
    {
        if (isset($visibility['browse'])) {
            $this->visibility_browse = $visibility['browse'];
        }
        if (isset($visibility['read'])) {
            $this->visibility_read = $visibility['read'];
        }
        if (isset($visibility['edit'])) {
            $this->visibility_edit = $visibility['edit'];
        }
        if (isset($visibility['create'])) {
            $this->visibility_create = $visibility['create'];
        }

        return $this;
    }

    /**
     * Returns the view state of the field.
     *
     * @return array
     */
    public function getVisibility(): array
    {
        return [
            'browse' => $this->visibility_browse,
            'read' => $this->visibility_read,
            'edit' => $this->visibility_edit,
            'create' => $this->visibility_create,
        ];
    }

    /**
     * Set up the base class for this component field.
     *
     * @param string|string[] $class
     *
     * @return $this
     */
    public function baseClass($class): Field
    {
        $this->base_classes = [];
        $this->addBaseClass($class);
        return $this;
    }

    /**
     * Set up the field format.
     *
     * @param $format
     *
     * @return $this
     */
    public function setFieldFormat($format): Field
    {
        $this->field_format = $format;
        return $this;
    }

    public function affix(string $affix)
    {
        $this->attributes(['affix' => $affix]);
        return $this;
    }

    public function suffix(string $suffix)
    {
        $this->attributes(['suffix' => $suffix]);
        return $this;
    }

    /**
     * Add an additional base class to the meta properties class list.
     *
     * @param string|string[] $class
     *
     * @return $this
     */
    public function addBaseClass($class): Field
    {
        if (is_array($class)) {
            foreach ($class as $base_class) {
                $this->base_classes[] = $base_class;
            }
        } else {
            if (is_string($class)) {
                $this->base_classes[] = $class;
            }
        }
        return $this;
    }

    /**
     * If the original component type is overridden, this method can help set the original component type,
     * useful to check if there's no implementation for the new component type and have a fallback component type.
     *
     * @param string $type
     *
     * @return $this
     */
    public function setOriginalComponentType(string $type): Field
    {
        $this->original_component_type = $type;
        return $this;
    }

    /**
     * Override this function if you want additional validation steps to add to your component.
     *
     * @return bool
     */
    public function validate()
    {
        return true;
    }

    /**
     * Checks if the component is a required field.
     *
     * @return bool
     */
    public function isRequired(): bool
    {
        return $this->is_required;
    }

    /**
     * Returns true if the field is nullable.
     *
     * @return bool
     */
    public function isNullable(): bool
    {
        return $this->is_nullable;
    }

    /**
     * Check if there's a _set_ value, if so, returns true.
     *
     * @return bool
     */
    public function hasValue(): bool
    {
        return $this->field_value !== null;
    }

    /**
     * Returns true if there's a default value set.
     *
     * @return bool
     */
    public function hasDefaultValue(): bool
    {
        return $this->field_default_value !== null;
    }

    /**
     * Returns true if there's a minimum value set.
     *
     * @return bool
     */
    public function hasMinimum(): bool
    {
        return $this->field_minimum !== null;
    }

    /**
     * Returns true if there's a maximum value set.
     *
     * @return bool
     */
    public function hasMaximum(): bool
    {
        return $this->field_maximum !== null;
    }

    /**
     * Returns true if there's a base class defined.
     *
     * @return bool
     */
    public function hasBaseClasses(): bool
    {
        return !empty($this->base_classes);
    }

    /**
     * Returns the name of the component field.
     *
     * @return string
     */
    public function getName()
    {
        return $this->field_name;
    }

    /**
     * Get the title of the component field.
     *
     * @return string|null
     */
    public function getTitle()
    {
        return $this->field_title ?? $this->field_title;
    }

    /**
     * Add a meta property that defines that the component should have a rounded property.
     * @return $this
     */
    public function rounded()
    {
        $this->attributes(['rounded' => true]);
        return $this;
    }

    /**
     * Returns the _set_ value.
     *
     * @return mixed|null
     */
    public function getValue()
    {
        return $this->field_value;
    }

    /**
     * Returns the default value.
     *
     * @return mixed|null
     */
    public function getDefaultValue()
    {
        return $this->field_default_value;
    }

    /**
     * Returns the minimum value.
     *
     * @return float|int|string
     */
    public function getMinimum()
    {
        return $this->field_minimum;
    }

    /**
     * Returns the maximum value.
     *
     * @return float|int|string
     */
    public function getMaximum()
    {
        return $this->field_maximum;
    }

    /**
     * Get the unique identifier for this component.
     *
     * @return string
     */
    public function getIdentifier()
    {
        return $this->field_name;
    }

    /**
     * Returns an array of all the base classes.
     *
     * @return array
     */
    public function getBaseClasses(): array
    {
        return $this->base_classes;
    }

    /**
     * @return string
     */
    public function getFieldFormat()
    {
        return $this->field_format;
    }

    /**
     * Returns the entire meta array.
     *
     * @return array
     */
    public function getMetaAttributes(): array
    {
        return $this->meta;
    }

    /**
     * Build up the component, as defined by the JSON_SCHEMA definition.
     *
     * @return array
     */
    public function toArray(): array
    {
        if ($this->validate()) {
            $data = [
                '$id' => '#/properties/' . $this->getName(),
                'type' => $this->field_type ?? $this->getFieldType(),
                'title' => $this->getTitle(),
                'name' => $this->getName(),
                'component_type' => Str::slug($this->getComponentType()),
                'default_value' => $this->field_default_value,
                'attrs' => [
                    'label' => $this->field_label,
                    'placeholder' => $this->field_placeholder,
                ],
                'value' => $this->field_value,
                'nullable' => $this->is_nullable,
                'meta' => [],
            ];

            if ($this->getFieldFormat()) {
                $data['format'] = $this->getFieldFormat();
            }

            if ($this->field_minimum) {
                $data['minimum'] = $this->field_minimum;
            }
            if ($this->field_maximum) {
                $data['maximum'] = $this->field_maximum;
            }


            if (!empty($this->meta)) {
                $data['meta'] = $this->meta;
            }

            $data['meta']['visibility'] = [
                'browse' => $this->visibility_browse,
                'read' => $this->visibility_read,
                'edit' => $this->visibility_edit,
                'create' => $this->visibility_create,
            ];

            if (!empty($this->base_classes)) {
                $data['base_classes'] = $this->base_classes;
            }

            if ($this instanceof HasRelationInterface && $this->hasRelation()) {
                $data['meta']['relation'] = $this->relationToArray();
            }

            if ($params = $this->getAdditionalProperties()) {
                foreach ($params as $key => $param) {
                    if (is_array($param)) {
                        $obj = $param;
                        $param = $key;
                        if (isset($obj['type']) && $obj['type'] == 'object') {
                            $data[$param] = (object)$this->{$param};
                        }
                    } else {
                        if (isset($this->{$param})) {
                            $data[$param] = $this->{$param};
                        }
                    }
                }
            }
            return $data;
        } else {
            return [];
            // throw new Exception("Invalid data provided for this field. Make sure the Field is a valid Type and/or Relation");
        }
    }


    /**
     * @param int|null $flags
     *
     * @return false|string
     */
    public function toJson(int $flags = null)
    {
        return json_encode($this->toArray(), $flags);
    }

    /**
     * @return false|string
     */
    public function __toString()
    {
        return $this->toJson();
    }
}
