---
title: Icon
---

Available icons for use in project.

Selected icons from Google Material Icons, in svg format and are available in three sizes.

Icons fetched from https://design.google.com/icons/
Downloaded icons are located @ design/assets/icons
The file name should reflect the name used @ https://design.google.com/icons/ to help locating the original source of the file
The svg code reveals the size fetched from https://design.google.com/icons/
Add the file name to the sass array defined in scss/helpers/_vars.scss to enable sass to locate the file
Icons with changed colors should be named "|original-name|-alt.svg", "-alt"-files do not need a seperate definition in sass array, instead use the available state class described below.

#### States

is-tiny (18px)
is-small (24px)
is-medium (36x)
is-large (48px)
is-inverted (Uses the "-alt.svg" file)