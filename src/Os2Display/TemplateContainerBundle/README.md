# Purpose of this bundle

The purpose of this bundle is to ease the transition to the Bundle based
structure.

If the only extension from the base installation is som templates, that
previously was placed in web/templates/, these files can now be cloned into
this bundle's Resources/public/templates/ folder. Any files in this folder
will be ignored by git.

To make the templates work in this new folder, the paths in the .json and 
other files need to reference the new location in:

`bundles/os2displaytemplatecontainerbundle/templates/..`

