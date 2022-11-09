<?php

namespace NSWDPC\Taxonomy\Tests;

use NSWDPC\Taxonomy\TaxonomyIconExtension;
use SilverStripe\Core\Config\Config;
use SilverStripe\Dev\SapphireTest;
use SilverStripe\Taxonomy\TaxonomyTerm;
use SilverStripe\View\Requirements;
use SilverStripe\Assets\Image;
use SilverStripe\AssetAdmin\Forms\UploadField;
use SilverStripe\Forms\TextField;

/**
 * Test features provided by the TaxonomyIconExtension
 * @author James
 */
class TaxonomyIconExtensionTest extends SapphireTest {

    /**
     * @inheritdoc
     */
    protected $usesDatabase = true;

    /**
     * @inheritdoc
     */
    protected static $fixture_file = './TaxonomyIconExtensionTest.yml';

    /**
     * Assert icon handling
     */
    public function testTaxonomyIconIsUpload() {

        Config::modify()->set( TaxonomyTerm::class, 'is_upload', true);
        Config::modify()->set( TaxonomyTerm::class, 'is_css', false);
        Config::modify()->set( TaxonomyTerm::class, 'is_filename', false);

        $term = $this->objFromFixture( TaxonomyTerm::class, 'is_upload');

        $iconUploadField = $term->getIconUploadField();
        $this->assertNotEmpty($iconUploadField);
        $this->assertInstanceOf( UploadField::class, $iconUploadField );

        $cmsFields = $term->getCmsFields();
        $dataField = $cmsFields->dataFieldByName('TaxonomyIcon');

        $this->assertNotEmpty($dataField);
        $this->assertInstanceOf( UploadField::class, $dataField );
    }

    /**
     * Assert CSS handling
     */
    public function testTaxonomyIconIsCss() {

        Config::modify()->set( TaxonomyTerm::class, 'is_upload', false);
        Config::modify()->set( TaxonomyTerm::class, 'is_css', true);
        Config::modify()->set( TaxonomyTerm::class, 'is_filename', false);

        $term = $this->objFromFixture( TaxonomyTerm::class, 'is_css');

        $cssField = $term->getIconCssClassField();
        $this->assertNotEmpty($cssField);
        $this->assertInstanceOf( TextField::class, $cssField );

        $cmsFields = $term->getCmsFields();
        $dataField = $cmsFields->dataFieldByName('TaxonomyIconCssClass');

        $this->assertNotEmpty($dataField);
        $this->assertInstanceOf( TextField::class, $dataField );
    }

    /**
     * Assert Upload handling
     */
    public function testTaxonomyIconIsFilename() {

        Config::modify()->set( TaxonomyTerm::class, 'is_upload', false);
        Config::modify()->set( TaxonomyTerm::class, 'is_css', false);
        Config::modify()->set( TaxonomyTerm::class, 'is_filename', true);
        Config::modify()->set( TaxonomyTerm::class, 'filename_path', 'some/relative/filename');

        $term = $this->objFromFixture( TaxonomyTerm::class, 'is_filename');

        $filenameField = $term->getIconFilenameField();
        $this->assertNotEmpty($filenameField);
        $this->assertInstanceOf( TextField::class, $filenameField );

        $cmsFields = $term->getCmsFields();
        $dataField = $cmsFields->dataFieldByName('TaxonomyIconFileName');

        $this->assertNotEmpty($dataField);
        $this->assertInstanceOf( TextField::class, $dataField );

        $iconPath = $term->getIconPath();

        $this->assertStringContainsString( 'some/relative/filename/' . $term->TaxonomyIconFileName, $iconPath );

    }
}
