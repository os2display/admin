Os2Display admin for Københavns Kommune
===

# Bundles
## kkos2-display-bundle
This bundle contains slides specific to Københavns Kommune. [More documentation](src/kkos2-display-bundle/README.md) can be found there.

## Tagging for a release
See the documentation in [kkos2/os2display-infrastructure](https://github.com/kkos2/os2display-infrastructure/blob/master/documentation/building-a-release.md) for how to create releases.

## Visual regression test with backstop.js
For now, this will need to be run locally on your machine. It will be moved to a container eventually.
In the root of this checkout run: `yarn install` to install the test-runner.

Until we have a way to fetch data for the dev environments, you will have to setup slides, channels, and screen yourself for testing. In `backstop.json` you will need to edit the url to the tests too. Make sure you use the "offentligt tilgængelig" url so no interaction is required for the screen.

To test, run `yarn backstop test`. It will fail the first time because there is no reference. Just run `yarn backstop approve` and then run the test again.
