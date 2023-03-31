# ContentDroplets

## Installation
Execute

    composer require hallowelt/contentdroplets dev-REL1_35
within MediaWiki root or add `hallowelt/contentdroplets` to the
`composer.json` file of your project

## Activation
Add

    wfLoadExtension( 'ContentDroplets' );
to your `LocalSettings.php` or the appropriate `settings.d/` file.