<?php

namespace NSWDPC\Taxonomy\Extensions;

use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Control\Director;
use SilverStripe\Forms\FieldList;
use SilverStripe\Forms\TextField;
use SilverStripe\ORM\DataExtension;
use SilverStripe\Taxonomy\TaxonomyTerm;

/**
 * Decorate {@link SilverStripe\Taxonomy\TaxonomyTerm} with an upload field,
 * a file name or a CSS class, field selection depends on configuration
 * @author James
 * @property ?string $TaxonomyIconFileName
 * @property ?string $TaxonomyIconCssClass
 * @property int $TaxonomyIconID
 * @method \SilverStripe\Assets\Image TaxonomyIcon()
 * @extends \SilverStripe\ORM\DataExtension<(\SilverStripe\Taxonomy\TaxonomyTerm & static)>
 */
class TaxonomyIconExtension extends DataExtension
{
    /**
     * @inheritdoc
     */
    private static array $db = [
        'TaxonomyIconFileName' => 'Varchar(255)',
        'TaxonomyIconCssClass' => 'Varchar(255)',
    ];

    /**
     * @inheritdoc
     */
    private static array $has_one = [
        'TaxonomyIcon' => Image::class,
    ];

    /**
     * @inheritdoc
     * Mark ownership of TaxonomyTerm.TaxonomyIcon
     */
    private static array $owns = [
        'TaxonomyIcon'
    ];

    /**
     * @inheritdoc
     */
    #[\Override]
    public function updateSummaryFields(&$fields)
    {
        if (!is_array($fields)) {
            return;
        }

        if ($this->getOwner()->config()->get('is_upload')) {
            $fields = array_merge([ 'TaxonomyIcon.CMSThumbnail' => 'Icon'], $fields);
        } elseif ($this->getOwner()->config()->get('is_css')) {
            $fields = array_merge($fields, [ 'TaxonomyIconCssClass' => 'Icon']);
        } elseif ($this->getOwner()->config()->get('is_filename') && $this->getOwner()->config()->get('filename_path')) {
            $fields = array_merge($fields, [ 'TaxonomyIconFileName' => 'Icon']);
        }
    }

    /**
     * @inheritdoc
     */
    public function updateSearchableFields(array &$fields)
    {
        unset($fields['TaxonomyIcon.CMSThumbnail']);
        unset($fields['TaxonomyIconCssClass']);
        unset($fields['TaxonomyIconFileName']);
    }

    /**
     * @inheritdoc
     */
    public function updateCMSFields(FieldList $fields)
    {

        $fields->removeByName([
            'TaxonomyIconFileName','TaxonomyIconCssClass','TaxonomyIcon'
        ]);

        if ($field = $this->getOwner()->getIconUploadField()) {
            $fields->addFieldToTab('Root.Main', $field);
        } elseif ($field = $this->getOwner()->getIconCssClassField()) {
            $fields->addFieldToTab('Root.Main', $field);
        } elseif ($field = $this->getOwner()->getIconFilenameField()) {
            $description = _t(
                self::class . ".ICON_FILE_PATH_LOCATION",
                "<span><span>The current icon location is</span> <code>{path}</code></span>",
                [
                    'path' => $this->getOwner()->config()->get('filename_path')
                ]
            );
            $field->setDescription($description);
            $fields->addFieldToTab('Root.Main', $field);
        }
    }

    public function getIconUploadField(): ?UploadField
    {
        $field = null;
        if ($this->getOwner()->config()->get('is_upload')) {
            $field = UploadField::create(
                'TaxonomyIcon',
                _t(
                    self::class . ".ICON_UPLOAD",
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

    public function getIconFilenameField(): ?TextField
    {
        $field = null;
        if ($this->getOwner()->config()->get('is_filename') && $this->getOwner()->config()->get('filename_path')) {
            $field = TextField::create(
                'TaxonomyIconFileName',
                _t(
                    self::class . ".ICON_NAME",
                    "Enter an icon file name e.g accessible.svg"
                )
            );
        }

        return $field;
    }

    public function getIconCssClassField(): ?TextField
    {
        $field = null;
        if ($this->getOwner()->config()->get('is_css')) {
            $field = TextField::create(
                'TaxonomyIconCssClass',
                _t(
                    self::class . ".ICON_NAME",
                    "Icon CSS class name or file name e.g 'icon-accessible'"
                )
            );
        }

        return $field;
    }

    /**
     * Return the icon absolute path. It will be either the path to the upload or the path to the file name
     */
    public function getIconPath(): string
    {
        $path = "";
        if ($this->getOwner()->config()->get('is_upload')) {
            $icon = $this->getOwner()->TaxonomyIcon();
            if ($icon && $icon->exists()) {
                $path = $icon->AbsoluteLink();
            }
        } elseif ($this->getOwner()->config()->get('is_filename')
            && $this->getOwner()->config()->get('filename_path')) {
            $path = trim((string) $this->getOwner()->config()->get('filename_path'), "/")
                    . "/"
                    . $this->getOwner()->TaxonomyIconFileName;
            $path = Director::absoluteURL($path);
        }

        return $path;
    }

}
