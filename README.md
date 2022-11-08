# Icon support for Silverstripe taxonomy terms

Upload an icon, assign a CSS class name or a filename to a taxonomy term

## Configuration

Site configuration by your developer/site owner can be done as follows:

```yaml
---
Name: nswdpc-taxonomy-icon
---
SilverStripe\Taxonomy\TaxonomyTerm:
  is_upload: true
  is_css: true
  is_filename: true
  filename_path: 'relative/path/to/icons'
```

The order of preference is upload, css, filename e.g. if is_upload and is_filename are true, only the upload field will be presented.

In the administration screen a CMS editor can:

1. Upload an icon OR
1. Enter a CSS class name OR
1. Enter a filename for an icon, which will be relative to the configured icon file path

## Requirements

The recommended way of installing this module is via [composer](https://getcomposer.org/download/)

```
composer require nswdpc/silverstripe-taxonomy-icons
```

## License

[BSD-3-Clause](./LICENSE.md)

## Documentation

* [Documentation](./docs/en/001_index.md)


## Maintainers

+ [dpcdigital@NSWDPC:~$](https://dpc.nsw.gov.au)

> Add additional maintainers here and/or include [authors in composer](https://getcomposer.org/doc/04-schema.md#authors)

## Bugtracker

We welcome bug reports, pull requests and feature requests on the Github Issue tracker for this project.

Please review the [code of conduct](./code-of-conduct.md) prior to opening a new issue.

## Security

If you have found a security issue with this module, please email digital[@]dpc.nsw.gov.au in the first instance, detailing your findings.

## Development and contribution

If you would like to make contributions to the module please ensure you raise a pull request and discuss with the module maintainers.

Please review the [code of conduct](./code-of-conduct.md) prior to completing a pull request.
