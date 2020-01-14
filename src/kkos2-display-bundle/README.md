Os2Display custom bundle for KFF
===

This bundle contains the custom slide types for KFF's Os2Display installations. It uses [Os2Display Slide Tools](https://github.com/reload/os2display-slide-tools) as a foundation for all the slides. Read the docs in that library too.

# Development
This code can run in [Os2Display develompent setup](https://github.com/kkos2/os2display-infrastructure), but as of now, all front-end things that need to built is run with the developer's local machine. It would be nice to get this in a container sometime in the future.

You need Yarn installed locally for building the front end.

## Building the JS and CSS files
Run `yarn build` to compile or `yarn watch` to watch the files.

Make sure you read the comments in the angular.yml-file about how to minimize errors between prod and dev environments.

To ensure some degree of code reuse, the gulfile compiles all needed libraries into _every slide's_ CSS and JS files. The Os2Display framework does not have a way to include more than one file JS and one CSS file pr. slide type. Please note that the code can be loaded more than once on the page because we do it this way. Make sure you encapsulate code in JS and use CSS selectors that are specific enough where that is needed.
