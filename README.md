# itk-os2display developer admin App

This is a symfony project that contains the bundles we work on at ITK.

Run

```sh
scripts/install_bundles.sh
```

to set up repositories in `composer.json`.

Use

```sh
scripts/install_bundles.sh --dev
```

to clone bundles to local storage (`../bundles`) and symlink from `vendor`.

To install assets, run

```sh
app/console assets:install --symlink
```
