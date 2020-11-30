<?php

namespace NSWDPC\Taxonomy;

use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\CompositeField;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Taxonomy\TaxonomyTerm;

/**
 * Decorate {@link SilverStripe\Taxonomy\TaxonomyTerm} with an upload field,
 * a file name or a CSS class, field selection depends on configuration
 * @author James
 */
class TaxonomyIconExtension extends DataExtension {

    private static $db = [
        'TaxonomyIconFileName' => 'Varchar(255)',
        'TaxonomyIconCssClass' => 'Varchar(255)',
    ];

    private static $has_one = [
        'TaxonomyIcon' => Image::class,
    ];

    /**
     * Mark ownership of TaxonomyTerm.TaxonomyIcon
    */
    private static $owns = [
        'TaxonomyIcon'
    ];

    public function updateSummaryFields(&$fields) {
        if(!is_array($fields)) {
            return;
        }
        if($this->owner->config()->get('is_upload')) {
            $fields = array_merge([ 'TaxonomyIcon.CMSThumbnail' => 'Icon'], $fields);
        } else if( $this->owner->config()->get('is_css')) {
            $fields = array_merge($fields, [ 'TaxonomyIconCssClass' => 'Icon']);
        } else if($this->owner->config()->get('is_filename') && $this->owner->config()->get('filename_path')) {
            $fields = array_merge($fields, [ 'TaxonomyIconFileName' => 'Icon']);
        }
    }

    public function updateCMSFields(FieldList $fields)
    {

        $fields->removeByName([
            'TaxonomyIconFileName','TaxonomyIconCssClass','TaxonomyIcon'
        ]);

        if($field = $this->owner->getIconUploadField()) {
            $fields->addFieldsToTab('Root.Main', $field);
        } else if($field = $this->owner->getIconCssClassField()) {
            $fields->addFieldsToTab('Root.Main', $field);
        } else if($field = $this->owner->getIconFilenameField()) {
            $description = _t(
                __CLASS__ . ".ICON_FILE_PATH_LOCATION",
                "<span><span>The current icon location is</span> <code>{path}</code></span>",
                [
                    'path' => $this->owner->config()->get('filename_path')
                ]
            );
            $field->setDescription($description);
            $fields->addFieldsToTab('Root.Main', $field);
        }
    }

    /**
     * @return null|UploadField
     */
    public function getIconUploadField() {
        $field = null;
        if($this->owner->config()->get('is_upload')) {
            $field = UploadField::create(
                'TaxonomyIcon',
                _t(
                    __CLASS__ . ".ICON_UPLOAD",
                    "Upload a png or webp icon"
                )
            )->setAllowedExtensions(['png','webp'])
            ->setFolderName('taxonomies')
            ->setIsMultiUpload(false)
            ->setAllowedMaxFileNumber(1)
            ->setAttachEnabled(false);
        }
        return $field;
    }

    /**
     * @return null|TextField
     */
    public function getIconFilenameField() {
        $field = null;
        if( $this->owner->config()->get('is_filename') && $this->owner->config()->get('filename_path') ) {
            $field = TextField::create(
                'TaxonomyIconFileName',
                _t(
                    __CLASS__ . ".ICON_NAME",
                    "Enter an icon file name e.g accessible.svg"
                )
            );
        }
        return $field;
    }

    /**
     * @return null|TextField
     */
    public function getIconCssClassField() {
        $field = null;
        if( $this->owner->config()->get('is_css')) {
            $field = TextField::create(
                'TaxonomyIconCssClass',
                _t(
                    __CLASS__ . ".ICON_NAME",
                    "Icon CSS class name or file name e.g 'icon-accessible'"
                )
            );
        }
        return $field;
    }

    /**
     * Return the icon absolute path. It will be either the path to the upload or the path to the file name
     * @return string
     */
    public function getIconPath() : string {
        $path = "";
        if($this->owner->config()->get('is_upload')) {
            $icon = $this->TaxonomyIcon();
            if($icon && $icon->exists()) {
                $path = $icon->AbsoluteLink();
            }
        } else if($this->owner->config()->get('is_filename')
            && $this->owner->config()->get('filename_path')) {
            $path = trim($this->owner->config()->get('filename_path'). "/")
                    . "/"
                    . $this->TaxonomyIconFileName;
            $path = Director::absoluteURL($path);
        }
        return $path;
    }

}
